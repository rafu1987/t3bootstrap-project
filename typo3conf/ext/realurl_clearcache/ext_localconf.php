<?php
if (!defined ("TYPO3_MODE")) die ("Access denied.");

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['additionalBackendItems']['cacheActions'][] = 'EXT:realurl_clearcache/class.tx_realurlclearcache_cachemenu.php:&tx_realurlclearcache_cachemenu';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_realurlclearcache::clear'] = 'EXT:realurl_clearcache/class.tx_realurlclearcache.php:tx_realurlclearcache->clear';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/template.php']['docHeaderButtonsHook'][] = 'EXT:realurl_clearcache/class.tx_realurlclearcache.php:tx_realurlclearcache->pageIcon';
?>