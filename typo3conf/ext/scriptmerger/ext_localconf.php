<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

	// post processing hook to clear any existing cache files if the button in
	// the backend is clicked (contains an age check)
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
    'EXT:scriptmerger/class.tx_scriptmerger_cache.php:&tx_scriptmerger_cache->clearCachePostProc';

	// register the minify, compress and merge processes
if (TYPO3_MODE == 'FE') {
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] =
		'EXT:scriptmerger/class.tx_scriptmerger.php:tx_scriptmerger->contentPostProcOutput';
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] =
		'EXT:scriptmerger/class.tx_scriptmerger.php:tx_scriptmerger->contentPostProcAll';
}

	// needs to be disabled for the frontend, otherwise the default exclude rule prevents any script files from merging
$TYPO3_CONF_VARS['FE']['versionNumberInFilename'] = '';
$TYPO3_CONF_VARS['FE']['compressionLevel'] = '0';

?>