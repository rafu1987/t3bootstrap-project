<?php

if (!defined('TYPO3_MODE')) die ('Access denied.');

// Register this custom 404 handler.
$TYPO3_CONF_VARS['FE']['pageNotFound_handling'] = 'USER_FUNCTION:'.t3lib_extMgm::extPath($_EXTKEY).'class.itypo_404handler.php:user_itypo_404handler->handleError';

// Hook the fe init
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['initFEuser'][$_EXTKEY] = 'EXT:'.$_EXTKEY.'/class.tx_itypo404handler_hooks.php:&tx_itypo404handler_hooks->fe_feuserInit';

?>