<?php
namespace Pixelant\Dashboard\Controller;

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

use Pixelant\Dashboard\Domain\Model\Widget;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use Pixelant\Dashboard\Service\TranslationService;
use TYPO3\CMS\Lang\LanguageService;

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
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * Default View Container
     *
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * dashboardRepository
     *
     * @var \Pixelant\Dashboard\Domain\Repository\DashboardRepository
     * @inject
     */
    protected $dashboardRepository = null;

    /**
     * dashboard
     *
     * @var \Pixelant\Dashboard\Domain\Model\Dashboard
     */
    protected $dashboard = null;

    /**
     * @var BackendUserAuthentication
     */
    private $backendUserAuthentication;

    public function __construct(BackendUserAuthentication $backendUserAuthentication = null)
    {
        parent::__construct();

        $this->backendUserAuthentication = $backendUserAuthentication ?: $GLOBALS['BE_USER'];
    }

    /**
     * Initialize action
     */
    public function initializeAction()
    {
        $dashboardSettings = $this->objectManager
            ->get(ConfigurationManagerInterface::class)
            ->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'dashboard', 'dashboardmod1');

        $userTsConfigSettings = GeneralUtility::removeDotsFromTS((array)$this->backendUserAuthentication->getTSConfigProp('tx_dashboard.settings'));
        $this->dashboardSettings = array_replace($dashboardSettings, $userTsConfigSettings);
        $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        if (empty($this->dashboardSettings['persistence']['storagePid']) || strpos($this->dashboardSettings['persistence']['storagePid'], ',') !== false) {
            throw new \UnexpectedValueException('tx_dashboard.persistence.storagePid must be set in Dashboard settings or user tsconfig and must be a single page id', 1511102864);
        }
        $querySettings->setStoragePageIds([$this->dashboardSettings['persistence']['storagePid']]);
        $this->dashboardRepository->setDefaultQuerySettings($querySettings);

        $dashBoardUid = null;
        if ($this->request->hasArgument('id')) {
            $dashBoardUid = (int)$this->request->getArgument('id');
        }
        if ($dashBoardUid) {
            $this->dashboard = $this->dashboardRepository->findByUid($dashBoardUid);
            if ($this->dashboard->getBeUser()->getUid() !== (int)$this->backendUserAuthentication->user['uid']) {
                throw new \Exception('Access denied to selected dashboard', 1);
            }
        } else {
            $this->dashboard = $this->dashboardRepository->findOneByBeuser($this->backendUserAuthentication->user['uid']);
        }
    }

    /**
     * action index
     *
     * @return void
     */
    public function indexAction()
    {
        $this->registerDocheaderMenu();
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

        $this->view->assign('stylesheets', $this->resolveResourcePaths($this->dashboardSettings['settings']['stylesheets']));
        $this->view->assign('dynamicRequireJsModules', $this->dashboardSettings['settings']['dynamicRequireJsModules']);
        $this->view->assign('dashboardAppInitialData', $this->getDashboardAppInitialData());
        if (!empty($this->dashboardSettings['settings']['javaScriptTranslationFile'])) {
            $this->getPageRenderer()->addInlineLanguageLabelFile($this->dashboardSettings['settings']['javaScriptTranslationFile']);
        }
        $this->view->assign('dashboard', $this->dashboard);
    }

    public function initializeChangeAction()
    {
        $configuration = $this->arguments->getArgument('items')
            ->getPropertyMappingConfiguration();

        $configuration->allowAllProperties();
        $configuration->forProperty('*')->setTypeConverterOption(PersistentObjectConverter::class, PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED, true);
        $configuration->forProperty('*.*')->allowAllProperties();
    }

    /**
     * action change
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Dashboard\Domain\Model\Widget> $items
     * @return string
     */
    public function changeAction($items)
    {
        foreach ($items as $widget) {
            $this->dashboard->updateWidget($widget);
        }
        $this->dashboardRepository->update($this->dashboard);
        return 'Updated widget positions';
    }

    /**
     * action createWidget
     *
     * @return string
     */
    public function createWidgetAction()
    {
        $getVars = $this->request->getArguments();
        if ($this->dashboard) {
            $storagePid = $this->dashboardSettings['persistence']['storagePid'];
            $widgetType = $getVars['widgetType'];
            $widgetSettings = $this->getWidgetSettings($widgetType);
            $width = $widgetSettings['defaultWidth'] ?? 3;
            $height = $widgetSettings['defaultHeight'] ?? 5;
            $overrideVals = '&overrideVals[tx_dashboard_domain_model_widget][dashboard]=' . $this->dashboard->getUid();
            $overrideVals .= '&overrideVals[tx_dashboard_domain_model_widget][widget_identifier]=' . $getVars['widgetType'];
            $overrideVals .= '&overrideVals[tx_dashboard_domain_model_widget][width]=' . $width;
            $overrideVals .= '&overrideVals[tx_dashboard_domain_model_widget][height]=' . $height;
            $overrideVals .= '&overrideVals[tx_dashboard_domain_model_widget][y]=' . $this->dashboard->findNextAvailableWidgetPosition();
            $overrideVals .= '&overrideVals[tx_dashboard_domain_model_widget][x]=0';
            $params = '&edit[tx_dashboard_domain_model_widget][' . $storagePid . ']=new' . $overrideVals;

            $returnUrl = urlencode($this->controllerContext->getUriBuilder()->uriFor('index', ['id' => $this->dashboard->getUid()]));
            return BackendUtility::getModuleUrl('record_edit') . $params . '&returnUrl=' . $returnUrl;
        }
        return 'widgetType: ' . $getVars['widgetType'];
    }

    /**
     * action change
     *
     * @return string
     */
    public function createAction()
    {
        $getVars = $this->request->getArguments();

        if (isset($this->backendUserAuthentication->user['uid'])) {
            $beUserUid = (int)$this->backendUserAuthentication->user['uid'];

            $beUserRepository = $this->objectManager->get(
                \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository::class
            );
            $beUser = $beUserRepository->findByUid($beUserUid);
            if ($beUser !== null) {
                $newDashboard = $this->objectManager->get(\Pixelant\Dashboard\Domain\Model\Dashboard::class);
                $newDashboard->setTitle($getVars['dashboardName']);
                $newDashboard->setPid($this->dashboardSettings['persistence']['storagePid']);
                $newDashboard->setBeuser($beUser);
                $this->dashboardRepository->add($newDashboard);
                // We need to call persistAll here to get the uid of the just created dashboard
                $this->objectManager->get(PersistenceManagerInterface::class)->persistAll();
                return $this->controllerContext->getUriBuilder()->uriFor('index', ['id' => $newDashboard->getUid()]);
            }
        }
        return false;
    }

    /**
     * action renderWidget
     *
     * @param int $widgetId
     * @return string
     */
    public function renderWidgetAction(int $widgetId)
    {
        $errorTitle = $this
            ->getLanguageService()
            ->sL('LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:error.title');
        $content = '';
        if (!empty($widgetId) && (int)$widgetId > 0) {
            $widget = $this->dashboard->getWidgetById($widgetId);
            if ($widget) {
                $widgetSettings = $widget->getSettings();
                $widgetControllerClassName = $widgetSettings['class'];
                if (class_exists($widgetControllerClassName)) {
                    $widgetController = $this->objectManager->get($widgetControllerClassName);
                    try {
                        return $widgetController->render($widget);
                    } catch (\Exception $e) {
                        $localizedError = $this
                            ->getLanguageService()
                            ->sL('LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:error.' . $e->getCode());

                        $localizedError = strlen($localizedError) > 0 ? $localizedError : $e->getMessage();

                        $content = $this->getHtmlErrorMessage($errorTitle, $localizedError);
                    }
                } else {
                    $content = $this->getHtmlErrorMessage($errorTitle, 'Class : ' . $widgetControllerClassName . ' could not be found!');
                }
            } else {
                $content = $this->getHtmlErrorMessage($errorTitle, 'Widget [' . $widgetId . '] could not be found!');
            }
        }
        return $content;
    }

    /**
     * Registers the menu of dashboards into the docheader
     *
     * @throws \InvalidArgumentException
     */
    protected function registerDocheaderMenu()
    {
        // Dashboards
        $dashboards = $this->dashboardRepository->findByBeuser((int)$this->backendUserAuthentication->user['uid']);
        if (!empty($dashboards)) {
            $dashboardMenu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
            $dashboardMenu->setIdentifier('_dashboardSelector');
            $uriBuilder = $this->controllerContext->getUriBuilder();
            foreach ($dashboards as $index => $dashboard) {
                $uriBuilder->reset();
                $menuItem = $dashboardMenu->makeMenuItem()
                    ->setTitle($dashboard->getTitle())
                    ->setHref(
                        $uriBuilder->uriFor('index', ['id' => $dashboard->getUid()])
                    );
                if ($dashboard->getUid() === $this->dashboard->getUid()) {
                    $menuItem->setActive(true);
                }
                $dashboardMenu->addMenuItem($menuItem);
            }
            $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($dashboardMenu);
        }
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

        // New dashboard button
        $newDashboardButton = $buttonBar->makeLinkButton()
            ->setDataAttributes(['identifier' => 'newDashboard'])
            ->setHref('#')
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardManager.create_new_dashboard'))
            ->setIcon($this->view->getModuleTemplate()->getIconFactory()->getIcon('actions-document-new', Icon::SIZE_SMALL))
            ->setShowLabelText(true);
        $buttonBar->addButton($newDashboardButton, ButtonBar::BUTTON_POSITION_LEFT, 1);

        if (is_object($this->dashboard)) {
            // Edit dashboard button
            $newDashboardButton = $buttonBar->makeLinkButton()
                ->setDataAttributes(['identifier' => 'editDashboard'])
                ->setHref('#')
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardManager.edit_dashboard'))
                ->setIcon($this->view->getModuleTemplate()->getIconFactory()->getIcon('actions-document-open', Icon::SIZE_SMALL))
                ->setShowLabelText(true);
            $buttonBar->addButton($newDashboardButton, ButtonBar::BUTTON_POSITION_LEFT, 1);

            // new widget button
            $newWidgetButton = $buttonBar->makeLinkButton()
                ->setDataAttributes(
                    [
                        'identifier' => 'newWidget',
                        'dashboardid' => $this->dashboard->getUid(),
                    ]
                )
                ->setHref('#')
                ->setTitle(
                    $this->getLanguageService()->sL(
                        'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardManager.create_new_dashboard_widget_setting'
                    )
                )
                ->setIcon(
                    $this->view->getModuleTemplate()->getIconFactory()->getIcon(
                        'actions-document-new',
                        Icon::SIZE_SMALL
                    )
                )
                ->setShowLabelText(true);
            $buttonBar->addButton($newWidgetButton, ButtonBar::BUTTON_POSITION_LEFT, 10);
        }
    }

    /**
     * Returns the json encoded data which is used by the dashboard
     * JavaScript app.
     *
     * @return array
     */
    protected function getDashboardAppInitialData(): array
    {
        $uriBuilder = $this->controllerContext->getUriBuilder();
        $uriBuilder->reset();
        $uriArguments = $this->dashboard ? ['id' => $this->dashboard->getUid()] : [];
        $dashboardAppInitialData = [
            'selectableWidgetTypesConfiguration' => $this->getSelectableWidgets(),
            'endpoints' => [
                'create' => $uriBuilder->uriFor('create'),
                'createWidget' => $uriBuilder->uriFor('createWidget', $uriArguments),
                'change' => $uriBuilder->uriFor('change', $uriArguments),
                'index' => $uriBuilder->uriFor('index', $uriArguments),
                'renderWidget' => $uriBuilder->uriFor('renderWidget', $uriArguments),
                'editDashboard' => $this->getEditDashboardEndpoint(),
            ],
        ];

        if ($this->dashboard) {
            $dashboardAppInitialData['dashboard'] = [
                'id' => $this->dashboard->getUid(),
                'title' => $this->dashboard->getTitle(),
            ];
        }

        $dashboardAppInitialData = ArrayUtility::reIndexNumericArrayKeysRecursive($dashboardAppInitialData);
        $dashboardAppInitialData = TranslationService::getInstance()->translateValuesRecursive(
            $dashboardAppInitialData,
            $this->dashboardSettings['settings']['translationFile']
        );

        return $dashboardAppInitialData;
    }

    /**
     * Returns array of items configured for widget_identifier
     *
     * @return array
     */
    protected function getSelectableWidgets(): array
    {
        $items = $GLOBALS['TCA']['tx_dashboard_domain_model_widget']['columns']['widget_identifier']['config']['items'];
        unset($items['0']);
        if (!empty($items) && is_array($items)) {
            foreach ($items as $index => $values) {
                $items[$index]['0'] = $this->getLanguageService()->sL($values['0']);
            }
        }
        return $items;
    }

    /**
     * Returns array of item configured for widget_identifier
     *
     * @param string $widgetIdentifier
     *
     * @return array
     */
    protected function getWidgetSettings(string $widgetIdentifier): array
    {
        $widget = new Widget($widgetIdentifier);
        return $widget->getSettings();
    }

    /**
     * Returns edit url for this dashboard
     *
     * @return string
     */
    protected function getEditDashboardEndpoint()
    {
        $editDashboardEndpoint = '';
        if ($this->dashboard) {
            $params = '&edit[tx_dashboard_domain_model_dashboard][' . $this->dashboard->getUid() . ']=edit';
            $returnUrl = urlencode($this->controllerContext->getUriBuilder()->uriFor('index', ['id' => $this->dashboard->getUid()]));
            $editDashboardEndpoint = BackendUtility::getModuleUrl('record_edit') . $params . '&returnUrl=' . $returnUrl;
        }
        return $editDashboardEndpoint;
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
     * Returns html "template" for a error message
     *
     * @param string $title
     * @param string $message
     *
     * @return string
     */
    protected function getHtmlErrorMessage($title, $message)
    {
        $content = '<div class="typo3-messages">';
        $content .= '   <div class="alert alert-danger">';
        $content .= '       <div class="media">';
        $content .= '           <div class="media-left">';
        $content .= '               <span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-exclamation fa-stack-1x"></i></span>';
        $content .= '           </div>';
        $content .= '           <div class="media-body">';
        $content .= '               <h4 class="alert-title">' . htmlspecialchars($title) . '</h4>';
        $content .= '               <p class="alert-message">' . htmlspecialchars($message) . '</p>';
        $content .= '           </div>';
        $content .= '       </div>';
        $content .= '   </div>';
        $content .= '</div>';
        return $content;
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
}
