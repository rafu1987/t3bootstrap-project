<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_in2facebook_domain_model_opengraph'] = array(
	'ctrl' => $TCA['tx_in2facebook_domain_model_opengraph']['ctrl'],
	'interface' => array(
		'showRecordFieldList'	=> 'title,type,url,image,site_name,admins,description',
	),
	'types' => array(
		'1' => array('showitem'	=> 'title,type,url,image,site_name,admins,description'),
	),
	'palettes' => array(
		'1' => array('showitem'	=> ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude'			=> 1,
			'label'				=> 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config'			=> array(
				'type'					=> 'select',
				'foreign_table'			=> 'sys_language',
				'foreign_table_where'	=> 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.php:LGL.default_value', 0)
				),
			)
		),
		'l18n_parent' => array(
			'displayCond'	=> 'FIELD:sys_language_uid:>:0',
			'exclude'		=> 1,
			'label'			=> 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config'		=> array(
				'type'			=> 'select',
				'items'			=> array(
					array('', 0),
				),
				'foreign_table' => 'tx_in2facebook_domain_model_opengraph',
				'foreign_table_where' => 'AND tx_in2facebook_domain_model_opengraph.uid=###REC_FIELD_l18n_parent### AND tx_in2facebook_domain_model_opengraph.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array(
			'config'		=>array(
				'type'		=>'passthrough',
			)
		),
		't3ver_label' => array(
			'displayCond'	=> 'FIELD:t3ver_label:REQ:true',
			'label'			=> 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
			'config'		=> array(
				'type'		=>'none',
				'cols'		=> 27,
			)
		),
		'hidden' => array(
			'exclude'	=> 1,
			'label'		=> 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'	=> array(
				'type'	=> 'check',
			)
		),
		'title' => array(
			'exclude'	=> 1,
			'label'		=> 'LLL:EXT:in2facebook/Resources/Private/Language/locallang_db.xml:tx_in2facebook_domain_model_opengraph.title',
			'config'	=> array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'type' => array(
			'exclude'	=> 1,
			'label'		=> 'LLL:EXT:in2facebook/Resources/Private/Language/locallang_db.xml:tx_in2facebook_domain_model_opengraph.type',
			'config'	=> array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'url' => array(
			'exclude'	=> 1,
			'label'		=> 'LLL:EXT:in2facebook/Resources/Private/Language/locallang_db.xml:tx_in2facebook_domain_model_opengraph.url',
			'config'	=> array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'image' => array(
			'exclude'	=> 1,
			'label'		=> 'LLL:EXT:in2facebook/Resources/Private/Language/locallang_db.xml:tx_in2facebook_domain_model_opengraph.image',
			'config'	=> array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'site_name' => array(
			'exclude'	=> 1,
			'label'		=> 'LLL:EXT:in2facebook/Resources/Private/Language/locallang_db.xml:tx_in2facebook_domain_model_opengraph.site_name',
			'config'	=> array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'admins' => array(
			'exclude'	=> 1,
			'label'		=> 'LLL:EXT:in2facebook/Resources/Private/Language/locallang_db.xml:tx_in2facebook_domain_model_opengraph.admins',
			'config'	=> array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'description' => array(
			'exclude'	=> 0,
			'label'		=> 'LLL:EXT:in2facebook/Resources/Private/Language/locallang_db.xml:tx_in2facebook_domain_model_opengraph.description',
			'config'	=> array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
	),
);
?>