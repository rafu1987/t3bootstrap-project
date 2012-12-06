<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key";


t3lib_extMgm::addPlugin(Array("LLL:EXT:macina_searchbox/locallang_db.php:tt_content.list_type", $_EXTKEY."_pi1"),"list_type");


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_macinasearchbox_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi1/class.tx_macinasearchbox_pi1_wizicon.php";
?>