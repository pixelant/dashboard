<?php
namespace TYPO3\CMS\Dashboard\DashboardWidgets;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

use TYPO3\CMS\Backend\Utility\BackendUtility;
#use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Dashboard\DashboardWidgetInterface;

class ActionWidget extends AbstractWidget implements DashboardWidgetInterface
{

    /**
     * Limit, If set, it will limit the results in the list.
     *
     * @var integer
     */
    protected $limit = 0;

    /**
     * Renders content
     * @param \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSetting
     * @return string the rendered content
     */
    public function render($dashboardWidgetSetting = null)
    {
        $this->initialize($dashboardWidgetSetting);
        $content = $this->generateContent();
        return $content;
    }

    /**
     * Initializes settings from flexform
     * @param \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSetting
     * @return void
     */
    private function initialize($dashboardWidgetSetting = null)
    {
        $flexformSettings = $this->getFlexFormSettings($dashboardWidgetSetting);
        $this->limit = (int)$flexformSettings['settings']['limit'];
        $this->widget = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dashboard']['widgets'][$dashboardWidgetSetting->getWidgetIdentifier()];
    }

    /**
     * Generates the content
     * @return string
     */
    private function generateContent()
    {
        $widgetTemplateName = $this->widget['template'];
        $actionView = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $template = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($widgetTemplateName);
        $actionView->setTemplatePathAndFilename($template);
        $actionEntries = $this->getActionEntries();
        $actionView->assign('actionEntries', $actionEntries);
        return $actionView->render();
    }

    /**
     * Gets the entries for the action list
     *
     * @return array Array of action menu entries
     */
    protected function getActionEntries()
    {
        $backendUser = $this->getBackendUser();
        $databaseConnection = $this->getDatabaseConnection();
        $actions = array();
        if ($backendUser->isAdmin()) {
            $queryResource = $databaseConnection->exec_SELECTquery('*', 'sys_action', 'pid = 0 AND hidden=0', '', 'sys_action.sorting', $this->getLimit());
        } else {
            $groupList = 0;
            if ($backendUser->groupList) {
                $groupList = $backendUser->groupList;
            }
            $queryResource = $databaseConnection->exec_SELECT_mm_query(
                'sys_action.*',
                'sys_action',
                'sys_action_asgr_mm',
                'be_groups',
                ' AND be_groups.uid IN (' . $groupList . ') AND sys_action.pid = 0 AND sys_action.hidden = 0',
                'sys_action.uid',
                'sys_action.sorting'
            );
        }

        if ($queryResource) {
            while ($actionRow = $databaseConnection->sql_fetch_assoc($queryResource)) {
                $actions[] = array(
                    'title' => $actionRow['title'],
                    'action' => BackendUtility::getModuleUrl('user_task') . '&SET[mode]=tasks&SET[function]=sys_action.TYPO3\\CMS\\SysAction\\ActionTask&show=' . $actionRow['uid'],
                    'description' => $actionRow['description'],
                    't3_tables' => $actionRow['t3_tables'],
                    //'icon' => IconUtility::getSpriteIconForRecord('sys_action', $actionRow)
                    'icon' => ''
                );
            }
            $databaseConnection->sql_free_result($queryResource);
        }
        return $actions;
    }

    /**
     * Returns the current BE user.
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Return DatabaseConnection
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Return limit for query
     *
     * @return string
     */
    protected function getLimit()
    {
        return (int)$this->limit > 0 ? (int)$this->limit : '';
    }
}
