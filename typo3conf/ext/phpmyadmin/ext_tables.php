<?php
/**
 * TYPO3 Extension configuration for the tx_phpmyadmin Extension
 *
 * @author		mehrwert <typo3@mehrwert.de>
 * @package		TYPO3
 * @subpackage	tx_phpmyadmin
 * @license		GPL
 * @version		$Id: ext_tables.php 68078 2012-11-28 23:22:22Z mehrwert $
 */

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

	// Get config
$extensionConfiguration = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['phpmyadmin']);

	// Check for IP restriction (devIpMask), and die if not allowed
$showPhpMyAdminInWebModule = (boolean) $extensionConfiguration['showPhpMyAdminInWebModule'];

	// If the backend is loaded, add the module
if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModule('tools', 'txphpmyadmin', '', t3lib_extMgm::extPath($_EXTKEY) . 'modsub/');
}

	// Require the utilities class and define logoff method for hook
require_once(t3lib_extMgm::extPath('phpmyadmin').'res/class.tx_phpmyadmin_utilities.php');

	// Do not load post processing class if TYPO3 is in CLI mode
if (!defined('TYPO3_cliMode') || !TYPO3_cliMode) {
	$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_post_processing'][] = 'tx_phpmyadmin_utilities->user_pmaLogOff';
}

	// The subdirectory where the pMA source is located (used for cookie removal and script inclusion)
$TYPO3_CONF_VARS['EXTCONF']['phpmyadmin']['pmaDirname'] = 'phpMyAdmin-3.5.4-all-languages';

?>