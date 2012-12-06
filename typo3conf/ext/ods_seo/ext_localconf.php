<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_odsseo_pi1.php', '_pi1', 'includeLib', 0);

if(!empty($GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'])) $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'].=',';
$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'].='shortcut';

// a tag rel attribute
$TYPO3_CONF_VARS['BE']['XCLASS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']=t3lib_extMgm::extPath('ods_seo').'class.ux_tx_rtehtmlarea_browse_links.php';
?>