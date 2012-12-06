<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$GLOBALS['fl_realurl_image'] = unserialize($_EXTCONF);

// Generate RealURL Image Paths - XCLASS: tslib_content  (Datei: typo3/syext/cms/tslib/class.tslib_content.php)
// the essential part
$TYPO3_CONF_VARS['FE']['XCLASS']['tslib/class.tslib_content.php'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/class.ux_tslib_cObj.php';

// Decode the RealURLs - HOOK: tslib_fe
// this is just necessary if the option linkStatic is not activated.
// if linkStatic is activated this is just a back up in case a link got lost.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.hook_tslib_fe.php']['connectToDB']['tx_flrealurlimage'] = 'EXT:fl_realurl_image/Classes/class.hook_tslib_fe.php:&hook_tslib_fe->checkImageDecode';

$GLOBALS['TYPO3_CONF_VARS']['FE']['XCLASS']['tslib/content/class.tslib_content_imageresource.php'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/XClass/ImageResource.php';


// Sheduler
if (TYPO3_MODE == 'BE') {
	if (class_exists('tx_scheduler_Task'))
		require_once(t3lib_extMgm::extPath('fl_realurl_image', 'Classes/Service/CleanTask.php'));
	$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['Tx_FlRealurlImage_CleanTask'] = array(
	    'extension' => $_EXTKEY,
	    'title' => 'Cleanup RealURL Images', //'title' => 'LLL:EXT:'.$_EXTKEY.'/locallang.xml:TaskName.name', // Der Titel der Aufgabe
		   //'description' => 'LLL:EXT:'.$_EXTKEY.'/locallang.xml:TaskName.description', // Die Beschreibung der Aufgabe
		   // 'additionalFields' => 'tx_extkey_TaskName_AdditionalFieldProvider' // Zus�tzliche Felder
	);
}
?>