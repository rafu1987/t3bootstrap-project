<?php
if (!defined ('TYPO3_MODE'))     die ('Access denied.');

$tempTablesDef = array (
	'static_countries' => array (
		'cn_short_en' => 'cn_short_de',
	),
	'static_country_zones' => array (
		'zn_name_en' => 'zn_name_de',
	),
	'static_currencies' => array (
		'cu_name_en' => 'cu_name_de',
		'cu_sub_name_en' => 'cu_sub_name_de',
	),
	'static_languages' => array (
		'lg_name_en' => 'lg_name_de',
	),
	'static_territories' => array (
		'tr_name_en' => 'tr_name_de',
	)
);

foreach ($tempTablesDef as $tempTable => $tempFieldDef) {
	t3lib_div::loadTCA($tempTable);
	foreach ($tempFieldDef as $tempSourceField => $tempDestField) {
		$tempColumns = array();
		$tempColumns[$tempDestField] = $TCA[$tempTable]['columns'][$tempSourceField];
		$tempColumns[$tempDestField]['label'] = 'LLL:EXT:'.STATIC_INFO_TABLES_DE_EXTkey.'/locallang_db.xml:'.$tempTable.'_item.'.$tempDestField;
		t3lib_extMgm::addTCAcolumns($tempTable, $tempColumns, 1);
		t3lib_extMgm::addToAllTCAtypes($tempTable, $tempDestField, '', 'after:'.$tempSourceField);
	}
}

?>