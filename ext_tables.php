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
$GLOBALS['TCA']['tx_dashboard_domain_model_dashboard'] = array(
    'ctrl' => array(
        'title'    => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboard',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'sortby' => 'sorting',

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'title,description,dashboard_widget_settings,beuser,',
        'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Dashboard.php',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dashboard_domain_model_dashboard.gif'
    ),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_dashboard_domain_model_dashboardwidgetsettings', 'EXT:dashboard/Resources/Private/Language/locallang_csh_tx_dashboard_domain_model_dashboardwidgetsettings.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dashboard_domain_model_dashboardwidgetsettings');
$GLOBALS['TCA']['tx_dashboard_domain_model_dashboardwidgetsettings'] = array(
    'ctrl' => array(
        'title'    => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboardwidgetsettings',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'sortby' => 'sorting',

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(

        ),
        'searchFields' => 'title,widget_identifier,state,position,settings_flexform,',
        'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/DashboardWidgetSettings.php',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dashboard_domain_model_dashboardwidgetsettings.gif'
    ),
);
## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

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
            'size' => '1x1',
        )
    )
);
