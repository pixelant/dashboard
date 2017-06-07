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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Dashboard\DashboardWidgetInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class ActionWidget extends AbstractWidget implements DashboardWidgetInterface
{
    const IDENTIFIER = '1439441923';

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
     * @throws 1910010001
               1477506500
     */
    private function generateContent()
    {
        if (!ExtensionManagementUtility::isLoaded('sys_action')) {
            throw new \Exception("Extension sys_actions is not enabled", 1910010001);
        }
        $actionEntries = [];
        $widgetTemplateName = $this->widget['template'];
        $actionView = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class)
            ->get(StandaloneView::class);

        $template = GeneralUtility::getFileAbsFileName($widgetTemplateName);
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
        $actionEntries = [];
        $backendUser = $this->getBackendUser();

        $queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getQueryBuilderForTable('sys_action');

        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction::class))
            ->add(GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\Query\Restriction\RootLevelRestriction::class, [
                'sys_action'
            ]));

        $queryBuilder
            ->select('sys_action.*')
            ->from('sys_action');

        if (!empty($GLOBALS['TCA']['sys_action']['ctrl']['sortby'])) {
            $queryBuilder->orderBy('sys_action.' . $GLOBALS['TCA']['sys_action']['ctrl']['sortby']);
        }

        if (!$backendUser->isAdmin()) {
            $groupList = $backendUser->groupList ?: '0';

            $queryBuilder
                ->join(
                    'sys_action',
                    'sys_action_asgr_mm',
                    'sys_action_asgr_mm',
                    $queryBuilder->expr()->eq(
                        'sys_action_asgr_mm.uid_local',
                        $queryBuilder->quoteIdentifier('sys_action.uid')
                    )
                )
                ->join(
                    'sys_action_asgr_mm',
                    'be_groups',
                    'be_groups',
                    $queryBuilder->expr()->eq(
                        'sys_action_asgr_mm.uid_foreign',
                        $queryBuilder->quoteIdentifier('be_groups.uid')
                    )
                )
                ->where(
                    $queryBuilder->expr()->in(
                        'be_groups.uid',
                        $queryBuilder->createNamedParameter(
                            GeneralUtility::intExplode(',', $groupList, true),
                            Connection::PARAM_INT_ARRAY
                        )
                    )
                )
                ->groupBy('sys_action.uid');
        }

        $result = $queryBuilder->execute();
        while ($actionRow = $result->fetch()) {
            $actions = [
                'title' => $actionRow['title'],
                'action' => sprintf(
                    '%s&SET[mode]=tasks&SET[function]=sys_action.%s&show=%u',
                    BackendUtility::getModuleUrl('user_task'),
                    ActionTask::class, // @todo: class name string is hand over as url parameter?!
                    $actionRow['uid']
                ),
                'icon' => 'TODO' //IconUtility::getSpriteIconForRecord('sys_action', $actionRow)
            ];
            $actionEntries[] = $actions;
        }

        return $actionEntries;
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
