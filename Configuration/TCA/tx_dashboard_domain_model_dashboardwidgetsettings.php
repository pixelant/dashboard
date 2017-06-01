<?php
return [
    'ctrl' => [
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
        'enablecolumns' => [],
        'searchFields' => 'title,widget_identifier,state,position,settings_flexform,',
        'iconfile' => 'EXT:dashboard/Resources/Public/Icons/tx_dashboard_domain_model_dashboardwidgetsettings.gif'

    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, title, widget_identifier, state, position, settings_flexform, dashboard',
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, title, widget_identifier, state, position, settings_flexform, dashboard, '
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => ''
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1
                    ],
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.default_value',
                        0
                    ],
                ],
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_dashboard_domain_model_dashboardwidgetsettings',
                'foreign_table_where' => 'AND tx_dashboard_domain_model_dashboardwidgetsettings.pid=###CURRENT_PID### AND tx_dashboard_domain_model_dashboardwidgetsettings.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboardwidgetsettings.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'widget_identifier' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboardwidgetsettings.widget_identifier',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '-- Label --',
                        '0'
                    ],
                    [
                        'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.rsswidget.name',
                        \TYPO3\CMS\Dashboard\DashboardWidgets\RssWidget::IDENTIFIER
                    ],
                    [
                        'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.actionwidget.name',
                        \TYPO3\CMS\Dashboard\DashboardWidgets\ActionWidget::IDENTIFIER
                    ],
                    [
                        'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.sysnewswidget.name',
                        \TYPO3\CMS\Dashboard\DashboardWidgets\SysNewsWidget::IDENTIFIER
                    ],
                    [
                        'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.iframe.name',
                        \TYPO3\CMS\Dashboard\DashboardWidgets\IframeWidget::IDENTIFIER
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => '',
                'default' => '',
            ],
        ],
        'state' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboardwidgetsettings.state',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'x' => [
            'exclude' => 1,
            'label' => 'x',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'int'
            ],
        ],
        'y' => [
            'exclude' => 1,
            'label' => 'y',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'int'
            ],
        ],
        'width' => [
            'exclude' => 1,
            'label' => 'width',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'int'
            ],
        ],
        'height' => [
            'exclude' => 1,
            'label' => 'height',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'int'
            ],
        ],
        'settings_flexform' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboardwidgetsettings.settings_flexform',
            'config' => [
                'type' => 'flex',
                'ds_pointerField' => 'widget_identifier',
                'ds' => [
                    \TYPO3\CMS\Dashboard\DashboardWidgets\RssWidget::IDENTIFIER => 'FILE:EXT:dashboard/Configuration/FlexForms/flexform_rsswidget.xml',
                    \TYPO3\CMS\Dashboard\DashboardWidgets\ActionWidget::IDENTIFIER => 'FILE:EXT:dashboard/Configuration/FlexForms/flexform_actionwidget.xml',
                    \TYPO3\CMS\Dashboard\DashboardWidgets\SysNewsWidget::IDENTIFIER => 'FILE:EXT:dashboard/Configuration/FlexForms/flexform_sysnewswidget.xml',
                    \TYPO3\CMS\Dashboard\DashboardWidgets\IframeWidget::IDENTIFIER => 'FILE:EXT:dashboard/Configuration/FlexForms/flexform_iframewidget.xml',
                    'default' => '<T3DataStructure><ROOT><type>array</type><el><empty><TCEforms><label>Please select Widget identifier first</label><config><type>none</type></config></TCEforms></empty></el></ROOT></T3DataStructure>',
                ],
            ],
        ],
        'dashboard' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_dashboard_domain_model_dashboard',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
    ],
];
