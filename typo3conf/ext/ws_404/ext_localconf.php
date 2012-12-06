<?php
if (!defined("TYPO3_MODE")) die("Access denied.");
$charset = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ? $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] : $GLOBALS['TSFE']->defaultCharSet;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] == '1';

$TYPO3_CONF_VARS['FE']['pageNotFound_handling'] = 'USER_FUNCTION:typo3conf/ext/ws_404/class.user_pageNotFound.php:user_pageNotFound->pageNotFound';
$TYPO3_CONF_VARS['FE']['pageNotFound_handling_statheader'] = 'HTTP/1.0 404 Not Found
Content-Type: text/html; charset='. $charset;

$TYPO3_CONF_VARS['FE']['pageUnavailable_handling'] = 'USER_FUNCTION:typo3conf/ext/ws_404/class.user_pageNotFound.php:user_pageNotFound->pageNotFound';
$TYPO3_CONF_VARS['FE']['pageUnavailable_handling_statheader'] = 'HTTP/1.0 503 Service Temporarily Unavailable
Content-Type: text/html; charset='. $charset;

$TYPO3_CONF_VARS['SYS']['devIPmask'] = '';
?>