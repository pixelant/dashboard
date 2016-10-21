<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_dashboard_domain_model_dashboardwidgetsettings'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_dashboard_domain_model_dashboardwidgetsettings']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, title, widget_identifier, state, position, settings_flexform, dashboard',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, title, widget_identifier, state, position, settings_flexform, dashboard, '),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(

		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
                'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
                'renderType' => 'selectSingle',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_dashboard_domain_model_dashboardwidgetsettings',
				'foreign_table_where' => 'AND tx_dashboard_domain_model_dashboardwidgetsettings.pid=###CURRENT_PID### AND tx_dashboard_domain_model_dashboardwidgetsettings.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboardwidgetsettings.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'widget_identifier' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboardwidgetsettings.widget_identifier',
			'config' => array(
				'type' => 'select',
                'renderType' => 'selectSingle',
				'items' => array(
					array('-- Label --', 0),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			),
		),
		'state' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboardwidgetsettings.state',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'position' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboardwidgetsettings.position',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'settings_flexform' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboardwidgetsettings.settings_flexform',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			)
		),
		'dashboard' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
	),
);
## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder
$GLOBALS['TCA']['tx_dashboard_domain_model_dashboardwidgetsettings']['columns']['widget_identifier']['config']['items']['41385600'] = array('Widget 41385600', 41385600);
$GLOBALS['TCA']['tx_dashboard_domain_model_dashboardwidgetsettings']['columns']['widget_identifier']['config']['items']['1439441923'] = array('Widget 1439441923', 1439441923);
$GLOBALS['TCA']['tx_dashboard_domain_model_dashboardwidgetsettings']['columns']['widget_identifier']['config']['items']['1439446997'] = array('Widget 1439446997', 1439446997);

$GLOBALS['TCA']['tx_dashboard_domain_model_dashboardwidgetsettings']['columns']['settings_flexform'] = array(
	'exclude' => 1,
	'label' => 'LLL:EXT:dashboard/Resources/Private/Language/locallang_db.xlf:tx_dashboard_domain_model_dashboardwidgetsettings.settings_flexform',
	'config' => array(
		'type' => 'flex',
		'ds_pointerField' => 'widget_identifier',
		'ds' => array(
			'41385600' => 'FILE:EXT:dashboard/Configuration/FlexForms/flexform_rsswidget.xml',
			'1439441923' => 'FILE:EXT:dashboard/Configuration/FlexForms/flexform_actionwidget.xml',
			'1439446997' => 'FILE:EXT:dashboard/Configuration/FlexForms/flexform_sysnewswidget.xml',
		)
	)
);
$GLOBALS['TCA']['tx_dashboard_domain_model_dashboardwidgetsettings']['columns']['dashboard']['config'] = array(
	'type' => 'select',
    'renderType' => 'selectSingle',
	'foreign_table' => 'tx_dashboard_domain_model_dashboard',
	'minitems' => 0,
	'maxitems' => 1,
);
