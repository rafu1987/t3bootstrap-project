<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

if (TYPO3_MODE=="BE")	include_once(t3lib_extMgm::extPath("mc_googlesitemapmod")."class.tx_mcgooglesitemapmod_tt_content_tx_mcgooglesitemapmod_objective.php");

$tempColumns = Array (
	"tx_mcgooglesitemapmod_objective" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_objective",
    "displayCond" => "FIELD:menu_type:=:mc_googlesitemapmod_pi1",		  
		"config" => Array (
			"type" => "select",
			"items" => Array (
				Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_objective.I.0", "0"),
			),
			"itemsProcFunc" => "tx_mcgooglesitemapmod_tt_content_tx_mcgooglesitemapmod_objective->main",	
			"size" => 1,	
			"maxitems" => 1,
		)
	),
	"tx_mcgooglesitemapmod_lastmod" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_lastmod",		
		"config" => Array (
			"type" => "select",
			"items" => Array (
				Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_lastmod.I.0", "0"),
				Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_lastmod.I.1", "1"),
			),
			"size" => 1,	
			"maxitems" => 1,
		)
	),
	"tx_mcgooglesitemapmod_pageuid" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_pageuid",	
    "displayCond" => "FIELD:menu_type:=:mc_googlesitemapmod_pi1",		
		"config" => Array (
			"type" => "group",	
			"internal_type" => "db",	
			"allowed" => "pages",	
			"size" => 1,	
			"minitems" => 0,
			"maxitems" => 1,
		)
	),
	"tx_mcgooglesitemapmod_url" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_url",	
    "displayCond" => "FIELD:menu_type:=:mc_googlesitemapmod_pi1",		
		"config" => Array (
			"type" => "text",
			"cols" => "30",	
			"rows" => "6",
		)
	),
	"tx_mcgooglesitemapmod_changefreq" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_changefreq",	
    "displayCond" => "FIELD:menu_type:=:mc_googlesitemapmod_pi1",		
		"config" => Array (
			"type" => "select",
			"items" => Array (
				Array("","0"),
				Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_changefreq.I.0", "1"),
				Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_changefreq.I.1", "2"),
				Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_changefreq.I.2", "3"),
				Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_changefreq.I.3", "4"),
				Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_changefreq.I.4", "5"),
				Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_changefreq.I.5", "6"),
				Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_changefreq.I.6", "7"),
			),
			"size" => 1,	
			"maxitems" => 1,
		)
	),
	"tx_mcgooglesitemapmod_priority" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.tx_mcgooglesitemapmod_priority",		
    "displayCond" => "FIELD:menu_type:=:mc_googlesitemapmod_pi1",	
		"config" => Array (
			"type" => "input",	
			"size" => "5",	
			"range" => Array ("lower"=>0,"upper"=>1),	
			"eval" => "nospace",
		)
	),
);


t3lib_div::loadTCA("tt_content");
t3lib_extMgm::addTCAcolumns("tt_content",$tempColumns,1); 
$TCA["tt_content"]["palettes"]["menu"]["showitem"] .= ',--linebreak--,tx_mcgooglesitemapmod_objective,--linebreak--,tx_mcgooglesitemapmod_lastmod,--linebreak--,tx_mcgooglesitemapmod_pageuid,--linebreak--,tx_mcgooglesitemapmod_url,--linebreak--,tx_mcgooglesitemapmod_changefreq,--linebreak--,tx_mcgooglesitemapmod_priority';

/*

t3lib_div::loadTCA("pages");
t3lib_extMgm::addTCAcolumns("pages",$tempColumns,1);
//t3lib_extMgm::addToAllTCAtypes("pages","tx_mcgooglesitemapmod_priority;;;;1-1-1, tx_mcgooglesitemapmod_changefreq"); 
$TCA["pages"]["palettes"]["miscellaneous"]["showitem"] .= ',--linebreak--,tx_mcgooglesitemapmod_priority,tx_mcgooglesitemapmod_changefreq';

*/

t3lib_extMgm::addPlugin(Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.menu_type_pi1", $_EXTKEY."_pi1"),"menu_type");  
t3lib_extMgm::addPlugin(Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.menu_type_pi2", $_EXTKEY."_pi2"),"menu_type"); 
t3lib_extMgm::addPlugin(Array("LLL:EXT:mc_googlesitemapmod/locallang_db.php:tt_content.menu_type_pi3", $_EXTKEY."_pi3"),"menu_type");

?>