<?php
/**
 * phpMyAdmin configuration for the tx_phpmyadmin TYPO3 extension
 *
 * @author		mehrwert <typo3@mehrwert.de>
 * @package		TYPO3
 * @subpackage	tx_phpmyadmin
 * @license		GPL
 * @version		$Id: config.inc.php 68078 2012-11-28 23:22:22Z mehrwert $
 */

	// Define the session name
$session_name = 'tx_phpmyadmin';

	// Code taken from phpMyAdmin sign-on script
if (isset($_COOKIE[$session_name])) {

		// End current session
	$old_session = session_name();
	$old_id = session_id();
	session_write_close();

		// Load config session
	session_name($session_name);
	session_id($_COOKIE[$session_name]);
	session_start();

		// Get additional config set in TYPO3
	$extensionConfiguration['PMA_hideOtherDBs'] = $_SESSION['PMA_hideOtherDBs'];
	$extensionConfiguration['PMA_typo_db'] = $_SESSION['PMA_typo_db'];
	$extensionConfiguration['PMA_uploadDir'] = $_SESSION['PMA_uploadDir'];
	$extensionConfiguration['PMA_SignonURL'] = ($_SESSION['PMA_SignonURL'] != '' ? $_SESSION['PMA_SignonURL'] : '../../modsub/index.php');
    $extensionConfiguration['PMA_LogoutURL'] = ($_SESSION['PMA_LogoutURL'] != '' ? $_SESSION['PMA_LogoutURL'] : '/typo3/logout.php');

		// End config session/
	session_write_close();

		// Restart phpMyAdmin session
	session_name($old_session);
	if (!empty($old_id)) {
		session_id($old_id);
	}
	session_start();
}

	// Define upload dir if enabled
if (trim($extensionConfiguration['PMA_uploadDir']) != '' && @file_exists(trim($extensionConfiguration['PMA_uploadDir'])) == 1) {
	// Directories for saving/loading files from server
	$cfg['UploadDir'] = trim($extensionConfiguration['PMA_uploadDir']);
	$cfg['SaveDir'] = trim($extensionConfiguration['PMA_uploadDir']);
}

// Servers configuration
$i = 0;

// First server
$i++;

/* Servers configuration */
$i = 0;

/* Server TYPO3 Database Server (signon) [1] */
$i++;

// $cfg['Servers'][$i]['pmadb'] = $extensionConfiguration['PMA_typo_db'];
// $cfg['Servers'][$i]['bookmarktable'] = 'tx_phpmyadmin_bookmark';
// $cfg['Servers'][$i]['relation'] = 'tx_phpmyadmin_relation';
// $cfg['Servers'][$i]['table_info'] = 'tx_phpmyadmin_table_info';
// $cfg['Servers'][$i]['table_coords'] = 'tx_phpmyadmin_table_coords';
// $cfg['Servers'][$i]['pdf_pages'] = 'tx_phpmyadmin_pdf_pages';
// $cfg['Servers'][$i]['column_info'] = 'tx_phpmyadmin_column_info';
// $cfg['Servers'][$i]['history'] = 'tx_phpmyadmin_history';
// $cfg['Servers'][$i]['tracking'] = 'tx_phpmyadmin_tracking';
// $cfg['Servers'][$i]['designer_coords'] = 'tx_phpmyadmin_designer_coords';

// $cfg['Servers'][$i]['controluser'] = '';
// $cfg['Servers'][$i]['controlpass'] = '';

$cfg['Servers'][$i]['extension'] = 'mysql';
$cfg['Servers'][$i]['connect_type'] = 'tcp';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['auth_type'] = 'signon';
$cfg['Servers'][$i]['only_db'] = ( $extensionConfiguration['PMA_hideOtherDBs'] == 1 ? $extensionConfiguration['PMA_typo_db'] : '' );
$cfg['Servers'][$i]['verbose'] = 'TYPO3 DB Host';
$cfg['Servers'][$i]['pmadb'] = 'tx_phpmyadmin';
$cfg['Servers'][$i]['nopassword'] = true;
$cfg['Servers'][$i]['AllowNoPasswordRoot'] = true;
$cfg['Servers'][$i]['AllowNoPassword'] = true;
$cfg['Servers'][$i]['auth_type'] = 'signon';
$cfg['Servers'][$i]['SignonSession'] = 'tx_phpmyadmin';
$cfg['Servers'][$i]['SignonURL'] = $extensionConfiguration['PMA_SignonURL'];
$cfg['Servers'][$i]['LogoutURL'] = $extensionConfiguration['PMA_LogoutURL'];

/* End of servers configuration */

$cfg['ThemeDefault'] = 'pmahomme';
$cfg['LeftFrameLight'] = true;
$cfg['LeftFrameDBTree'] = true;
$cfg['LeftFrameDBSeparator'] = '_';
$cfg['LeftFrameTableSeparator'] = '__';
$cfg['LeftFrameTableLevel'] = 1;
$cfg['LeftDisplayLogo'] = false;
$cfg['LeftDisplayServers'] = false;
$cfg['LeftDefaultTabTable'] = 'sql.php';
$cfg['DisplayServersList'] = false;
$cfg['DisplayDatabasesList'] = 'auto';
$cfg['LeftPointerEnable'] = true;
$cfg['DefaultTabServer'] = 'main.php';
$cfg['DefaultTabDatabase'] = 'db_structure.php';
$cfg['DefaultTabTable'] = 'tbl_structure.php';
$cfg['LightTabs'] = false;
$cfg['ErrorIconic'] = true;
$cfg['MainPageIconic'] = true;
$cfg['ReplaceHelpImg'] = true;
$cfg['NavigationBarIconic'] = 'both';
$cfg['PropertiesIconic'] = true;
$cfg['BrowsePointerEnable'] = true;
$cfg['BrowseMarkerEnable'] = true;
$cfg['ModifyDeleteAtRight'] = false;
$cfg['ModifyDeleteAtLeft'] = true;
$cfg['RepeatCells'] = 100;
$cfg['DefaultDisplay'] = 'horizontal';
$cfg['TextareaCols'] = 40;
$cfg['TextareaRows'] = 7;
$cfg['LongtextDoubleTextarea'] = true;
$cfg['TextareaAutoSelect'] = false;
$cfg['CharEditing'] = 'input';
$cfg['CharTextareaCols'] = 40;
$cfg['CharTextareaRows'] = 2;
$cfg['CtrlArrowsMoving'] = true;
$cfg['DefaultPropDisplay'] = 'horizontal';
$cfg['InsertRows'] = 2;
$cfg['EditInWindow'] = true;
$cfg['QueryWindowHeight'] = 510;
$cfg['QueryWindowWidth'] = 640;
$cfg['QueryWindowDefTab'] = 'sql';

?>