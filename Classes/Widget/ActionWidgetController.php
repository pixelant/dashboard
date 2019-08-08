<?php
namespace Pixelant\Dashboard\Widget;

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

use Doctrine\DBAL\Connection;
use Pixelant\Dashboard\Domain\Model\Widget;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\RootLevelRestriction;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ActionWidgetController implements WidgetControllerInterface
{
    const IDENTIFIER = '1439441923';

    /**
     * Limit, If set, it will limit the results in the list.
     *
     * @var int
     */
    protected $limit = 0;

    /**
     * Widget configuration
     *
     * @var array
     */
    protected $widget = [];

    /**
     * Renders content
     *
     * @param Widget $widget
     * @return string the rendered content
     */
    public function render(Widget $widget): string
    {
        $this->initialize($widget);
        return $this->generateContent();
    }

    /**
     * Initializes settings from flexform
     *
     * @param Widget $widget
     * @return void
     */
    private function initialize($widget = null)
    {
        $settings = $widget->getSettings();
        $this->limit = (int)$settings['limit'];
        $this->widget = $settings;
    }

    /**
     * Generates the content
     * @throws \Exception
     * @return string
     */
    private function generateContent()
    {
        if (!ExtensionManagementUtility::isLoaded('sys_action')) {
            throw new \Exception('Extension sys_actions is not enabled', 1910010001);
        }
        $widgetTemplateName = $this->widget['template'];
        $actionView = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class)
            ->get(StandaloneView::class);

        $template = GeneralUtility::getFileAbsFileName($widgetTemplateName);
        $actionView->setTemplatePathAndFilename($template);
        $actionView->assign('actions', $this->getActions());
        $actionView->assign('userTaskLink', BackendUtility::getModuleUrl('user_task'));
        return $actionView->render();
    }

    /**
     * Get all actions of an user. Admins can see any action, all others only those
     * which are allowed in sys_action record itself.
     *
     * @return array Array holding every needed information of a sys_action
     */
    protected function getActions()
    {
        $backendUser = $this->getBackendUser();
        $actionList = [];

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_action');
        $queryBuilder->select('sys_action.*')
            ->from('sys_action');

        if (!empty($GLOBALS['TCA']['sys_action']['ctrl']['sortby'])) {
            $queryBuilder->orderBy('sys_action.' . $GLOBALS['TCA']['sys_action']['ctrl']['sortby']);
        }

        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(RootLevelRestriction::class, ['sys_action']));

        // Editors can only see the actions which are assigned to a usergroup they belong to
        if (!$backendUser->isAdmin()) {
            $groupList = $backendUser->groupList ?: '0';

            $queryBuilder->getRestrictions()
                ->add(GeneralUtility::makeInstance(HiddenRestriction::class));

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

        $queryResult = $queryBuilder->execute();

        while ($actionRow = $queryResult->fetch()) {
            $actionList[] = [
                'uid' => 'actiontask' . $actionRow['uid'],
                'title' => $actionRow['title'],
                'description' => $actionRow['description'],
                'link' => BackendUtility::getModuleUrl('user_task')
                    . '&SET[function]=sys_action.'
                    . \TYPO3\CMS\SysAction\ActionTask::class
                    . '&show='
                    . (int)$actionRow['uid'],
            ];
        }

        return $actionList;
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
