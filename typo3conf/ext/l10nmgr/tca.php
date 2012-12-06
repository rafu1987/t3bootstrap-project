<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_l10nmgr_cfg"] = Array (
	"ctrl" => $TCA["tx_l10nmgr_cfg"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "title,depth,sourceLangStaticId,tablelist,exclude,incfcewithdefaultlanguage"
	),
	"feInterface" => $TCA["tx_l10nmgr_cfg"]["feInterface"],
	"columns" => Array (
		"title" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.title",
			"config" => Array (
				"type" => "input",
				"size" => "48",
				"eval" => "required",
			)
		),
		"filenameprefix" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.filenameprefix",
			"config" => Array (
				"type" => "input",
				"size" => "48",
				"eval" => "required",
			)
		),
		"depth" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.depth",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.depth.I.0", "0"),
					Array("LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.depth.I.1", "1"),
					Array("LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.depth.I.2", "2"),
					Array("LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.depth.I.3", "3"),
					Array("LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.depth.I.4", "100"),
					Array("LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.depth.I.-1", "-1"),
				),
				"size" => 1,
				"maxitems" => 1,
			)
		),
		"displaymode" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.displaymode",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.displaymode.I.0", "0"),
					Array("LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.displaymode.I.1", "1"),
					Array("LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.displaymode.I.2", "2"),
				),
				"size" => 1,
				"maxitems" => 1,
			)
		),
		"tablelist" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.tablelist",
			"config" => Array (
				'type' => 'select',
				'special' => 'tables',
				'size' => '5',
				'autoSizeMax' => 50,
				'maxitems' => 100,
				'renderMode' => $GLOBALS['TYPO3_CONF_VARS']['BE']['accessListRenderMode'],
				'iconsInOptionTags' => 1,
			)
		),
		"exclude" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.exclude",
			"config" => Array (
				"type" => "text",
				"cols" => "48",
				"rows" => "3",
			)
		),
		"include" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.include",
			"config" => Array (
				"type" => "text",
				"cols" => "48",
				"rows" => "3",
			)
		),
		"sourceLangStaticId" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.sourceLang",
			"displayCond" => "EXT:static_info_tables:LOADED:true",
			"config" => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'foreign_table' => 'static_languages',
				'foreign_table_where' => 'AND static_languages.pid=0 ORDER BY static_languages.lg_name_en',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		"incfcewithdefaultlanguage" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.incfcewithdefaultall",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "title,filenameprefix;;;;2-2-2, depth;;;;3-3-3, sourceLangStaticId, tablelist, exclude, include, displaymode, incfcewithdefaultlanguage")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);

$TCA["tx_l10nmgr_priorities"] = Array (
	"ctrl" => $TCA["tx_l10nmgr_priorities"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,title,description,languages,element"
	),
	"feInterface" => $TCA["tx_l10nmgr_priorities"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"title" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_priorities.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"description" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_priorities.description",
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"languages" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_priorities.languages",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "sys_language",
				"foreign_table_where" => "AND sys_language.pid=###SITEROOT### AND sys_language.hidden=0 ORDER BY sys_language.uid",
				"size" => 5,
				"minitems" => 0,
				"maxitems" => 100,
			)
		),
		"element" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_priorities.element",
			"config" => Array (
				"type" => "group",
				"internal_type" => "db",
				"allowed" => "*",
				"prepend_tname" => TRUE,
				"size" => 10,
				"minitems" => 0,
				"maxitems" => 100,
				"show_thumbs" => 1
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2, description;;;;3-3-3, languages, element")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);

$TCA["tx_l10nmgr_exportdata"] = Array (
	"ctrl" => $TCA["tx_l10nmgr_exportdata"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "title,crdate, tablelist,translation_lang,source_lang"
	),
	"feInterface" => $TCA["tx_l10nmgr_exportdata"]["feInterface"],
	"columns" => Array (
		"title" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.title",
			"config" => Array (
				"type" => "input",
				"size" => "48",
				"eval" => "required",
				"readOnly" => 1,
			)
		),
		"crdate" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.crdate",
			"config" => Array (
				"type" => "input",
				"eval" => "date",
				"size" => "48",
				"readOnly" => 1,
			)
		),
		"tablelist" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.exporttablelist",
			"config" => Array (
				"type" => "input",
				"size" => "48",
				"readOnly" => 1,
			)
		),
		"translation_lang" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.translationLang",
			"config" => Array (
				"type" => "input",
				"size" => "48",
				"readOnly" => 1,
			)
		),
		"source_lang" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:l10nmgr/locallang_db.xml:tx_l10nmgr_cfg.sourceLang",
			"config" => Array (
				"type" => "input",
				"size" => "48",
				"readOnly" => 1,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "title, crdate, translation_lang, tablelist, source_lang")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>
