<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {

    /**
     * Registers a Backend Module
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'TYPO3\CMS.' . $_EXTKEY,
        'system',     // Make module a submodule of 'user'
        'dashboardmod1',    // Submodule key
        '',                        // Position
        array(
            'Dashboard' => 'list',
        ),
        array(
            'access' => 'user,group',
            'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_dashboardmod1.xlf',
        )
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Dashboard');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_dashboard_domain_model_dashboard', 'EXT:dashboard/Resources/Private/Language/locallang_csh_tx_dashboard_domain_model_dashboard.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dashboard_domain_model_dashboard');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_dashboard_domain_model_dashboardwidgetsettings', 'EXT:dashboard/Resources/Private/Language/locallang_csh_tx_dashboard_domain_model_dashboardwidgetsettings.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dashboard_domain_model_dashboardwidgetsettings');

$GLOBALS['TCA']['tx_dashboard_domain_model_dashboardwidgetsettings']['ctrl']['requestUpdate'] = 'widget_identifier';

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dashboard'] = array(
    'widgets' => array(
        '41385600' => array(
            'name' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:41385600.name',
            'description' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:41385600.description',
            'icon' => 'EXT:dashboard/Resources/Public/Icons/RssWidget.png',
            'class' => 'TYPO3\\CMS\\Dashboard\\DashboardWidgets\\RssWidget',
            'template' => 'EXT:dashboard/Resources/Private/Templates/DashboardWidgets/RssWidget.html',
            'size' => '1x2',
        ),
        '1439441923' => array(
            'name' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:1439441923.name',
            'description' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:1439441923.description',
            'icon' => 'EXT:dashboard/Resources/Public/Icons/ActionWidget.png',
            'class' => 'TYPO3\\CMS\\Dashboard\\DashboardWidgets\\ActionWidget',
            'template' => 'EXT:dashboard/Resources/Private/Templates/DashboardWidgets/ActionWidget.html',
            'size' => '1x1',
        ),
        '1439446997' => array(
            'name' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:1439446997.name',
            'description' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:1439446997.description',
            'icon' => 'EXT:dashboard/Resources/Public/Icons/SysNewsWidget.png',
            'class' => 'TYPO3\\CMS\\Dashboard\\DashboardWidgets\\SysNewsWidget',
            'template' => 'EXT:dashboard/Resources/Private/Templates/DashboardWidgets/SysNewsWidget.html',
            'size' => '1x2',
        ),
        \TYPO3\CMS\Dashboard\DashboardWidgets\IframeWidget::IDENTIFIER => array(
            'name' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.iframe.name',
            'description' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.iframe.description',
            'icon' => 'EXT:dashboard/Resources/Public/Icons/frameWidget.png',
            'class' => 'TYPO3\\CMS\\Dashboard\\DashboardWidgets\\IframeWidget',
            'template' => 'EXT:dashboard/Resources/Private/Templates/DashboardWidgets/IframeWidget.html',
            'size' => '3x3',
        )
    )
);
