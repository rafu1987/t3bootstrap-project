<?php
if (!defined ('TYPO3_MODE')) 
	die ('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['BE']['XCLASS']['ext/rtehtmlarea/extensions/RemoveFormat/class.tx_rtehtmlarea_removeformat.php'] = t3lib_extMgm::extPath($_EXTKEY).'class.ux_tx_rtehtmlarea_removeformat.php';
?>