<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

(function () {
    if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['dashboard'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['dashboard'] = [];
    }

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dashboard'] = [
        'widgets' => [
            \Pixelant\Dashboard\Widget\RssWidget::IDENTIFIER => [
                'name' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.rsswidget.name',
                'description' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.rsswidget.description',
                'icon' => 'widget-rss',
                'class' => \Pixelant\Dashboard\Widget\RssWidget::class,
                'template' => 'EXT:dashboard/Resources/Private/Templates/DashboardWidgets/RssWidget.html',
                'defaultWidth' => '3',
                'defaultHeight' => '5',
                'minWidth' => '3',
            ],
            \Pixelant\Dashboard\Widget\ActionWidget::IDENTIFIER => [
                'name' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.actionwidget.name',
                'description' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.actionwidget.description',
                'class' => \Pixelant\Dashboard\Widget\ActionWidget::class,
                'template' => 'EXT:dashboard/Resources/Private/Templates/DashboardWidgets/ActionWidget.html',
                'defaultWidth' => '3',
                'defaultHeight' => '5',
                'minWidth' => '3',
            ],
            \Pixelant\Dashboard\Widget\SysNewsWidget::IDENTIFIER => [
                'name' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.sysnewswidget.name',
                'description' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.sysnewswidget.description',
                'class' => \Pixelant\Dashboard\Widget\SysNewsWidget::class,
                'template' => 'EXT:dashboard/Resources/Private/Templates/DashboardWidgets/SysNewsWidget.html',
                'defaultWidth' => '3',
                'defaultHeight' => '5',
                'minWidth' => '3',
            ],
            \Pixelant\Dashboard\Widget\IframeWidget::IDENTIFIER => [
                'name' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.iframe.name',
                'description' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.iframe.description',
                'class' => \Pixelant\Dashboard\Widget\IframeWidget::class,
                'template' => 'EXT:dashboard/Resources/Private/Templates/DashboardWidgets/IframeWidget.html',
                'defaultWidth' => '12',
                'defaultHeight' => '6',
                'minWidth' => '3',
            ],
        ],
    ];

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'dashboard-widget-default',
        \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        ['source' => 'EXT:dashboard/Resources/Public/Icons/dashboardWidget.png']
    );
    $iconRegistry->registerIcon(
        'widget-rss',
        \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        ['source' => 'EXT:dashboard/Resources/Public/Icons/RssWidget.png']
    );
})();
