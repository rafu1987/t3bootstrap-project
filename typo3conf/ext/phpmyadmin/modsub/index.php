<?php
/***************************************************************
* Copyright notice
*
* (c) 1999-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
* (c) 2006-2011 mehrwert (typo3@mehrwert.de)
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Utilities for the phpMyAdmin third party database administration Tool
 *
 * @package		TYPO3
 * @subpackage	tx_phpmyadmin
 * @version		$Id: index.php 79361 2013-09-01 08:36:12Z mehrwert $
 * @author		mehrwert <typo3@mehrwert.de>
 * @author		Kasper Skårhøj <kasperYYYY@typo3.com>
 * @license		GPL
 */
class SC_mod_tools_phpadmin_index {

	/**
	 * Configuration for the module
	 * @var	Array
	 */
	public $MCONF = array();

	/**
	 * The backend document
	 * @var	Object
	 */
	public $doc;

	/**
	 * The main method of the Plugin
	 *
	 * @return	Mixed		Either returns an error or sends a redirect header
	 */
	public function main() {

			// Declare globals
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

			// Set the path to phpMyAdmin
		$extPath = t3lib_extMgm::extPath('phpmyadmin');
		$typo3DocumentRoot = t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT');

			// Set class config for module
		$this->MCONF = $GLOBALS['MCONF'];

			// Get config
		$extensionConfiguration = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['phpmyadmin']);

			// IP-based Access restrictions
		$devIPmask = trim($TYPO3_CONF_VARS['SYS']['devIPmask']);
		$remoteAddress = t3lib_div::getIndpEnv('REMOTE_ADDR');

					// Check for IP restriction (devIpMask), and die if not allowed
		$useDevIpMask = (boolean) $extensionConfiguration['useDevIpMask'];
		if ($useDevIpMask === TRUE) {
				// Abort if devIPmask is wildcarded
			if ($devIPmask != '*') {
				$message = '<h1>Access Denied</h1>
							<p>
								This phpMyAdmin-Module was configured with IP-based access restrictions and your
								REMOTE_ADDR ('.$remoteAddress.') is not in TYPO3 devIPmask ('.$devIPmask.').
							</p>';
				if (!t3lib_div::cmpIP($remoteAddress, $devIPmask)) {
					die($message);
				}
			}
		}

			// Check for ip restriction, and die if not allowed
		$allowedIps = trim($extensionConfiguration['allowedIps']);
		if (!empty($allowedIps)) {
			$message = '<h1>Access Denied</h1>
						<p>
							This phpMyAdmin-Module was configured with IP-based access restrictions and your
							REMOTE_ADDR ('.$remoteAddress.') is not in the list of allowed IPs ('.$allowedIps.').
						</p>';
			if (!t3lib_div::cmpIP($remoteAddress, $allowedIps)) {
				die($message);
			}
		}

			// Path to install dir
		$this->MCONF['PMA_absolute_path'] = $extPath.$this->MCONF['PMA_subdir'];

			// PMA uses relative file inclusion, so we need to ensure a proper include_path
		@set_include_path($this->MCONF['PMA_absolute_path'] . PATH_SEPARATOR . get_include_path());

			// Path to web dir
		$this->MCONF['PMA_relative_path'] = t3lib_extMgm::extRelPath('phpmyadmin').$this->MCONF['PMA_subdir'];

			// If phpMyAdmin is configured in the conf.php script, we continue to load it...
		if ($this->MCONF['PMA_absolute_path'] && @is_dir($this->MCONF['PMA_absolute_path'])) {

				// Need to have cookie visible from parent directory
			session_set_cookie_params(0, '/', '', 0);

				// Create signon session
			$session_name = 'tx_phpmyadmin';
			session_name($session_name);
			session_start();

				// Store the credentials in the session
			$_SESSION['PMA_single_signon_user'] = TYPO3_db_username;
			$_SESSION['PMA_single_signon_password'] = TYPO3_db_password;
			$_SESSION['PMA_single_signon_host'] = TYPO3_db_host;
			$_SESSION['PMA_single_signon_only_db'] = TYPO3_db;

				// Configure some other parameters
			$_SESSION['PMA_extConf'] = $TYPO3_CONF_VARS['EXT']['extConf']['phpmyadmin'];
			$_SESSION['PMA_hideOtherDBs'] = $extensionConfiguration['hideOtherDBs'];

				// Get signon uri for redirect
			$path_ext = substr($extPath, strlen($typo3DocumentRoot), strlen($extPath));
			$path_ext = (substr($path_ext, 0, 1) != '/'  ? '/'.$path_ext : $path_ext);
			$path_pma = $path_ext.$this->MCONF['PMA_subdir'];
			$_SESSION['PMA_SignonURL'] = $path_pma.'index.php';

				// Try to get the TYPO3 backend uri even if it's installed in a subdirectory
				// Compile logout path and add a slash if the returned string does not start with
			$path_typo3 = substr(PATH_typo3, strlen($typo3DocumentRoot), strlen(PATH_typo3));
			$path_typo3 = (substr($path_typo3, 0, 1) != '/'  ? '/'.$path_typo3 : $path_typo3);
			$_SESSION['PMA_LogoutURL'] = $path_typo3.'logout.php';

				// Prepend document root if uploadDir does not start with a slash "/"
			$extensionConfiguration['uploadDir'] = trim($extensionConfiguration['uploadDir']);
			if (strpos($extensionConfiguration['uploadDir'], '/') !== 0) {
				$_SESSION['PMA_uploadDir'] = $typo3DocumentRoot.'/'.$extensionConfiguration['uploadDir'];
			} else {
				$_SESSION['PMA_uploadDir'] = $extensionConfiguration['uploadDir'];
			}
			$_SESSION['PMA_typo_db'] = TYPO3_db;

				// Check if Ajax is enabled by config - @see http://forge.typo3.org/issues/51384
			$ajaxEnable = (boolean) $extensionConfiguration['ajaxEnable'];
			if ($ajaxEnable === TRUE) {
				$_SESSION['AjaxEnable'] = TRUE;
			} else {
				$_SESSION['AjaxEnable'] = FALSE;
			}

			$id = session_id();

				// Force to set the cookie according to issue #8884
				// http://bugs.typo3.org/view.php?id=8884#c23323
			setcookie($session_name, $id, 0, '/', '');

				// Close that session
			session_write_close();

				// Mapping language keys for phpMyAdmin
			$LANG_KEY_MAP = array(
				'dk'=>'da',
				'de'=>'de',
				'no'=>'no',
				'it'=>'it',
				'fr'=>'fr',
				'es'=>'es',
				'nl'=>'nl',
				'cz'=>'cs-iso',
				'pl'=>'pl',
				'si'=>'sk'
			);

			$LANG_KEY = $LANG_KEY_MAP[$LANG->lang];
			if (!$LANG_KEY) {
				$LANG_KEY = 'en';
			}

				// Redirect to phpMyAdmin (should use absolute URL here!), setting default database
			$redirect_uri = $_SESSION['PMA_SignonURL'].'?lang='.$LANG_KEY.'&db='.urlencode(TYPO3_db);

				// Build and set cache-header header
			$headers = array(
				'Expires: Mon, 26 Jul 1997 05:00:00 GMT',
				'Pragma: no-cache',
				'Cache-Control: private',
				'Location: '.$redirect_uri
			);

				// Send all headers
			foreach($headers as $header)	{
				header($header);
			}

		} else {
				// No configuration set
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;
			$this->content = $this->doc->startPage($LANG->getLL('title'));
			$this->content .= ('
				<h3>phpMyAdmin module was not installed?</h3>
				'.($this->MCONF['PMA_subdir'] && !@is_dir($this->MCONF['PMA_subdir'])?'<hr /><strong>ERROR: The directory, '.$this->MCONF['PMA_subdir'].', was NOT found!</strong><HR>':'').'
			');
			$this->content .= $this->doc->endPage();
		}
	}

	/**
	 * Prints the content of the module directly to the browser
	 *
	 * @return	void
	 */
	public function printContent()	{
		echo $this->content;
	}
}

	// Proceed if TYPO3_MODE is defined
if ( !defined('TYPO3_MODE') ) {
	die ('<h1>Error</h1><p>Unable to determine TYPO3_MODE.</p>');
} else {
	
		// Proceed if BE loaded
	if ( TYPO3_MODE == 'BE' ) {
		
			// Apply access restrictions
		$BE_USER->modAccess($MCONF, 1);

			// Make instance:
		$SOBE = t3lib_div::makeInstance('SC_mod_tools_phpadmin_index');
		$SOBE->main();
		$SOBE->printContent();
		
	} else {
		die ('<h1>Error</h1><p>The TYPO3 Backend is required for phpMyAdmin module but was not loaded.</p>');
	}
}

?>