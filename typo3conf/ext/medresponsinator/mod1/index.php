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


$GLOBALS['LANG']->includeLLFile('EXT:medresponsinator/mod1/locallang.xml');
//require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$GLOBALS['BE_USER']->modAccess($MCONF, 1); // This checks permissions and exits if the users has no permission for entry.
// DEFAULT initialization of a module [END]

/**
 * Module 'Responsinator' for the 'medresponsinator' extension.
 *
 * @author	Raphael Zschorsch <zschorsch@medialis.net>
 * @package	TYPO3
 * @subpackage	tx_medresponsinator
 */
class tx_medresponsinator_module1 extends t3lib_SCbase {

    protected $pageinfo;

    /**
     * Initializes the module.
     *
     * @return void
     */
    public function init() {
        parent::init();

        /*
          if (t3lib_div::_GP('clear_all_cache'))	{
          $this->include_once[] = PATH_t3lib . 'class.t3lib_tcemain.php';
          }
         */
    }

    /**
     * Adds items to the ->MOD_MENU array. Used for the function menu selector.
     *
     * @return	void
     */
    public function menuConfig() {
        $this->MOD_MENU = array(
            'function' => array(
                '1' => $GLOBALS['LANG']->getLL('function1'),
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
            $this->doc->styleSheetFile2 = $GLOBALS["temp_modPath"] . '../typo3conf/ext/medresponsinator/res/css/bemodul.css';
            $this->doc->backPath = $GLOBALS['BACK_PATH'];
            $this->doc->form = '<form action="" method="post" enctype="multipart/form-data">';

            // JavaScript
            $this->doc->JScode = '
                <script src="../typo3conf/ext/medresponsinator/res/js/jquery-1.8.2.min.js" type="text/javascript"></script>
                <script src="../typo3conf/ext/medresponsinator/res/js/functions.js" type="text/javascript"></script>
                <script language="javascript" type="text/javascript">
                        script_ended = 0;
                        function jumpToUrl(URL)	{
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
                // Get page ID
                $pid = $this->id;

                if ($pid != 0) {
                	$url = 't3bootstrap.medialis-hosting.net';
                	
                	// Get base URL
					$TS = $this->loadTS('1');
					
					if(!$TS['config.']['baseURL']) { 
						$baseURL = 'http://'.$_SERVER['SERVER_NAME'].'/';
					} 
					else {
						$baseURL = $TS['config.']['baseURL'];
					}    
             	
                    $content = '<div class="responsinator"><iframe src="http://responsinator.com/?url='.$baseURL.'index.php?id='.$pid.'" frameborder="0" framespacing="0" style="height:100%; width:100%;"></iframe></div>';
                }
                
                else {
                    $content = '<p><b>'.$GLOBALS['LANG']->getLL('choose_page').'</b></p>';
                }

                $this->content .= $this->doc->section('', $content, 0, 1);
                break;
        }
    }
    
    protected function loadTS($pageUid) {
        $sysPageObj = t3lib_div::makeInstance('t3lib_pageSelect');
        $rootLine = $sysPageObj->getRootLine($pageUid);
        $TSObj = t3lib_div::makeInstance('t3lib_tsparser_ext');
        $TSObj->tt_track = 0;
        $TSObj->init();
        $TSObj->runThroughTemplates($rootLine);
        $TSObj->generateConfig();
	    
	    return $TSObj->setup;
    } 

}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/medresponsinator/mod1/index.php'])) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/medresponsinator/mod1/index.php']);
}




// Make instance:
/** @var $SOBE tx_medresponsinator_module1 */
$SOBE = t3lib_div::makeInstance('tx_medresponsinator_module1');
$SOBE->init();

// Include files?
foreach ($SOBE->include_once as $INC_FILE) {
    include_once($INC_FILE);
}

$SOBE->main();
$SOBE->printContent();
?>