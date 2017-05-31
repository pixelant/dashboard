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
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Form\Service\TranslationService;

/**
 * DashboardController
 */
class DashboardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var array
     */
    protected $dashboardSettings;

    /**
     * Default View Container
     *
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

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

        $configurationManager = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(ConfigurationManagerInterface::class);
        $this->dashboardSettings = $configurationManager
            ->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'dashboard', 'dashboardmod1');
    }

    /**
     * action index
     *
     * @return void
     */
    public function indexAction()
    {
        $this->registerDocheaderButtons();
        $this->view->getModuleTemplate()->setModuleName($this->request->getPluginName() . '_' . $this->request->getControllerName());
        $this->view->getModuleTemplate()->setFlashMessageQueue($this->controllerContext->getFlashMessageQueue());

        $this->getPageRenderer()->addRequireJsConfiguration(
            [
                'paths' => [
                    'lodash' => '../typo3conf/ext/dashboard/Resources/Public/JavaScript/Backend/lodash.min',
                    'gridstack' => '../typo3conf/ext/dashboard/Resources/Public/JavaScript/Backend/gridstack.min',
                ],
                'shim' => [
                    'deps' => ['lodash', 'jquery'],
                    'gridstack' => ['exports' => 'gridstack'],
                ],
            ]
        );
        $this->getPageRenderer()->addRequireJsConfiguration(
            [
                'paths' => [
                    'jquery-ui' => '../typo3conf/ext/dashboard/Resources/Public/JavaScript/Contrib/jquery-ui',
                    'gridstackjqueryui' => '../typo3conf/ext/dashboard/Resources/Public/JavaScript/Backend/gridstack.jQueryUI.min',
                ],
                'shim' => [
                    'deps' => ['lodash', 'jquery', 'jquery-ui', 'gridstack'],
                    'gridstackjqueryui' => ['exports' => 'gridstackjqueryui'],
                ],
            ]
        );

        // $this->view->assign('forms', $this->getAvailableFormDefinitions());
        $this->view->assign('stylesheets', $this->resolveResourcePaths($this->dashboardSettings['settings']['stylesheets']));
        $this->view->assign('dynamicRequireJsModules', $this->dashboardSettings['settings']['dynamicRequireJsModules']);
        $this->view->assign('dashboardAppInitialData', $this->getDashboardAppInitialData());
        if (!empty($this->dashboardSettings['settings']['javaScriptTranslationFile'])) {
            $this->getPageRenderer()->addInlineLanguageLabelFile($this->dashboardSettings['settings']['javaScriptTranslationFile']);
        }
    }

    /**
     * action change
     *
     * @return string
     */
    public function changeAction()
    {
        $getVars = $this->request->getArguments();

        return 'sent string was: ' . $getVars['items'];
    }

    /**
     * action change
     *
     * @return string
     */
    public function createAction()
    {
        $getVars = $this->request->getArguments();

        // return 'Would create dashboard with name: ' . $getVars['dashboardName'];
        return $this->controllerContext->getUriBuilder()->uriFor('index', ['id' => 1]);
    }

    /**
     * Registers the Icons into the docheader
     *
     * @throws \InvalidArgumentException
     */
    protected function registerDocheaderButtons()
    {
        /** @var ButtonBar $buttonBar */
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $currentRequest = $this->request;
        $moduleName = $currentRequest->getPluginName();
        $getVars = $this->request->getArguments();

        $mayMakeShortcut = $this->getBackendUser()->mayMakeShortcut();
        if ($mayMakeShortcut) {
            $extensionName = $currentRequest->getControllerExtensionName();
            if (count($getVars) === 0) {
                $modulePrefix = strtolower('tx_' . $extensionName . '_' . $moduleName);
                $getVars = ['id', 'M', $modulePrefix];
            }

            $shortcutButton = $buttonBar->makeShortcutButton()
                ->setModuleName($moduleName)
                ->setDisplayName($this->getLanguageService()->sL('LLL:EXT:form/Resources/Private/Language/Database.xlf:module.shortcut_name'))
                ->setGetVariables($getVars);
            $buttonBar->addButton($shortcutButton);
        }

        if (isset($getVars['action']) && $getVars['action'] !== 'index') {
            $backButton = $buttonBar->makeLinkButton()
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:lang/Resources/Private/Language/locallang_common.xlf:back'))
                ->setIcon($this->view->getModuleTemplate()->getIconFactory()->getIcon('actions-view-go-up', Icon::SIZE_SMALL))
                ->setHref($this->getModuleUrl($moduleName));
            $buttonBar->addButton($backButton);
        } else {
            // New dashboard button
            $addFormButton = $buttonBar->makeLinkButton()
                ->setDataAttributes(['identifier' => 'newDashboard'])
                ->setHref('#')
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardManager.create_new_dashboard'))
                ->setIcon($this->view->getModuleTemplate()->getIconFactory()->getIcon('actions-document-new', Icon::SIZE_SMALL));
            $buttonBar->addButton($addFormButton, ButtonBar::BUTTON_POSITION_LEFT);
        }
    }

    /**
     * Returns the json encoded data which is used by the dashboard
     * JavaScript app.
     *
     * @return string
     */
    protected function getDashboardAppInitialData(): string
    {
        $dashboardAppInitialData = [
            'selectablePrototypesConfiguration' => $this->dashboardSettings['settings']['selectablePrototypesConfiguration'],
            'endpoints' => [
                'create' => $this->controllerContext->getUriBuilder()->uriFor('create'),
                'change' => $this->controllerContext->getUriBuilder()->uriFor('change'),
                'index' => $this->controllerContext->getUriBuilder()->uriFor('index')
            ],
        ];

        $dashboardAppInitialData = ArrayUtility::reIndexNumericArrayKeysRecursive($dashboardAppInitialData);
        $dashboardAppInitialData = TranslationService::getInstance()->translateValuesRecursive(
            $dashboardAppInitialData,
            $this->dashboardSettings['settings']['translationFile']
        );
        return json_encode($dashboardAppInitialData);
    }

    /**
     * Convert arrays with EXT: resource paths to web paths
     *
     * Input:
     * [
     *   100 => 'EXT:form/Resources/Public/Css/form.css'
     * ]
     *
     * Output:
     *
     * [
     *   0 => 'typo3/sysext/form/Resources/Public/Css/form.css'
     * ]
     *
     * @param array $resourcePaths
     * @return array
     */
    protected function resolveResourcePaths(array $resourcePaths): array
    {
        $return = [];
        foreach ($resourcePaths as $resourcePath) {
            $fullResourcePath = GeneralUtility::getFileAbsFileName($resourcePath);
            $resourcePath = PathUtility::getAbsoluteWebPath($fullResourcePath);
            if (empty($resourcePath)) {
                continue;
            }
            $return[] = $resourcePath;
        }

        return $return;
    }

    /**
     * Returns the current BE user.
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Returns the Language Service
     *
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * Returns the page renderer
     *
     * @return PageRenderer
     */
    protected function getPageRenderer(): PageRenderer
    {
        return GeneralUtility::makeInstance(PageRenderer::class);
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
        // $pageRenderer->loadRequireJsModule('TYPO3/CMS/Dashboard/GridList');

        $this->view->assignMultiple([
            'includeCssFiles' => $this->getIncludeCssFilesFromSettings(),
            'includeJsFiles' => $this->getIncludeJsFilesFromSettings()
        ]);

        if (isset($GLOBALS['BE_USER']->user['uid'])) {
            $beUserUid = (int)$GLOBALS['BE_USER']->user['uid'];

            $dashboards = $this->dashboardRepository->findByBeuser($beUserUid);

            if ($dashboards->count() == 0) {
                // Create a new dashboard if none exists (use a "template" when first dashboard is created?)
                $beUserRepository = $this->objectManager->get(\TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository::class);
                $beUser = $beUserRepository->findByUid($beUserUid);
                if ($beUser !== null) {
                    $defaultDashboardName = strlen(trim($beUser->getRealName())) > 0 ? $beUser->getRealName() : $beUser->getUserName();
                    $newDashboard = $this->objectManager->get(\TYPO3\CMS\Dashboard\Domain\Model\Dashboard::class);
                    $newDashboard->setTitle($defaultDashboardName . ' dashboard');
                    $newDashboard->setBeuser($beUser);
                    $newDashboard->addDashboardWidgetSetting($this->getExampleWidgetSettingObject());
                    $this->dashboardRepository->add($newDashboard);
                    $this->objectManager
                         ->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class)
                         ->persistAll();
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
        if (!empty($this->settings['includeCssFiles']) && is_array($this->settings['includeCssFiles'])) {
            foreach ($this->settings['includeCssFiles'] as $key => $path) {
                $fileAbsFileName = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($path, true, true);
                $relativePathTo = \TYPO3\CMS\Core\Utility\PathUtility::getRelativePathTo($fileAbsFileName);
                $includeCssFiles[$key] = rtrim($relativePathTo, '/');
            }
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
        $newDashboardWidgetSetting = $this->objectManager->get(
            \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings::class
        );
        $newDashboardWidgetSetting->setTitle('TYPO3 News');
        $newDashboardWidgetSetting->setWidgetIdentifier('41385600');
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
