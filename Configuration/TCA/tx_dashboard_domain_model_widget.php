<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_widget',
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
        'requestUpdate' => 'widget_identifier',
        'searchFields' => 'title,widget_identifier,position,settings_flexform,',
        'iconfile' => 'EXT:dashboard/Resources/Public/Icons/tx_dashboard_domain_model_widget.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, title, widget_identifier, position, settings_flexform, dashboard, x, y, width, height',
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, widget_identifier, title, position, settings_flexform, dashboard',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'passthrough',
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
                'foreign_table' => 'tx_dashboard_domain_model_widget',
                'foreign_table_where' => 'AND tx_dashboard_domain_model_widget.pid=###CURRENT_PID### AND tx_dashboard_domain_model_widget.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_widget.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'widget_identifier' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_widget.widget_identifier',
            'config' => [
                'type' => 'select',
                'readOnly' => 1,
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '-- Label --',
                        '0',
                    ],
                    [
                        'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.rsswidget.name',
                        \Pixelant\Dashboard\Widget\RssWidgetController::IDENTIFIER,
                    ],
                    [
                        'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.actionwidget.name',
                        \Pixelant\Dashboard\Widget\ActionWidgetController::IDENTIFIER,
                    ],
                    [
                        'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.sysnewswidget.name',
                        \Pixelant\Dashboard\Widget\SysNewsWidgetController::IDENTIFIER,
                    ],
                    [
                        'LLL:EXT:dashboard/Resources/Private/Language/locallang.xlf:dashboardWidget.iframe.name',
                        \Pixelant\Dashboard\Widget\IframeWidgetController::IDENTIFIER,
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => '',
                'default' => '',
            ],
        ],
        'x' => [
            'exclude' => 1,
            'label' => 'x',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'int',
            ],
        ],
        'y' => [
            'exclude' => 1,
            'label' => 'y',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'int',
            ],
        ],
        'width' => [
            'exclude' => 1,
            'label' => 'width',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'int',
            ],
        ],
        'height' => [
            'exclude' => 1,
            'label' => 'height',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'int',
            ],
        ],
        'settings_flexform' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_widget.settings_flexform',
            'config' => [
                'type' => 'flex',
                'ds_pointerField' => 'widget_identifier',
                'ds' => [
                    \Pixelant\Dashboard\Widget\RssWidgetController::IDENTIFIER => 'FILE:EXT:dashboard/Configuration/FlexForms/flexform_rsswidget.xml',
                    \Pixelant\Dashboard\Widget\ActionWidgetController::IDENTIFIER => 'FILE:EXT:dashboard/Configuration/FlexForms/flexform_actionwidget.xml',
                    \Pixelant\Dashboard\Widget\SysNewsWidgetController::IDENTIFIER => 'FILE:EXT:dashboard/Configuration/FlexForms/flexform_sysnewswidget.xml',
                    \Pixelant\Dashboard\Widget\IframeWidgetController::IDENTIFIER => 'FILE:EXT:dashboard/Configuration/FlexForms/flexform_iframewidget.xml',
                    'default' => '<T3DataStructure><ROOT><type>array</type><el><empty><TCEforms><label>Please select Widget identifier first</label><config><type>none</type></config></TCEforms></empty></el></ROOT></T3DataStructure>',
                ],
            ],
        ],
        'dashboard' => [
            'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboard',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'readOnly' => 1,
                'foreign_table' => 'tx_dashboard_domain_model_dashboard',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
    ],
];
