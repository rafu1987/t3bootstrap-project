<?php

if (!defined("TYPO3_MODE"))
	die("Access denied.");

t3lib_extMgm::addPItoST43($_EXTKEY, "pi1/class.tx_sourceopt_pi1.php", "_pi1", "", 1);

#####################################################
## Hook for HTML-modification on the page   #########
#####################################################
// hook is called after Caching! 
// => for modification of pages with COA_/USER_INT objects. 
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'EXT:sourceopt/class.tx_sourceopt_fehook.php:&tx_sourceopt_fehook->intPages';
// hook is called before Caching!
// => for modification of pages on their way in the cache.
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = 'EXT:sourceopt/class.tx_sourceopt_fehook.php:&tx_sourceopt_fehook->noIntPages';
?>