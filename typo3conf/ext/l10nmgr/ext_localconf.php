<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_l10nmgr_cfg=1
	options.saveDocNew.tx_l10nmgr_priorities=1
');

if (TYPO3_MODE=='BE')    {
    // Setting up scripts that can be run from the cli_dispatch.phpsh script.
    $TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys']['l10nmgr_import'] = array('EXT:'.$_EXTKEY.'/cli/cli.import.php','_CLI_user');
    $TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys']['l10nmgr_export'] = array('EXT:'.$_EXTKEY.'/cli/cli.export.php','_CLI_user');
}

//! increase with every change to XML Format
define('L10NMGR_FILEVERSION','1.2');
define('L10NMGR_VERSION','3.4.0');

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['lowlevel']['cleanerModules']['tx_l10nmgr_index'] = array('EXT:l10nmgr/class.tx_l10nmgr_index.php:tx_l10nmgr_index');
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['tx_l10nmgr'] = 'EXT:l10nmgr/class.l10nmgr_tcemain_hook.php:&tx_l10nmgr_tcemain_hook';
$_EXTCONF_ARRAY = unserialize($_EXTCONF);
if ($_EXTCONF_ARRAY['enable_stat_hook']) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['recStatInfoHooks']['tx_l10nmgr'] = 'EXT:l10nmgr/class.l10nmgr_tcemain_hook.php:&tx_l10nmgr_tcemain_hook->stat';
}

	// Add file cleanup task
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_l10nmgr_FileGarbageCollection'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'LLL:EXT:' . $_EXTKEY . '/tasks/locallang.xml:fileGarbageCollection.name',
	'description'      => 'LLL:EXT:' . $_EXTKEY . '/tasks/locallang.xml:fileGarbageCollection.description',
	'additionalFields' => 'tx_l10nmgr_FileGarbageCollection_AdditionalFieldProvider',
);
?>