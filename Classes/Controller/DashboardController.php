<?php
namespace TYPO3\CMS\Dashboard\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * DashboardController
 */
class DashboardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * dashboardRepository
     *
     * @var \TYPO3\CMS\Dashboard\Domain\Repository\DashboardRepository
     * @inject
     */
    protected $dashboardRepository = null;

    /**
     * Initialize action
     */
    public function initializeAction()
    {
        $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $querySettings->setRespectStoragePage(false);
        $this->dashboardRepository->setDefaultQuerySettings($querySettings);
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {

        /** @var $pageRenderer PageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Dashboard/SortPanels');

        $this->view->assignMultiple([
            'includeCssFiles' => $this->getIncludeCssFilesFromSettings(),
            'includeJsFiles' => $this->getIncludeJsFilesFromSettings()
        ]);

        if (isset($GLOBALS['BE_USER']->user['uid'])) {
            $beUserUid = (int)$GLOBALS['BE_USER']->user['uid'];

            $dashboards = $this->dashboardRepository->findByBeuser($beUserUid);

            if ($dashboards->count() == 0) {
                // Create a new dashboard if none exists (use a "template" when first dashboard is created?)
                $beUserRepository = $this->objectManager->get('TYPO3\\CMS\\Beuser\\Domain\\Repository\\BackendUserRepository');
                $beUser = $beUserRepository->findByUid($beUserUid);
                if ($beUser !== null) {
                    $defaultDashboardName = strlen(trim($beUser->getRealName())) > 0 ? $beUser->getRealName() : $beUser->getUserName();
                    $newDashboard = $this->objectManager->get('TYPO3\\CMS\\Dashboard\\Domain\\Model\\Dashboard');
                    $newDashboard->setTitle($defaultDashboardName . ' dashboard');
                    $newDashboard->setBeuser($beUser);
                    $newDashboard->addDashboardWidgetSetting($this->getExampleWidgetSettingObject());
                    $this->dashboardRepository->add($newDashboard);
                    $persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
                    $persistenceManager->persistAll();
                }
            }

            if ($this->request->hasArgument('dashboardUid')) {
                $dashboardCurrent = $this->dashboardRepository->findByUid($this->request->getArgument('dashboardUid'));
            } else {
                $dashboardCurrent = $this->dashboardRepository->findByBeuser($beUserUid)->getFirst();
            };

            // Get Storage Pid
            $configurationManager = GeneralUtility::makeInstance(ObjectManager::class)->get(ConfigurationManagerInterface::class);
            $configuration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'dashboard', 'dashboardmod1');
            $storagePid = $configuration['persistence']['storagePid'];

            $dashboardWidgets = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dashboard']['widgets'];
            if (is_array($dashboardWidgets) && count($dashboardWidgets) > 0) {
                foreach ($dashboardWidgets as $index => $dashboardWidget) {
                    if ($dashboardCurrent !== null) {
                        $overrideVals = '&overrideVals[tx_dashboard_domain_model_dashboardwidgetsettings][dashboard]=' . $dashboardCurrent->getUid();
                        $overrideVals .= '&overrideVals[tx_dashboard_domain_model_dashboardwidgetsettings][state]=new';
                        $overrideVals .= '&overrideVals[tx_dashboard_domain_model_dashboardwidgetsettings][position]=last';
                        $overrideVals .= '&overrideVals[tx_dashboard_domain_model_dashboardwidgetsettings][widget_identifier]=' . $index;
                        $editOnClick = '&edit[tx_dashboard_domain_model_dashboardwidgetsettings]['.$storagePid.']=new' . $overrideVals;
                        $dashboardWidgets[$index]['addNewLink'] = \TYPO3\CMS\Backend\Utility\BackendUtility::editOnClick($editOnClick);
                        $dashboardWidgets[$index]['widget_identifier'] = $index;
                        if (substr($dashboardWidget['name'], 0, 4) == 'LLL:') {
                            $dashboardWidgets[$index]['name'] =    $GLOBALS['LANG']->sL($dashboardWidget['name']);
                        }
                        if (substr($dashboardWidget['description'], 0, 4) == 'LLL:') {
                            $dashboardWidgets[$index]['description'] =    $GLOBALS['LANG']->sL($dashboardWidget['description']);
                        }
                    }
                }
            }
            $this->view->assign('dashboardWidgets', $dashboardWidgets);

            if ($dashboards->getFirst() !== null) {
                $link = \TYPO3\CMS\Backend\Utility\BackendUtility::editOnClick('&edit[tx_dashboard_domain_model_dashboard][' . $dashboards->getFirst()->getUid() . ']=edit');
                $this->view->assign('link', $link);
            }
        }

        $this->view->assign('dashboards', $dashboards);
        $this->view->assign('dashboardCurrent', $dashboardCurrent);
    }

    /**
     * [getIncludeCssFilesFromSettings Includes css files defined in ts]
     *
     * @return array Array of files
     */
    private function getIncludeCssFilesFromSettings()
    {
        $includeCssFiles = array();
        foreach ($this->settings['includeCssFiles'] as $key => $path) {
            $fileAbsFileName = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($path, true, true);
            $relativePathTo = \TYPO3\CMS\Core\Utility\PathUtility::getRelativePathTo($fileAbsFileName);
            $includeCssFiles[$key] = rtrim($relativePathTo, '/');
        }
        return $includeCssFiles;
    }

    /**
     * [getIncludeJsFilesFromSettings Includes css files defined in ts]
     *
     * @return array Array of files
     */
    private function getIncludeJsFilesFromSettings()
    {
        $includeJsFiles = array();
        foreach ($this->settings['includeJsFiles'] as $key => $path) {
            $fileAbsFileName = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($path, true, true);
            $relativePathTo = \TYPO3\CMS\Core\Utility\PathUtility::getRelativePathTo($fileAbsFileName);
            $includeJsFiles[$key] = rtrim($relativePathTo, '/');
        }
        return $includeJsFiles;
    }

    /**
     * Get a TYPO3 News RSS widget
     * @return \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings Settings for a TYPO3 News RSS widget
     */
    private function getExampleWidgetSettingObject()
    {
        // Create "example" dashboard widget setting
        $newDashboardWidgetSetting = $this->objectManager->get('TYPO3\\CMS\\Dashboard\\Domain\\Model\\DashboardWidgetSettings');
        $newDashboardWidgetSetting->setTitle('TYPO3 News');
        $newDashboardWidgetSetting->setWidgetIdentifier(41385600);
        $newDashboardWidgetSetting->setState('new');
        $newDashboardWidgetSetting->setPosition('1');
        $newDashboardWidgetSetting->setSettingsFlexform('<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
                <T3FlexForms>
                    <data>
                        <sheet index="sDEF">
                            <language index="lDEF">
                                <field index="settings.header">
                                    <value index="vDEF">TYPO3 News</value>
                                </field>
                                <field index="settings.feedUrl">
                                    <value index="vDEF">http://typo3.org/xml-feeds/rss.xml</value>
                                </field>
                                <field index="settings.feedLimit">
                                    <value index="vDEF">10</value>
                                </field>
                                <field index="settings.cacheLifetime">
                                    <value index="vDEF">10</value>
                                </field>
                            </language>
                        </sheet>
                    </data>
                </T3FlexForms>');
        return $newDashboardWidgetSetting;
    }
}
