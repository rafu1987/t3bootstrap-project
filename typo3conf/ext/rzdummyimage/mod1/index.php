<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Raphael Zschorsch <rafu1987@gmail.com>
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
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


$LANG->includeLLFile('EXT:rzdummyimage/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'Dummy Image' for the 'fr_dummy_image_mod' extension.
 *
 * @author	Raphael Zschorsch <rafu1987@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_frdummyimage_mod
 */
class  tx_rzdummyimage_module1 extends t3lib_SCbase {
				var $pageinfo;

				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					parent::init();
                    $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rzdummyimage']);
                    $store_path = $this->extConf['storePath']; 
                    if($store_path == '') $store_path = 'fileadmin/user_upload/rzdummyimage';

                    t3lib_div::mkdir(PATH_site . $store_path);
				}

				/**
				 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
				 *
				 * @return	void
				 */
				function menuConfig()	{
					global $LANG;
					$this->MOD_MENU = Array (
						'function' => Array (
							'1' => $LANG->getLL('title'),
							'2' => $LANG->getLL('about'),
						)
					);
					parent::menuConfig();
				}

				/**
				 * Main function of the module. Write the content to $this->content
				 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
				 *
				 * @return	[type]		...
				 */
				function main()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
					$access = is_array($this->pageinfo) ? 1 : 0;
				
							// Draw the header.
						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;

						$headerSection = $this->doc->getHeader('pages', $this->pageinfo, $this->pageinfo['_thePath']) . '<br />'
							. $LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path') . ': ' . t3lib_div::fixed_lgd_cs($this->pageinfo['_thePath'], -50);

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->divider(5);


						// Render content:
						$this->moduleContent();
				
				}

				/**
				 * Prints out the module HTML
				 *
				 * @return	void
				 */
				function printContent()	{
					echo '<div style="padding-left:15px;">'.$this->content.'<div>';
				}

				/**
				 * Generates the module content
				 *
				 * @return	void
				 */
				function moduleContent()	{
                    global $LANG;
					switch((string)$this->MOD_SETTINGS['function'])	{
						case 1:
                            // Extconf
                            $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rzdummyimage']);
                            $conf_width = $this->extConf['width'];
                            $conf_height = $this->extConf['height'];
                            $conf_textcolor = $this->extConf['textColor'];
                            $conf_bgcolor = $this->extConf['backgroundColor'];
                            $store_path = $this->extConf['storePath']; 
                            if($store_path == '') $store_path = 'fileadmin/user_upload/rzdummyimage';
            
                            // Width
                            if($_REQUEST['width']) $width = $_REQUEST['width'];
                            else $width = $conf_width;  

                            // Height                             
                            if($_REQUEST['height']) $height = $_REQUEST['height'];
                            else $height = $conf_height;  
                            
                            if($_REQUEST['text']) $text = '&text='.$_REQUEST['text'];
                            $action=$_REQUEST['action'] ? $_REQUEST['action'] : $LANG->getLL('preview');
                            
                            // Text color
                            if($_REQUEST['feColor']) $feColor='/'.$_REQUEST['feColor'];
                            else $feColor = $conf_textcolor;  
                            
                            // BG color
                            if($_REQUEST['bgColor']) $bgColor='/'.$_REQUEST['bgColor'];
                            else $bgColor = $conf_bgcolor;
                            
                            if($_REQUEST['format']) $format='.'.$_REQUEST['format'];
                            
                            if($feColor && !$bgColor)
                                $bgColor='ccc';

                            $bgColor=str_replace("#", "", $bgColor);
                            $feColor=str_replace("#", "", $feColor);

                            if($action==$LANG->getLL('save')) {
                              if($_REQUEST['ad_sizes'] == '' && $_REQUEST['screen_standards'] == '' && $_REQUEST['video_standards'] == '') {
                                $contents = file_get_contents('http://dummyimage.com/'.$width.'x'.$height . $bgColor . $feColor . $format . $text);
                                $fileName = $width.'x'.$height . str_replace("&text=","_",$text) . $format;           
                              }
                              else if($_REQUEST['ad_sizes'] != '' || $_REQUEST['screen_standards'] != '' || $_REQUEST['video_standards'] != '') {
                                if($_REQUEST['ad_sizes'] != '') $request = $_REQUEST['ad_sizes'];
                                else if($_REQUEST['screen_standards'] != '') $request = $_REQUEST['screen_standards'];
                                else if($_REQUEST['video_standards'] != '') $request = $_REQUEST['video_standards'];  
                                                              
                                $contents = file_get_contents('http://dummyimage.com/'.$request . $bgColor . $feColor . $format . $text);
                                $fileName = $request . str_replace("&text=","_",$text) . $format;           
                              }
                              
                              $fileName = strtolower($fileName);                     
                              $fileName = str_replace(array(" ","č","ž","š","đ","ä","ö","ü","ß"),array("_","c","z","s","dz","ae","oe","ue","ss"),$fileName);                                                                                
                              $savefile = fopen(PATH_site.''.$store_path.'/'.$fileName, 'w');
                              fwrite($savefile, $contents);
                              fclose($savefile);                              
                              
                              $message = $LANG->getLL('imageSaved').'<br />'.$LANG->getLL('path').': '.$store_path.'/'.$fileName;
                            }

                            $content.='
                                <form name="dummyImage" method="post">
                                <label for="width">'.$LANG->getLL('width').' (px):</label><br /> <input name="width" type="text" value="'.$width.'" maxlength="5" />
                                
                                <div style="clear:both; height:7px;"></div>
                                <label for="height">'.$LANG->getLL('height').' (px):</label><br /> <input name="height" type="text" value="'.$height.'" maxlength="5"/>
                                
                                <div style="clear:both; height:7px;"></div>
                                <label for="feColor">'.$LANG->getLL('textColor').' (HEX):</label><br /><input name="feColor" maxlength="7" type="text" value="'.str_replace("/","",$feColor).'" />

                                <div style="clear:both; height:7px;"></div>
                                <label for="bgColor">'.$LANG->getLL('backgroundColor').' (HEX):</label><br /><input maxlength="7" name="bgColor" type="text" value="'.str_replace("/","",$bgColor).'" />
                                
                                <div style="clear:both; height:7px;"></div>
                                <label for="text">'.$LANG->getLL('text').':</label><br /> <input name="text" type="text" maxlength="100" value="'.str_replace("&text=","",$text).'" />
                                
                                <div style="clear:both; height:7px;"></div>
                                <label for="text">'.$LANG->getLL('format').':</label><br /> 
                                <select name="format">
                                  <option value="png" '.$this->checkSelected($_REQUEST['format'],'png').'>png</option>
                                  <option value="gif" '.$this->checkSelected($_REQUEST['format'],'gif').'>gif</option>
                                  <option value="jpg" '.$this->checkSelected($_REQUEST['format'],'jpg').'>jpg</option>                                  
                                </select><br /><br />
                                
                                <hr style="margin-bottom:10px;" />
                                
                                <div style="clear:both; height:7px;"></div>
                                <label for="text">'.$LANG->getLL('ad_sizes').':</label><br /> 
                                <select name="ad_sizes">
                                  <option value=""></option>
                                  <option value="medrect" '.$this->checkSelected($_REQUEST['ad_sizes'],'medrect').'>mediumrectangle (300×250)</option>
                                  <option value="sqrpop" '.$this->checkSelected($_REQUEST['ad_sizes'],'sqrpop').'>squarepopup (250×250)</option>
                                  <option value="vertrec" '.$this->checkSelected($_REQUEST['ad_sizes'],'vertrec').'>verticalrectangle (240×400)</option>  
                                  <option value="lrgrec" '.$this->checkSelected($_REQUEST['ad_sizes'],'lrgrec').'>largerectangle (336×280)</option>  
                                  <option value="rec" '.$this->checkSelected($_REQUEST['ad_sizes'],'rec').'>rectangle (180×150)</option>  
                                  <option value="pop" '.$this->checkSelected($_REQUEST['ad_sizes'],'pop').'>popunder (720×300)</option>  
                                  <option value="fullban" '.$this->checkSelected($_REQUEST['ad_sizes'],'fullban').'>fullbanner (468×60)</option>  
                                  <option value="halfban" '.$this->checkSelected($_REQUEST['ad_sizes'],'halfban').'>halfbanner (234×60)</option>  
                                  <option value="mibar" '.$this->checkSelected($_REQUEST['ad_sizes'],'mibar').'>microbar (88×31)</option>  
                                  <option value="but1" '.$this->checkSelected($_REQUEST['ad_sizes'],'but1').'>button1 (120×90)</option>  
                                  <option value="but2" '.$this->checkSelected($_REQUEST['ad_sizes'],'but2').'>button2 (120×60)</option>  
                                  <option value="vertban" '.$this->checkSelected($_REQUEST['ad_sizes'],'vertban').'>verticalbanner (120×240)</option>  
                                  <option value="sqrbut" '.$this->checkSelected($_REQUEST['ad_sizes'],'sqrbut').'>squarebutton (125×125)</option>  
                                  <option value="leadbrd" '.$this->checkSelected($_REQUEST['ad_sizes'],'leadbrd').'>leaderboard (728×90)</option>  
                                  <option value="wiskyscrpr" '.$this->checkSelected($_REQUEST['ad_sizes'],'wiskyscrpr').'>wideskyscraper (160×600)</option>  
                                  <option value="skyscrpr" '.$this->checkSelected($_REQUEST['ad_sizes'],'skyscrpr').'>skyscraper (120×600)</option>  
                                  <option value="hpge" '.$this->checkSelected($_REQUEST['ad_sizes'],'hpge').'>halfpage (300×600)</option>                                  
                                </select>
                                
                                <div style="clear:both; height:7px;"></div>
                                <label for="text">'.$LANG->getLL('screen_standards').':</label><br /> 
                                <select name="screen_standards">
                                  <option value=""></option>
                                  <option value="cga" '.$this->checkSelected($_REQUEST['screen_standards'],'cga').'>cga (320x200)</option>   
                                  <option value="qvga" '.$this->checkSelected($_REQUEST['screen_standards'],'qvga').'>qvga (320x240)</option>   
                                  <option value="vga" '.$this->checkSelected($_REQUEST['screen_standards'],'vga').'>vga (640x480)</option>   
                                  <option value="wvga" '.$this->checkSelected($_REQUEST['screen_standards'],'wvga').'>wvga (800x480)</option>   
                                  <option value="svga" '.$this->checkSelected($_REQUEST['screen_standards'],'svga').'>svga (800x480)</option>   
                                  <option value="wsvga" '.$this->checkSelected($_REQUEST['screen_standards'],'wsvga').'>wsvga (1024x600)</option>   
                                  <option value="xga" '.$this->checkSelected($_REQUEST['screen_standards'],'xga').'>xga (1024x768)</option>   
                                  <option value="wxga" '.$this->checkSelected($_REQUEST['screen_standards'],'wxga').'>wxga (1280x800)</option> 
                                  <option value="wsxga" '.$this->checkSelected($_REQUEST['screen_standards'],'wsxga').'>wsxga (1440x900)</option> 
                                  <option value="wuxga" '.$this->checkSelected($_REQUEST['screen_standards'],'wuxga').'>wuxga (1920x1200)</option> 
                                  <option value="wqxga" '.$this->checkSelected($_REQUEST['screen_standards'],'wqxga').'>wqxga (2560x1600)</option>                                
                                </select>
                                
                                <div style="clear:both; height:7px;"></div>
                                <label for="text">'.$LANG->getLL('video_standards').':</label><br /> 
                                <select name="video_standards">
                                  <option value=""></option>
                                  <option value="ntsc" '.$this->checkSelected($_REQUEST['video_standards'],'ntsc').'>ntsc (720x480)</option>   
                                  <option value="pal" '.$this->checkSelected($_REQUEST['video_standards'],'pal').'>pal (768x576)</option>   
                                  <option value="hd720" '.$this->checkSelected($_REQUEST['video_standards'],'hd720').'>hd720 (1280x720)</option>   
                                  <option value="hd1080" '.$this->checkSelected($_REQUEST['video_standards'],'hd1080').'>hd1080 (1920x1080)</option>                                
                                </select><br /><br />                                                                                                                                
                                
                                <!-- * - '.$LANG->getLL('obligatory').'<br />-->
                                
                                <hr style="margin-bottom:10px;" />
                              ';
                              
                              if($_REQUEST['action'] == $LANG->getLL('preview')) {         
                                if($_REQUEST['ad_sizes'] == '' && $_REQUEST['screen_standards'] == '' && $_REQUEST['video_standards'] == '') {                    
                                  $content.='
                                    <img src="http://dummyimage.com/'.$width.'x'.$height . $bgColor . $feColor . $format . $text.'" />
                                  ';
                                }      
                                else if($_REQUEST['ad_sizes'] != '' || $_REQUEST['screen_standards'] != '' || $_REQUEST['video_standards'] != '') {
                                  if($_REQUEST['ad_sizes'] != '') $request = $_REQUEST['ad_sizes'];
                                  else if($_REQUEST['screen_standards'] != '') $request = $_REQUEST['screen_standards'];
                                  else if($_REQUEST['video_standards'] != '') $request = $_REQUEST['video_standards'];  
                                  
                                  $content.='
                                    <img src="http://dummyimage.com/'.$request . $bgColor . $feColor . $format . $text.'" />
                                  ';                                                                                                                                        
                                }                              
                              }
                              
                              $content.='                               
                                <div style="clear:both; height:7px;"></div>
                                <input name="action" type="submit" value="'.$LANG->getLL('preview').'" /> <input name="action" type="submit" value="'.$LANG->getLL('save').'" />
                                </form>
                            ';


							$this->content.=$content.'<br />'.$message;
						break;
					    default:
                            die("ERROR");
                    }
				}
        
        function checkSelected($request,$check_string) {
          if($request == $check_string) return 'selected="selected"';
        }
				
		}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rzdummyimage/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rzdummyimage/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_rzdummyimage_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>
