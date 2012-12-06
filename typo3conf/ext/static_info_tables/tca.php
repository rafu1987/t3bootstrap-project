<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['static_territories'] = array(
	'ctrl' => $TCA['static_territories']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'tr_iso_nr,tr_parent_iso_nr,tr_name_en'
	),
	'columns' => array(
		'tr_iso_nr' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_territories_item.tr_iso_nr',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'max' => '7',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'tr_parent_iso_nr' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_territories_item.tr_parent_iso_nr',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'max' => '7',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'tr_name_en' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.name',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '45',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		)
	),
	'types' => array(
		'1' => array(
			'showitem' => 'tr_iso_nr,tr_name_en,tr_parent_iso_nr'
		)
	),
);

$TCA['static_countries'] = array(
	'ctrl' => $TCA['static_countries']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'cn_iso_2,cn_iso_3,cn_iso_nr,cn_official_name_local,cn_official_name_en,cn_capital,cn_tldomain,cn_currency_iso_3,cn_currency_iso_nr,cn_phone,cn_uno_member,cn_eu_member,cn_address_format,cn_short_en'
	),
	'columns' => array(
		'cn_iso_2' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_iso_2',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '4',
				'max' => '2',
				'eval' => '',
				'default' => ''
			)
		),
		'cn_iso_3' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_iso_3',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'max' => '3',
				'eval' => '',
				'default' => ''
			)
		),
		'cn_iso_nr' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_iso_nr',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'max' => '7',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'cn_parent_tr_iso_nr' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_territories_item.tr_parent_iso_nr',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'max' => '7',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'cn_official_name_local' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_official_name_local',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '25',
				'max' => '128',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cn_official_name_en' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_official_name_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '25',
				'max' => '50',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cn_capital' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_capital',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '15',
				'max' => '45',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cn_tldomain' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_tldomain',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'max' => '',
				'eval' => '',
				'default' => ''
			)
		),
		'cn_currency_iso_nr' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_currency_iso_nr',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'max' => '3',
				'eval' => '',
				'default' => ''
			)
		),
		'cn_currency_iso_3' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_currency_iso_3',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'max' => '7',
				'eval' => '',
				'default' => '0'
			)
		),
		'cn_phone' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_phone',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => '',
				'default' => '0'
			)
		),
		'cn_eu_member' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_eu_member',
			'exclude' => '0',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'cn_uno_member' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_uno_member',
			'exclude' => '0',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'cn_address_format' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_address_format',
			'exclude' => '0',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('','0'),
					array('LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_address_format_1','1'),
					array('LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_address_format_2','2'),
					array('LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_address_format_3','3'),
					array('LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_address_format_4','4'),
					array('LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_address_format_5','5'),
					array('LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_address_format_6','6'),
					array('LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_address_format_7','7'),
					array('LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_address_format_8','8'),
					array('LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_address_format_9','9'),
					),
				'default' => '0'
			)
		),
		'cn_zone_flag' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_zone_flag',
			'exclude' => '0',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'cn_short_local' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_short_local',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '25',
				'max' => '50',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cn_short_en' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_short_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '25',
				'max' => '50',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
	),
	'types' => array(
		'1' => array(
			'showitem' => 'cn_short_local,cn_official_name_local,cn_official_name_en,--palette--;;1;;,--palette--;;2;;,--palette--;;3;;,--palette--;;4;;,cn_short_en'
		)
	),
	'palettes'	=> array(
		'1' => array(
			'showitem' => 'cn_iso_nr,cn_iso_2,cn_iso_3,cn_parent_tr_iso_nr', 'canNotCollapse' => '1'
		),
		'2' => array(
			'showitem' => 'cn_currency_iso_nr,cn_currency_iso_3', 'canNotCollapse' => '1'
		),
		'3' => array(
			'showitem' => 'cn_capital,cn_uno_member,cn_eu_member,cn_phone,cn_tldomain', 'canNotCollapse' => '1'
		),
		'4' => array(
			'showitem' => 'cn_address_format,cn_zone_flag', 'canNotCollapse' => '1'
		)
	)
);

$TCA['static_currencies'] = array(
	'ctrl' => $TCA['static_currencies']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'cu_iso_3,cu_iso_nr,cu_name_en,cu_symbol_left,cu_symbol_right,cu_thousands_point,cu_decimal_point,cu_decimal_digits,cu_sub_name_en,cu_sub_divisor,cu_sub_symbol_left,cu_sub_symbol_right'
	),
	'columns' => array(
		'cu_iso_3' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_currencies_item.cu_iso_3',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'max' => '3',
				'eval' => '',
				'default' => ''
			)
		),
		'cu_iso_nr' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_currencies_item.cu_iso_nr',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'max' => '3',
				'eval' => '',
				'default' => '0'
			)
		),
		'cu_name_en' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_currencies_item.cu_name_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '40',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cu_sub_name_en' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_currencies_item.cu_sub_name_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '20',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cu_symbol_left' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_currencies_item.cu_symbol_left',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '12',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cu_symbol_right' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_currencies_item.cu_symbol_right',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '12',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cu_thousands_point' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_currencies_item.cu_thousands_point',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '3',
				'max' => '1',
				'eval' => '',
				'default' => ''
			)
		),
		'cu_decimal_point' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_currencies_item.cu_decimal_point',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '3',
				'max' => '1',
				'eval' => '',
				'default' => ''
			)
		),
		'cu_decimal_digits' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_currencies_item.cu_decimal_digits',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'max' => '',
				'eval' => 'int',
				'default' => ''
			)
		),
		'cu_sub_divisor' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_currencies_item.cu_sub_divisor',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'int',
				'default' => '1'
			)
		),
		'cu_sub_symbol_left' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_currencies_item.cu_sub_symbol_left',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '12',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cu_sub_symbol_right' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_currencies_item.cu_sub_symbol_right',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '12',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		)
	),
	'types' => array(
		'1' => array(
			'showitem' => 'cu_name_en,--palette--;;1;;,--palette--;;2;;,cu_sub_name_en,--palette--;;3;;'
		)
	),
	'palettes'	=> array(
		'1' => array(
			'showitem' => 'cu_iso_nr,cu_iso_3', 'canNotCollapse' => '1'
		),
		'2' => array(
			'showitem' => 'cu_symbol_left,cu_symbol_right,cu_thousands_point,cu_decimal_point', 'canNotCollapse' => '1'
		),
		'3' => array(
			'showitem' => 'cu_sub_symbol_left,cu_sub_symbol_right,cu_decimal_digits,cu_sub_divisor', 'canNotCollapse' => '1'
		)
	)
);

$TCA['static_languages'] = array(
	'ctrl' => $TCA['static_languages']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'lg_name_local,lg_name_en,lg_iso_2,lg_typo3,lg_country_iso_2,lg_collate_locale,lg_sacred,lg_constructed'
	),
	'columns' => array(
		'lg_iso_2' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_languages_item.lg_iso_2',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '4',
				'max' => '2',
				'eval' => '',
				'default' => ''
			)
		),
		'lg_name_local' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.name',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '25',
				'max' => '50',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'lg_name_en' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_languages_item.lg_name_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '40',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'lg_typo3' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_languages_item.lg_typo3',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '3',
				'max' => '2',
				'eval' => '',
				'default' => ''
			)
		),
		'lg_country_iso_2' => Array (
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_countries_item.cn_iso_2',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '3',
				'max' => '2',
				'eval' => '',
				'default' => ''
			)
		),
		'lg_collate_locale' => Array (
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_languages_item.lg_collate_locale',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '5',
				'max' => '5',
				'eval' => '',
				'default' => ''
			)
		),
		'lg_sacred' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_languages_item.lg_sacred',
			'exclude' => '0',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'lg_constructed' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_languages_item.lg_constructed',
			'exclude' => '0',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
	),
	'types' => array(
		'1' => array(
			'showitem' => 'lg_name_local,lg_name_en,lg_iso_2,lg_typo3,lg_country_iso_2,lg_collate_locale,lg_sacred,lg_constructed'
		)
	)
);

$TCA['static_country_zones'] = array(
	'ctrl' => $TCA['static_country_zones']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'zn_country_iso_nr,zn_country_iso_2,zn_country_iso_3,zn_code,zn_name_local,zn_name_en'
	),
	'columns' => array(
		'zn_country_iso_nr' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_country_zones_item.zn_country_iso_nr',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'zn_country_iso_2' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_country_zones_item.zn_country_iso_2',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '3',
				'max' => '2',
				'eval' => '',
				'default' => ''
			)
		),
		'zn_country_iso_3' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_country_zones_item.zn_country_iso_3',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'max' => '3',
				'eval' => '',
				'default' => ''
			)
		),
		'zn_code' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_country_zones_item.zn_code',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '45',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'zn_name_local' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.name',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '45',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'zn_name_en' => array(
			'label' => 'LLL:EXT:static_info_tables/locallang_db.xml:static_country_zones_item.zn_name_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '45',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
	),
	'types' => array(
		'1' => array(
			'showitem' => 'zn_name_local,zn_code,--palette--;;1;;,zn_name_en'
		)
	),
	'palettes'	=> array(
		'1' => array(
			'showitem' => 'zn_country_iso_nr,zn_country_iso_2,zn_country_iso_3', 'canNotCollapse' => '1'
		)
	)
);

?>