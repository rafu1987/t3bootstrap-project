<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

// XCLASS scriptmerger
$TYPO3_CONF_VARS['FE']['XCLASS']['ext/scriptmerger/class.tx_scriptmerger.php'] = t3lib_extMgm::extPath($_EXTKEY).'lib/class.ux_tx_scriptmerger.php';

?>