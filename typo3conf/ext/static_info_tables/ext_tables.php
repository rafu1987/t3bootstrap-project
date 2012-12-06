<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addStaticFile(STATIC_INFO_TABLES_EXTkey, 'static/static_info_tables/', 'Static Info tables');

$TCA['static_territories'] = array(
	'ctrl' => array(
		'label' => 'tr_name_en',
		'label_alt' => 'tr_name_en,tr_iso_nr',
		'readOnly' => 1,	// This should always be true, as it prevents the static data from being altered
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'default_sortby' => 'ORDER BY tr_name_en',
		'title' => 'LLL:EXT:'.STATIC_INFO_TABLES_EXTkey.'/locallang_db.xml:static_territories.title',
		'dynamicConfigFile' => PATH_BE_staticinfotables.'tca.php',
		'iconfile' => PATH_BE_staticinfotables_rel.'icon_static_territories.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'tr_name_en,tr_iso_nr'
	)
);

// Country reference data from ISO 3166-1
$TCA['static_countries'] = array(
	'ctrl' => array(
		'label' => 'cn_short_en',
		'label_alt' => 'cn_short_en,cn_iso_2',
		'readOnly' => 1,	// This should always be true, as it prevents the static data from being altered
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'default_sortby' => 'ORDER BY cn_short_en',
		'delete' => 'deleted',
		'title' => 'LLL:EXT:'.STATIC_INFO_TABLES_EXTkey.'/locallang_db.xml:static_countries.title',
		'dynamicConfigFile' => PATH_BE_staticinfotables.'tca.php',
		'iconfile' => PATH_BE_staticinfotables_rel.'icon_static_countries.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'cn_iso_2,cn_iso_3,cn_iso_nr,cn_official_name_local,cn_official_name_en,cn_capital,cn_tldomain,cn_currency_iso_3,cn_currency_iso_nr,cn_phone,cn_uno_member,cn_eu_member,cn_address_format,cn_short_en'
	)
);

// Country subdivision reference data from ISO 3166-2
$TCA['static_country_zones'] = array(
	'ctrl' => array(
		'label' => 'zn_name_local',
		'label_alt' => 'zn_name_local,zn_code',
		'readOnly' => 1,
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'default_sortby' => 'ORDER BY zn_name_local',
		'title' => 'LLL:EXT:'.STATIC_INFO_TABLES_EXTkey.'/locallang_db.xml:static_country_zones.title',
		'dynamicConfigFile' => PATH_BE_staticinfotables.'tca.php',
		'iconfile' => PATH_BE_staticinfotables_rel.'icon_static_countries.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'zn_country_iso_nr,zn_country_iso_3,zn_code,zn_name_local,zn_name_en'
	)
);

// Language reference data from ISO 639-1
$TCA['static_languages'] = array(
	'ctrl' => array(
		'label' => 'lg_name_en',
		'label_alt' => 'lg_name_en,lg_iso_2',
		'readOnly' => 1,
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'default_sortby' => 'ORDER BY lg_name_en',
		'title' => 'LLL:EXT:'.STATIC_INFO_TABLES_EXTkey.'/locallang_db.xml:static_languages.title',
		'dynamicConfigFile' => PATH_BE_staticinfotables.'tca.php',
		'iconfile' => PATH_BE_staticinfotables_rel.'icon_static_languages.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'lg_name_local,lg_name_en,lg_iso_2,lg_typo3,lg_country_iso_2,lg_collate_locale,lg_sacred,lg_constructed'
	)
);

// Currency reference data from ISO 4217
$TCA['static_currencies'] = array(
	'ctrl' => array(
		'label' => 'cu_name_en',
		'label_alt' => 'cu_name_en,cu_iso_3',
		'readOnly' => 1,
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'default_sortby' => 'ORDER BY cu_name_en',
		'title' => 'LLL:EXT:'.STATIC_INFO_TABLES_EXTkey.'/locallang_db.xml:static_currencies.title',
		'dynamicConfigFile' => PATH_BE_staticinfotables.'tca.php',
		'iconfile' => PATH_BE_staticinfotables_rel.'icon_static_currencies.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'cu_iso_3,cu_iso_nr,cu_name_en,cu_symbol_left,cu_symbol_right,cu_thousands_point,cu_decimal_point,cu_decimal_digits,cu_sub_name_en,cu_sub_divisor,cu_sub_symbol_left,cu_sub_symbol_right'
	)
);

$TCA['static_countries']['ctrl']['readOnly'] = 0;
$TCA['static_languages']['ctrl']['readOnly'] = 0;
$TCA['static_country_zones']['ctrl']['readOnly'] = 0;
$TCA['static_currencies']['ctrl']['readOnly'] = 0;
$TCA['static_territories']['ctrl']['readOnly'] = 0;


// ******************************************************************
// sys_language
// ******************************************************************

t3lib_div::loadTCA('sys_language');
$TCA['sys_language']['columns']['static_lang_isocode']['config'] = array(
	'type' => 'select',
	'items' => array(
		array('',0),
	),
	#'foreign_table' => 'static_languages',
	#'foreign_table_where' => 'AND static_languages.pid=0 ORDER BY static_languages.lg_name_en',
	'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
	'itemsProcFunc_config' => array(
		'table' => 'static_languages',
		'indexField' => 'uid',
		// I think that will make more sense in the future
		// 'indexField' => 'lg_iso_2',
		'prependHotlist' => 1,
		//	defaults:
		//'hotlistLimit' => 8,
		//'hotlistSort' => 1,
		//'hotlistOnly' => 0,
		//'hotlistApp' => TYPO3_MODE,
	),
	'size' => 1,
	'minitems' => 0,
	'maxitems' => 1,
);

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:'.STATIC_INFO_TABLES_EXTkey.'/class.tx_staticinfotables_syslanguage.php:&tx_staticinfotables_syslanguage';

?>