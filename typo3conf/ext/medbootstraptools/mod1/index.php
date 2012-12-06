<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Raphael Zschorsch <zschorsch@medialis.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

// PHP to MySQL Importer
require_once(PATH_typo3conf . 'ext/medbootstraptools/mod1/phpmyimporter/phpMyImporter.php');

$GLOBALS['LANG']->includeLLFile('EXT:medbootstraptools/mod1/locallang.xml');
//require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$GLOBALS['BE_USER']->modAccess($MCONF, 1);    // This checks permissions and exits if the users has no permission for entry.
// DEFAULT initialization of a module [END]

/**
 * Module 'T3Bootstrap Config' for the 'medbootstraptools' extension.
 *
 * @author    Raphael Zschorsch <zschorsch@medialis.net>
 * @package    TYPO3
 * @subpackage    tx_medbootstraptools
 */
class tx_medbootstraptools_module1 extends t3lib_SCbase {

    protected $pageinfo;

    /**
     * Initializes the module.
     *
     * @return void
     */
    public function init() {
        parent::init();

        /*
          if (t3lib_div::_GP('clear_all_cache'))    {
          $this->include_once[] = PATH_t3lib . 'class.t3lib_tcemain.php';
          }
         */
    }

    /**
     * Adds items to the ->MOD_MENU array. Used for the function menu selector.
     *
     * @return    void
     */
    public function menuConfig() {
        $this->MOD_MENU = array(
            'function' => array(
                '1' => $GLOBALS['LANG']->getLL('function1')
            )
        );
        parent::menuConfig();
    }

    /**
     * Main function of the module. Write the content to $this->content
     * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
     *
     * @return void
     */
    public function main() {
        // Access check!
        // The page will show only if there is a valid page and if this page may be viewed by the user
        $this->pageinfo = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
        $access = is_array($this->pageinfo) ? 1 : 0;

        if (($this->id && $access) || ($GLOBALS['BE_USER']->user['admin'] && !$this->id)) {

            // Draw the header.
            $this->doc = t3lib_div::makeInstance('bigDoc');
            $this->doc->getPageRenderer()->addCssFile(t3lib_extMgm::extRelPath('medbootstraptools').'res/css/bemodul.css');  
            $this->doc->getPageRenderer()->addCssFile(t3lib_extMgm::extRelPath('medbootstraptools').'res/bootstrap/css/bootstrap.min.css');  
            $this->doc->backPath = $GLOBALS['BACK_PATH'];
            $this->doc->form = '<form action="" method="post" enctype="multipart/form-data">';

            // JavaScript
            $this->doc->JScode = '
                <script src="../typo3conf/ext/medbootstraptools/res/js/jquery-1.8.3.min.js"></script>
                <script src="../typo3conf/ext/medbootstraptools/res/js/functions.js"></script>
                <script language="javascript" type="text/javascript">
                    script_ended = 0;
                    function jumpToUrl(URL)    {
                        document.location = URL;
                    }
                </script>
            ';
            $this->doc->postCode = '
                <script language="javascript" type="text/javascript">
                    script_ended = 1;
                    if (top.fsMod) top.fsMod.recentIds["web"] = 0;
                </script>
            ';

            $headerSection = $this->doc->getHeader('pages', $this->pageinfo, $this->pageinfo['_thePath']) . '<br />'
                    . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.path') . ': ' . t3lib_div::fixed_lgd_cs($this->pageinfo['_thePath'], -50);

            $this->content .= $this->doc->startPage($GLOBALS['LANG']->getLL('title'));
            //$this->content .= $this->doc->header($GLOBALS['LANG']->getLL('title'));
            //$this->content .= $this->doc->spacer(5);
            //$this->content .= $this->doc->section('', $this->doc->funcMenu($headerSection, t3lib_BEfunc::getFuncMenu($this->id, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function'])));
            //$this->content .= $this->doc->divider(5);
            // Render content:
            $this->moduleContent();

            // Shortcut
            if ($GLOBALS['BE_USER']->mayMakeShortcut()) {
                $this->content .= $this->doc->spacer(20) . $this->doc->section('', $this->doc->makeShortcutIcon('id', implode(',', array_keys($this->MOD_MENU)), $this->MCONF['name']));
            }

            $this->content .= $this->doc->spacer(10);
        } else {
            // If no access or if ID == zero

            $this->doc = t3lib_div::makeInstance('bigDoc');
            $this->doc->backPath = $GLOBALS['BACK_PATH'];

            $this->content .= $this->doc->startPage($GLOBALS['LANG']->getLL('title'));
            $this->content .= $this->doc->header($GLOBALS['LANG']->getLL('title'));
            $this->content .= $this->doc->spacer(5);
            $this->content .= $this->doc->spacer(10);
        }
    }

    /**
     * Prints out the module HTML.
     *
     * @return void
     */
    public function printContent() {
        $this->content .= $this->doc->endPage();
        echo $this->content;
    }

    /**
     * Generates the module content.
     *
     * @return void
     */
    protected function moduleContent() {
        switch ((string) $this->MOD_SETTINGS['function']) {
            case 1:
                // Get default project name
                $path = PATH_site . 'fileadmin/templates/';
                $dirs = scandir($path);

                // Filter directories
                foreach ($dirs as $dir) {
                    if ($dir != '.' && $dir != '..' && $dir != 'default' && $dir != 'ts')
                        $projectDir = $dir;
                }

                // Form submitted
                if ($_POST['submit_config']) {    
                	// No basedomain given
                	if(!$_POST['project_basedomainde']) {
                		$errorMessageContent = '<h3>'.$GLOBALS['LANG']->getLL('noBasedomain').'</h3>';
                		$errorMessageContent .= '<p>'.$GLOBALS['LANG']->getLL('noBasedomainText').'</p>';
	                    $content = '<div class="alert alert-error">'.$errorMessageContent.'</div>';
	                    $this->content .= $this->doc->section($GLOBALS['LANG']->getLL('title'), $content, 0, 1);	                		
                	}   
                	else if(!$_POST['project_email']) {
                		$errorMessageContent = '<h3>'.$GLOBALS['LANG']->getLL('noEmail').'</h3>';
                		$errorMessageContent .= '<p>'.$GLOBALS['LANG']->getLL('noEmailText').'</p>';
	                    $content = '<div class="alert alert-error">'.$errorMessageContent.'</div>';
	                    $this->content .= $this->doc->section($GLOBALS['LANG']->getLL('title'), $content, 0, 1);	                	
                	}
                	else if(!$this->checkEmail($_POST['project_email'])) {
                		$errorMessageContent = '<h3>'.$GLOBALS['LANG']->getLL('noValidEmail').'</h3>';
                		$errorMessageContent .= '<p>'.$GLOBALS['LANG']->getLL('noValidEmailText').'</p>';
	                    $content = '<div class="alert alert-error">'.$errorMessageContent.'</div>';
	                    $this->content .= $this->doc->section($GLOBALS['LANG']->getLL('title'), $content, 0, 1);	                	
                	}
                	else {         	
	                    // Get project name
	                    $projectName = trim(strtolower($_POST['project_name']));
	                    
	                    // Check if uppercase
	                    if(ctype_upper($_POST['project_name'])) {
		                	$projectNameUpper = $_POST['project_name']; 
		                	$projectDirUpper = strtoupper($projectDir);   
	                    }
	                    else {
		                    $projectNameUpper = $projectName;
		                    $projectDirUpper = $projectDir; 
	                    }
	                    	
	                    // Rename dir
	                    rename($path . $projectDir, $path . $projectName);
	
	                    /* Change files BEGIN */
	                    
	                    // Files to change
	                    $files = array(
	                        PATH_site . 'fileadmin/templates/ts/setup/JavaScriptIncludes_setup.ts',
	                        PATH_site . 'fileadmin/templates/ts/TSConfig/Page.ts',
	                        PATH_site . 'typo3conf/localconf.php'
	                    );
	
	                    // Parse files
	                    foreach ($files as $f) {
	                        // Open file
	                        $data = file_get_contents($f);
	
	                        // Change data
	                        $data = str_replace($projectDir, $projectName, $data);
	
	                        // Write file
	                        file_put_contents($f, $data);
	                    }
	                    
	                    /* Change files END */
	                    
	                    /* Change files with uppercase BEGIN */
	                    
	                    // Files to change
	                    $files2 = array(
	                        PATH_site . 'fileadmin/templates/ts/setup/lib_setup.ts',
	                        PATH_site . 'fileadmin/templates/ts/constants/StandardConfig_constants.ts',
	                        PATH_site . 'fileadmin/templates/ts/setup/lib_setup.ts',
	                    );
	                    
	                    // Parse files
	                    foreach ($files2 as $f2) {
	                        // Open file
	                        $data2 = file_get_contents($f2);
	
	                        // Change data
	                        $data2 = str_replace(ucfirst($projectDir), ucfirst($projectNameUpper), $data2);
	
	                        // Write file
	                        file_put_contents($f2, $data2);
	                    }                    
	                    
	                    /* Change files with uppercase END */
	                    
	                    /* Copyright notice BEGIN */
	                    
	                    $copyrightNotice = $_POST['project_copyright'];
	                    if(!$copyrightNotice) $copyrightNotice = $GLOBALS['LANG']->getLL('copyrightDefault');
	                    
	                    $files3 = array(
	                    	PATH_site . 'fileadmin/templates/ts/setup/StandardConfig_setup.ts',
	                    );
	                    
	                    foreach($files3 as $f3) {
		                    // Open file
		                    $data3 = file_get_contents($f3);
		                    
		                    // Change data
		                    $data3 = str_replace("headerComment =","headerComment = ".$copyrightNotice,$data3);
		                    
		                    // Write file
		                    file_put_contents($f3, $data3);
	                    }
	                    
	                    /* Copyright notice END */
	                    
	                    /* Basedomain BEGIN */
	                    
	                    $files4 = array(
	                    	PATH_site . 'fileadmin/templates/ts/constants/StandardConfig_constants.ts',
	                    );
	                    
	                    foreach($files4 as $f4) {
	                    	$basedomainDE = trim($_POST['project_basedomainde'],'/').'/';
	                    	$basedomainEN = trim($_POST['project_basedomainen'],'/').'/';
	                    
		                    // Open file
		                    $data4 = file_get_contents($f4);
		                    
// Add data
$data4 = "".$data4."

# # medbootstraptools [BEGIN]

t3bootstrap {
\tbasedomain.de = ".$basedomainDE."
\tbasedomain.en = ".$basedomainEN."
}

# # medbootstraptools [END]";
		                    
		                    // Write file
		                    file_put_contents($f4, $data4);
	                    }
	                    
	                    /* Basedomain END */
	                    
	                    /* Responsive or not BEGIN */
	                    
	                    if ($_POST['project_responsive'] != "on") {
	                        $resp = PATH_site . 'fileadmin/templates/ts/setup/CSSIncludes_setup.ts';
	                        
	                        $dataResp = file_get_contents($resp);
	                        $dataResp = str_replace("bootstrap-responsive","no-responsive",$dataResp);
	                        
	                        file_put_contents($resp, $dataResp);
	                        
	                        // Rename t3bootstrap responsive
	                        $t3bootstrapResp = PATH_site . 'fileadmin/templates/default/less/t3bootstrap-responsive.less';
	                        rename($t3bootstrapResp,$t3bootstrapResp.'_doNotUse');
	                        
		                    // File
		                    $lessConfigFile = PATH_site . 'fileadmin/templates/ts/setup/Extensions_setup.ts';
		                    
		                    // Get content
		                    $lessConfigFileContent = file_get_contents($lessConfigFile);
		                    
		                    // Remove LESS config for responsive CSS file
		                    $lessConfigFileContent = preg_replace('/t3bootstrap-responsive {[^{}]*}/', '', $lessConfigFileContent);	
		                    
		                    // Write file
		                    file_put_contents($lessConfigFile, $lessConfigFileContent);                        
	                    }
	                    
	                    /* Responsive or not END */
	
	                    /* Install Tool password BEGIN */
	
	                    $localconfFile = PATH_site . 'typo3conf/localconf.php';
	                    $localconfData = file_get_contents($localconfFile);
	
	                    $newInstallPassword = $this->generatePW();
	
	                    $localConfContent = "// Updated by medbootstraptools " . date("d.m.y", time()) . " " . date("H:i:s", time()) . "\n\$TYPO3_CONF_VARS['BE']['installToolPassword'] = '" . md5($newInstallPassword) . "';";
	
	                    $localconfData = str_replace("?>", "\n" . $localConfContent . "\n?>", $localconfData);
	                    file_put_contents($localconfFile, $localconfData);
	
	                    /* Install Tool password END */
	                    
	                    /* Update site name BEGIN */
	                    
	                    // Get localconf
	                    $data = file_get_contents($localconfFile);
	                    
	                    $data = str_replace("\$TYPO3_CONF_VARS['SYS']['sitename'] = '".ucfirst($projectDir)."';", "\$TYPO3_CONF_VARS['SYS']['sitename'] = '".ucfirst($projectNameUpper)."';", $data);
	
	                    // Write file
	                    file_put_contents($localconfFile, $data);                    
	                    
	                    /* Update site name END */	                    
	                    
	                    /* Settings LIVE/PREVIEW server BEGIN */
	                    
	                    // Get file
	                    $settingsFile = PATH_typo3conf . 'settings.php';
	                    
	                    // Get settings
	                    $server = $_POST['live_server'];
	                    $host = $_POST['live_host'];
	                    $username = $_POST['live_username'];
	                    $dbPassword = $_POST['live_password'];
	                    $database = $_POST['live_database'];
	                    $imPath = $_POST['live_impath'];
	                    
	                    $previewServer = $_POST['preview_server'];
	                    $previewHost = $_POST['preview_host'];
	                    $previewUsername = $_POST['preview_username'];
	                    $previewDbPassword = $_POST['preview_password'];
	                    $previewDatabase = $_POST['preview_database'];	 
	                    $previewImPath = $_POST['preview_impath'];                   
	                    
	                    // Get content
	                    if(!$server && !$host && !$username && !$dbPassword && !$database && !$previewServer && !$previewHost && !$previewUsername && !$previewDbPassword && !$previewDatabase && !$imPath && !$previewImPath) {
$settingsContent = "<?php
\$TYPO3_CONF_VARS['GFX']['im_path_lzw'] = '/usr/local/bin/';
\$TYPO3_CONF_VARS['GFX']['im_path'] = '/usr/local/bin/';
?>";
	                    }
	                    else {
$settingsContent = "<?php
\tif(\$_SERVER['SERVER_NAME'] == '".$server."') {
\t\t\$typo_db_username = '".$username."';
\t\t\$typo_db_password = '".$dbPassword."';
\t\t\$typo_db_host = '".$host."';
\t\t\$typo_db = '".$database."';
\t\t\$TYPO3_CONF_VARS['GFX']['im_path_lzw'] = '".$imPath."';
\t\t\$TYPO3_CONF_VARS['GFX']['im_path'] = '".$imPath."';
\t}
\telse if(\$_SERVER['SERVER_NAME'] == '".$previewServer."') {
\t\t\$typo_db_username = '".$previewUsername."';
\t\t\$typo_db_password = '".$previewDbPassword."';
\t\t\$typo_db_host = '".$previewHost."';
\t\t\$typo_db = '".$previewDatabase."';
\t\t\$TYPO3_CONF_VARS['GFX']['im_path_lzw'] = '".$previewImPath."';
\t\t\$TYPO3_CONF_VARS['GFX']['im_path'] = '".$previewImPath."';
\t}
?>";
	                    }
	                    
	                    file_put_contents($settingsFile, $settingsContent);	                    
	                    
	                    /* Settings LIVE/PREVIEW server END */                 
	                    
	                    /* Import database BEGIN */
	                    
	                    /**
	                    * @todo Is there another way to get the database connection values from localconf.php
	                    * @todo Replace @mysql_connect, as TYPO3 Backend is already connected; change import script class
	                    */
	                    
	                    // Include localconf to get database settings
	                	include(PATH_typo3conf . 'localconf.php');
	                	
	                	// Connect to database
	                	$connection = @mysql_connect($typo_db_host,$typo_db_username,$typo_db_password);
	                		                	
	                	// Get SQL file
	                	$filename = PATH_typo3conf.'ext/medbootstraptools/mod1/sql/t3bootstrap.sql';
	                	$compress = false;
	                	
						$dump = new phpMyImporter($typo_db,$connection,$filename,$compress);
						$dump->utf8 = true; // Uses UTF8 connection with MySQL server, default: true                  
						
						$dump->doImport();
						
						/* Clear sys_log and be_sessions table after import [BEGIN] */
						
						mysql_query("TRUNCATE TABLE sys_log"); 
						//mysql_query("TRUNCATE TABLE be_sessions"); 
						
						/* Clear sys_log and be_sessions table after import [END] */
						
	                    /* Import database END */
	                    
	                    /* Update contact form BEGIN */
	                    
	                    $email = $_POST['project_email'];	                    
	                    
	                    $GLOBALS['TYPO3_DB']->sql_query("UPDATE tt_content SET pi_flexform = REPLACE(pi_flexform, 'your@email.de', '".$email."') WHERE uid=103");	                    
	                    $GLOBALS['TYPO3_DB']->sql_query("UPDATE tt_content SET pi_flexform = REPLACE(pi_flexform, '".ucfirst($projectDirUpper)."', '".ucfirst($projectNameUpper)."') WHERE uid=103");	                    
	                    
	                    /* Update contact form END */	  
	                   
	                    /* Templavoilà BEGIN */
	
	                    $GLOBALS['TYPO3_DB']->sql_query("UPDATE tx_templavoila_datastructure SET belayout = REPLACE(belayout, '" . $projectDir . "', '" . $projectName . "') WHERE uid=1");
	                    $GLOBALS['TYPO3_DB']->sql_query("UPDATE tx_templavoila_tmplobj SET fileref = REPLACE(fileref, '" . $projectDir . "', '" . $projectName . "') WHERE uid=1");
	                    $GLOBALS['TYPO3_DB']->sql_query("UPDATE tx_templavoila_tmplobj SET fileref_md5 = MD5(fileref) WHERE uid=1");
	                    
	                    /* Templavoilà BEGIN */
	
	                    /* Update page ID 1 BEGIN */
	
	                    $updateArrayMod = array(
	                        //'tx_medbootstraptools_bootstrapconfig' => 1,
	                        'title' => ucfirst($projectNameUpper)
	                    );
	
	                    $resMod = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('pages', 'uid=1', $updateArrayMod);
	
	                    /* Update page ID 1  END */
	                   
	                    /* Update user group ID 2 BEGIN */
	                    
	                    $updateArrayUser = array(
	                        'title' => ucfirst($projectNameUpper).' '.$GLOBALS['LANG']->getLL('admin'),
	                        'description' => ucfirst($projectNameUpper).' '.$GLOBALS['LANG']->getLL('adminUserGroup')
	                    );
	
	                    $resMod = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('be_groups', 'uid=2', $updateArrayUser);                    
	                    
	                    /* Update user group ID 2 END */             
	                    
	                    /* Update user group ID 3 BEGIN */
	                    
	                    $updateArrayUser2 = array(
	                        'title' => ucfirst($projectNameUpper).' '.$GLOBALS['LANG']->getLL('editor'),
	                        'description' => ucfirst($projectNameUpper).' '.$GLOBALS['LANG']->getLL('editorUserGroup')
	                    );
	
	                    $resMod2 = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('be_groups', 'uid=3', $updateArrayUser2);                    
	                    
	                    /* Update user group ID 3 END */	 
	                    
	                    /* Create backend users BEGIN */
	                    
	                    $beUsers = explode("\n",trim($_POST['project_beusers']));
	                    if($_POST['project_beusers']) {
		                    $beUsersFinal = array();
		                    foreach($beUsers as $beUser) {
			                	$beUsersFinal[] = trim($beUser);    
		                    }
		                    
		                    $beUsers = $beUsersFinal;
		                    
		                    foreach($beUsers as $beUser) {
		                    	$userData = explode(",",$beUser);
		                    	
								$insertArray = array(						
									'username' => trim(str_replace('"','',stripslashes($userData[0]))),	
									'admin' => trim(str_replace('"','',stripslashes($userData[5]))),
									'realName' => trim(str_replace('"','',stripslashes($userData[1]))),
									'email' => trim(str_replace('"','',stripslashes($userData[2]))),
									'lang' => trim(str_replace('"','',stripslashes($userData[3]))),
									'tstamp' => time(),
									'crdate' => time(),
									'usergroup' => trim(str_replace('"','',stripslashes($userData[4])))				
								);
								
								$resBeUser = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_users', $insertArray);                    
		                    }
	                    }
	                    
	                    /* Create backend users END */
	
	                    /* Backend user passwords BEGIN */
	
	                    // Create 10 passwords
	                    $passwordArr = array();
	                    for ($i = 1; $i <= 10; $i++) {
	                        $passwordArr[] = $this->generatePW();
	                    }
	
	                    // Get all be_users
	                    $resUsers = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,username', 'be_users', 'disable=0 AND deleted=0', '', '', '');
	                    $i = 0;
	                    $passwordArrWithUsername = array();
	                    while ($rowUsers = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resUsers)) {
	                        $passwordArrWithUsername[$rowUsers['username']] = $passwordArr[$i];
	
	                        // Create salted password
	                        $password = $passwordArr[$i]; // plain-text password
	                        $saltedPassword = '';
	                        if (t3lib_extMgm::isLoaded('saltedpasswords')) {
	                            if (tx_saltedpasswords_div::isUsageEnabled('FE')) {
	                                $objSalt = tx_saltedpasswords_salts_factory::getSaltingInstance(NULL);
	                                if (is_object($objSalt)) {
	                                    $saltedPassword = $objSalt->getHashedPassword($password);
	                                }
	                            }
	                        }
	
	                        $updateArray = array(
	                            'password' => $saltedPassword
	                        );
	
	                        $res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('be_users', 'uid=' . $rowUsers['uid'], $updateArray);
	
	                        $i++;
	                    }
	
	                    /* Backend user passwords END */	                                      
	
	                    // Success message
	                    $successMessageContent = '<h3>'.$GLOBALS['LANG']->getLL('configSaved').'</h3>';
	                    $successMessageContent .= '<p><br /><b>'.$GLOBALS['LANG']->getLL('backendPasses').'</b><br />';
	                    $c = 0;
	                    foreach ($passwordArrWithUsername as $pKey => $pVal) {
	                        if ($c == 0) {
	                            $successMessageContent .= $pKey . ': ' . $pVal;
	                        } else {
	                            $successMessageContent .= '<br />' . $pKey . ': ' . $pVal;
	                        }
	
	                        $c++;
	                    }
	                    $successMessageContent .= '</p>';
	                    $successMessageContent .= '<p><br /><b>'.$GLOBALS['LANG']->getLL('installToolPassword').'</b><br />' . $newInstallPassword . '</p>';
	                    // Import SQL
	                    $successMessageContent .= '<p><br /><b>'.$GLOBALS['LANG']->getLL('database').'</b><br />'.$GLOBALS['LANG']->getLL('databaseSuccess').'</p>';  
	                    
	                    $content = '<div class="alert alert-success">'.$successMessageContent.'</div>';
	                    $this->content .= $this->doc->section($GLOBALS['LANG']->getLL('title'), $content, 0, 1);
	                 }
	                } else {
	                    // Check if module has already been deactivated
	                    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_medbootstraptools_bootstrapconfig','pages','uid=1','','','');
	                    $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
	                    
	                    if($row['tx_medbootstraptools_bootstrapconfig'] == 1) {
	                        $content = '<p><b>'.$GLOBALS['LANG']->getLL('configAlready').'</b></p>';
	                    }

                    else {
                        $content = '
                            <form method="post" action="">
                            	<div class="settings">
	                            	<h4>'.$GLOBALS['LANG']->getLL('generalSettings').'</h4>
	                            
	                                <label>'.$GLOBALS['LANG']->getLL('projectName').'</label>
	                                <input type="text" name="project_name" value="' . $projectDir . '">
	                                    
	                                <label>'.$GLOBALS['LANG']->getLL('basedomainDE').'</label>
	                                <input type="text" placeholder="http://subdomain.domain.de/" name="project_basedomainde" class="input-middle"> 
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('basedomainEN').'</label>
	                                <input type="text" placeholder="http://subdomain.domain.de/en/" name="project_basedomainen" class="input-middle">     
	                                    
	                                <label>'.$GLOBALS['LANG']->getLL('copyrightNotice').'</label>
	                                <input type="text" class="input-long" placeholder="'.$GLOBALS['LANG']->getLL('copyrightDefault').'" name="project_copyright">     
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('email').'</label>
	                                <input type="text" name="project_email" placeholder="your@email.de">
	                                    
	                                <label>'.$GLOBALS['LANG']->getLL('adminUser').' <i>'.$GLOBALS['LANG']->getLL('adminUserInfo').'</i>:</label>
	                                <textarea cols="5" rows="10" class="textarea-beusers" name="project_beusers"></textarea>
	                                    
	                                <label>'.$GLOBALS['LANG']->getLL('responsive').'</label>
	                                <input type="checkbox" name="project_responsive" checked="checked">                         
	                                
	                                <h4>'.$GLOBALS['LANG']->getLL('databaseConnectionPreview').'</h4>
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('serverName').'</label>
	                                <input type="text" name="preview_server" placeholder="domain.de">                              
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('username').'</label>
	                                <input type="text" name="preview_username" autocomplete="off">
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('password').'</label>
	                                <input type="password" name="preview_password" autocomplete="off">  
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('host').'</label>
	                                <input type="text" name="preview_host">   
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('database').'</label>
	                                <input type="text" name="preview_database"> 
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('imageMagickPath').'</label>
	                                <input type="text" name="preview_impath" placeholder="/usr/local/bin/">                                                                  
	                                
	                                <h4>'.$GLOBALS['LANG']->getLL('databaseConnection').'</h4>
	                              
	                                <label>'.$GLOBALS['LANG']->getLL('serverName').'</label>
	                                <input type="text" name="live_server" placeholder="domain.de">                              
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('username').'</label>
	                                <input type="text" name="live_username" autocomplete="off">
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('password').'</label>
	                                <input type="password" name="live_password" autocomplete="off">  
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('host').'</label>
	                                <input type="text" name="live_host">   
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('database').'</label>
	                                <input type="text" name="live_database">     
	                                
	                                <label>'.$GLOBALS['LANG']->getLL('imageMagickPath').'</label>
	                                <input type="text" name="live_impath" placeholder="/usr/local/bin/">                                                                                                                        
	                                
	                                <input type="hidden" name="submit_config" value="1">
	                                <p><a href="javascript:void(0);" class="btn btn-primary btn-submit">'.$GLOBALS['LANG']->getLL('save').'</a></p>
                                </div>
                            </form>
                        ';
                    }
                    $this->content .= $this->doc->section($GLOBALS['LANG']->getLL('title'), $content, 0, 1);
                }
                break;
        }
    }

    private function generatePW($length = 8) {
        $dummy = array_merge(range('0', '9'), range('a', 'z'), range('A', 'Z'), array('#', '&', '@', '$', '_', '%', '?', '+'));

        // shuffle array
        mt_srand((double) microtime() * 1000000);

        for ($i = 1; $i <= (count($dummy) * 2); $i++) {
            $swap = mt_rand(0, count($dummy) - 1);
            $tmp = $dummy[$swap];
            $dummy[$swap] = $dummy[0];
            $dummy[0] = $tmp;
        }

        // get password
        return substr(implode('', $dummy), 0, $length);
    }
    
    private function checkEmail($email) {
        if (preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-.]+\.([a-zA-Z]{2,4})$/", $email)) {
            return true;
        } else {
            return false;
        }
    }

    private function debug($var) {
        t3lib_utility_Debug::debug($var);
    }

}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/medbootstraptools/mod1/index.php'])) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/medbootstraptools/mod1/index.php']);
}




// Make instance:
/** @var $SOBE tx_medbootstraptools_module1 */
$SOBE = t3lib_div::makeInstance('tx_medbootstraptools_module1');
$SOBE->init();

// Include files?
foreach ($SOBE->include_once as $INC_FILE) {
    include_once($INC_FILE);
}

$SOBE->main();
$SOBE->printContent();
?>