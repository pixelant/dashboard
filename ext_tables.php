<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

/**
 * Registers a Backend Module
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Pixelant.Dashboard',
    'user',     // Make module a submodule of 'user'
    'dashboardmod1',    // Submodule key
    '',                        // Position
    [
        'Dashboard' => 'index, change, create, createWidget, renderWidget',
    ],
    [
        'access' => 'user,group',
        'icon' => 'EXT:dashboard/Resources/Public/Icons/Extension.png',
        'labels' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_dashboardmod1.xlf',
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_dashboard_domain_model_dashboard', 'EXT:dashboard/Resources/Private/Language/locallang_csh_tx_dashboard_domain_model_dashboard.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dashboard_domain_model_dashboard');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_dashboard_domain_model_widget', 'EXT:dashboard/Resources/Private/Language/locallang_csh_tx_dashboard_domain_model_widget.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dashboard_domain_model_widget');
