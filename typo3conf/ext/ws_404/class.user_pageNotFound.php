<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Web.Spectr <info@web-spectr.com>
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


/**
 * Plugin for 404 error with multilingual and multidomain support, with support for RealUrl configs.
 * Uses $TYPO3_CONF_VARS['FE']['pageNotFound_handling'] hook
 *
 * @author Nikolay Orlenko <info@web-spectr.com>
 * @package TYPO3
 * @subpackage ws_404
 */
class user_pageNotFound {

  /**
   * Process 404 error and prints it's result from defined page according to current language and current domain
   *
   * @param array Parameter
   * @param ref reference to parent TSFE object, which is not fully initialized
   * @return void
   */
  function pageNotFound($param, $ref) {
    $sContent = '';
	$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ws_404']);
	
	try {
        if($param['reasonText']){
            t3lib_div::devLog('404 Reason: ' . $param['reasonText'], 'ws_404', 1, array('page' => t3lib_div::getIndpEnv('TYPO3_REQUEST_URL')));
			
			if(!$this->isSpecialStaticFile()){
				$sLanguageVar = (!empty($aTSconf['languageVar'])) ? $aTSconf['languageVar'] : 'L';

				$this->aParams = $param;
				$this->sCurrentUrl = $param['currentUrl'];

				// suppose that language configuration located in preVars part of RealUrl cofiguration
				$this->aRealurlExtConf = $this->aGetRealurlConfiguration($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl'], t3lib_div::getIndpEnv('HTTP_HOST'));

				$iLanguageUid = $this->mGetValue($sLanguageVar);
				$sNotFoundPageList = $this->sCheckPages($this->extConf['pagesFor404Error'], $iLanguageUid);
				$sNotFoundPageList = $sNotFoundPageList ? $sNotFoundPageList : 1;
				
				$iNotFoundPageId = $this->iGetNotFoundPageId($sNotFoundPageList);
				
				$sAllowedTypeNum = $this->extConf['typeNum'];
				$aAllowedTypeNum = t3lib_div::trimExplode(',', $sAllowedTypeNum);
				$iType = $this->mGetValue('type');
				if (!in_array($iType, $aAllowedTypeNum)) {
				  $iType = 0;
				}

				$aUrl = array();
				$aUrl['domain'] = t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST');
				$aUrl['id'] = $iNotFoundPageId ? '?id=' . $iNotFoundPageId : '';
				$aUrl['type'] = $iType > 0 ? '&type=' . $iType : '';
				$aUrl['L'] = $iLanguageUid !== false ? '&' . $sLanguageVar . '=' . $iLanguageUid : '';

				// this is a url from were to get content of error with appropriate language
				$sNotFoundContentPageUrl = $aUrl['domain'].'/'.'index.php' . $aUrl['id'] . $aUrl['L'] . $aUrl['type'];
				
				
				/*
				//Get charset
				$charset = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ? $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] : $GLOBALS['TSFE']->defaultCharSet;
				$aHeaderArr = array(
					'User-agent: ' . t3lib_div::getIndpEnv('HTTP_USER_AGENT'),
					'Referer: ' . t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'),
					'Content-Type: text/html; charset="' . $charset .'"',
				);
				$res = t3lib_div::getURL($sNotFoundContentPageUrl, 1, $aHeaderArr);
				// Header and content are separated by an empty line
				list($sHeader,$sContent) = explode("\r\n\r\n", $res, 2);
				$sContent.= "\r\n";
				*/

				//$sContent = @file_get_contents($sNotFoundContentPageUrl);
				
				if (function_exists('curl_version') == "Enabled" ) {
					$sContent = $this->curlGet($sNotFoundContentPageUrl);
				} else {
					$sContent = @file_get_contents($sNotFoundContentPageUrl);
					if(!$sContent) {
						$sContent = 'please enable allow_url_fopen option or install cUrl!';
					}
				}
			}
			else {
				$sContent = '404 Error';
			}
        }

		echo $sContent;
		throw new Exception('');
    }
    catch (Exception $e){
        t3lib_div::devLog('404: ' . $e->getTraceAsString(), 'ws_404', 1 );
    }
	exit;
  }
  
  function isSpecialStaticFile(){
	$containStatic = FALSE;
	if(!empty($this->extConf['staticFiles'])){
		$aStaticFiles = explode(',', $this->extConf['staticFiles']);
		foreach($aStaticFiles as $fileExt){
			if(stristr(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'), '.'.$fileExt) === FALSE){
				$containStatic = FALSE;
			}
			else {
				$containStatic = TRUE;
				break;
			}
		}
	}
	return $containStatic;
	
  }

  /**
   * Returns value of website GET/POST variable used with current request
   * @param string  GET/POST variable
   * @return mixed  Variable value which was found in current request
   */
  function mGetValue($psVar) {
    $mVal = false;

    if (t3lib_div::_GET($psVar)) {
      $mVal = t3lib_div::_GET($psVar);
    }

    if (is_array($this->aRealurlExtConf) && !empty($this->aRealurlExtConf) && $mVal === false) {
      $aPreGetVars = $this->decodeSpURL_doDecode($this->aParams['currentUrl']);
      if (is_array($aPreGetVars) && array_key_exists($psVar, $aPreGetVars)) {
        $mVal = $aPreGetVars[$psVar];
      }
    }

    $mDefaultVal = $this->mGetDefaultFromRealUrlConf($psVar);
    if($mDefaulTypeValue !== false && $mVal === false) {
      $mVal = (int) $mDefaultVal;
    }

    $mVal = ( isset($mVal) && !empty($mVal) ) ? $mVal : 0;

    return $mVal;
  }

  /**
   * Get valueDefault value from realurl config by given parameter
   * @param $GP What default value we are looking for
   * @param $sKey string  Key where to search, Could be preVars, postVarSets
   * @return mixed value
   */
  function mGetDefaultFromRealUrlConf($GP, $sKey = 'preVars'){
    $mValueDefault = false;
    if (isset($GP)) {
	  if(is_array($this->aRealurlExtConf[$sKey])){
		  foreach($this->aRealurlExtConf[$sKey] as $aItem){
			if (array_search($GP, $aItem)) {
			  if (isset($aItem['valueDefault'])) {
				$mValueDefault = $aItem['valueDefault'];
			  }
			}
		  }
	  }
    }
    return $mValueDefault;
  }


  /**
   * Returns RealUrl configuration for requested domain
   *
   * @param array Whole RealUrl config: $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']
   * @param string	Current requested domain name
   * @return array	RealUrl configuration for requested domain
   */
  function aGetRealurlConfiguration($paRealurlConf = array(), $psHost='_DEFAULT') {
  	if (is_array($paRealurlConf) && array_key_exists($psHost, $paRealurlConf)) {
  	  $sCurrentDomain = $psHost;
  	} else {
  	  $sCurrentDomain = '_DEFAULT';
  	}
  	if (is_array($paRealurlConf[$sCurrentDomain])) {
  	  return $paRealurlConf[$sCurrentDomain];
  	} else {
  	  return $paRealurlConf[$paRealurlConf[$sCurrentDomain]];
  	}
  }

  /**
  * Get 404 page uid for current rootpage
  * @param string List of pages uid with error content for showing
  * @return int
  */
  public function iGetNotFoundPageId($sPagesList) {
    $iErrorUid = 0;
	
    if (!defined('PATH_tslib')) {
      if (@is_dir(PATH_site.'typo3/sysext/cms/tslib/')) {
        define('PATH_tslib', PATH_site.'typo3/sysext/cms/tslib/');
      } elseif (@is_dir(PATH_site.'tslib/')) {
        define('PATH_tslib', PATH_site.'tslib/');
      }
    }
    require_once (PATH_tslib . 'class.tslib_eidtools.php');
    require_once (PATH_t3lib . 'class.t3lib_page.php');

	global $TYPO3_CONF_VARS;

	/**
     * initialize TSFE
     */
	$oUserTSFE = t3lib_div::makeInstance('tslib_fe', $TYPO3_CONF_VARS, 1, 0);
    $oUserTSFE->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');


    $aPagesList = t3lib_div::trimExplode(',', $sPagesList);
    $iRootPageUid = (int) $oUserTSFE->findDomainRecord();
    foreach($aPagesList as $uid){
      $aRootLine = $oUserTSFE->sys_page->getRootLine($uid);
	  if($aRootLine[0]['uid'] == $iRootPageUid) {
        $iErrorUid = (int) $uid;
      }
    }

    if (!$iErrorUid && $iRootPageUid ) $iErrorUid = $iRootPageUid;
    if(count($aPagesList) == 1)	$iErrorUid = (int)$sPagesList;

    unset($oUserTSFE);
	unset($oUserTSFE->sys_page);

    return $iErrorUid;
  }


  /******* RealUrl section ******/
	/**
	 * This is taken directly from class.tx_realurl.php and truncated and modified for our case
	 * Decodes a speaking URL path into an array of GET parameters and a page id.
	 *
	 * @param	string		Speaking URL path (after the "root" path of the website!) but without query parameters
	 * @param	boolean		If cHash caching is enabled or not.
	 * @return	array		Array with id and GET parameters.
	 * @see decodeSpURL()
	 */
	function decodeSpURL_doDecode($speakingURIpath, $cHashCache = FALSE) {
	  $speakingURIpath = t3lib_div::trimExplode('?', $speakingURIpath);
	  $speakingURIpath = $speakingURIpath[0];

	  // Cached info:
    $cachedInfo = array();

    // Convert URL to segments
    $pathParts = t3lib_div::trimExplode('/', $speakingURIpath, 1);

    array_walk($pathParts, create_function('&$value', '$value = rawurldecode($value);'));

    // Strip/process file name or extension first
    $file_GET_VARS = $this->decodeSpURL_decodeFileName($pathParts);

    // Setting original dir-parts:
    $this->dirParts = $pathParts;

    // Setting "preVars":
    $pre_GET_VARS = $this->decodeSpURL_settingPreVars($pathParts, $this->aRealurlExtConf['preVars']);

    // Merge Get vars together:
    $cachedInfo['GET_VARS'] = array();
    if (is_array($pre_GET_VARS))
      $cachedInfo['GET_VARS'] = t3lib_div::array_merge_recursive_overrule($cachedInfo['GET_VARS'], $pre_GET_VARS);
    if (is_array($file_GET_VARS))
      $cachedInfo['GET_VARS'] = t3lib_div::array_merge_recursive_overrule($cachedInfo['GET_VARS'], $file_GET_VARS);

    // Return information found:
    return $cachedInfo['GET_VARS'];
	}

  /**
   * This is taken directly from class.tx_realurl.php
   * Decodes the file name and adjusts file parts accordingly
   *
   * @param array $pathParts Path parts of the URLs (can be modified)
   * @return array GET varaibles from the file name or empty array
   */
  protected function decodeSpURL_decodeFileName(array &$pathParts) {
    $getVars = array();
    $fileName = rawurldecode(array_pop($pathParts));
    list($segment, $extension) = t3lib_div::revExplode('.', $fileName, 2);
    if ($extension) {
      $getVars = array();
      if (!$this->decodeSpURL_decodeFileName_lookupInIndex($fileName, $segment, $extension, $pathParts, $getVars)) {
        if (!$this->decodeSpURL_decodeFileName_checkHtmlSuffix($fileName, $segment, $extension, $pathParts)) {
        }
      }
    }
    return $getVars;
  }

  /**
   * This is taken directly from class.tx_realurl.php
   * Checks if the suffix matches to the configured one.
   *
   * @param string $fileName
   * @param string $segment
   * @param string $extension
   * @param array $pathPartsCopy
   * @see tx_realurl::decodeSpURL_decodeFileName()
   */
  protected function decodeSpURL_decodeFileName_checkHtmlSuffix($fileName, $segment, $extension, array &$pathParts) {
    $handled = false;
    if (isset($this->aRealurlExtConf['fileName']['defaultToHTMLsuffixOnPrev'])) {
      $suffix = $this->aRealurlExtConf['fileName']['defaultToHTMLsuffixOnPrev'];
      $suffix = (!$this->isString($suffix, 'defaultToHTMLsuffixOnPrev') ? '.html' : $suffix);
      if ($suffix == '.' . $extension) {
        $pathParts[] = rawurlencode($segment);
        $this->filePart = '.' . $extension;
      }
      $handled = true;
    }
    return $handled;
  }

  /**
   * This is taken directly from class.tx_realurl.php
   * Looks up the file name or the extension in the index.
   *
   * @param string $fileName
   * @param string $segment
   * @param string $extension
   * @param array $pathPartsCopy Path parts (can be modified)
   * @return array GET variables (can be enpty in case if there is a default file name)
   * @see tx_realurl::decodeSpURL_decodeFileName()
   */
  protected function decodeSpURL_decodeFileName_lookupInIndex($fileName, $segment, $extension, array &$pathPartsCopy, array &$getVars) {
    $handled = false;
    $keyValues = '';
    if (is_array($this->aRealurlExtConf['fileName']['index'])) {
      foreach ($this->aRealurlExtConf['fileName']['index'] as $key => $config) {
        if ($key == $fileName) {
          $keyValues = $config['keyValues'];
          $this->filePart = $fileName;
          if (isset($config['mimetype'])) {
            $this->mimeType = $config['mimetype'];
          }
          $handled = true;
          break;
        }
        elseif ($key == '.' . $extension) {
          $keyValues = $config['keyValues'];
          $pathPartsCopy[] = urlencode($segment);
          $this->filePart = '.' . $extension;
          if (isset($config['mimetype'])) {
            $this->mimeType = $config['mimetype'];
          }
          $handled = true;
          break;
        }
      }
    }
    // Must decode key values if set
    if ($keyValues) {
      $getString = $this->decodeSpURL_getSingle($keyValues);
      parse_str($getString, $getVars);
    }
    return $handled;
  }

  /**
   * This is taken directly from class.tx_realurl.php
   * Traverses incoming array of GET-var => value pairs and implodes that to a string of GET parameters
   *
   * @param array   Parameters
   * @return  string    GET parameters
   * @see decodeSpURL_fileName(), decodeSpURL_settingPostVarSets(), encodeSpURL_setSingle()
   */
  protected function decodeSpURL_getSingle($keyValues) {
    $GET_string = '';
    if (is_array($keyValues)) {
      foreach ($keyValues as $kkey => $vval) {
        $GET_string .= '&' . rawurlencode($kkey) . '=' . rawurlencode($vval);
      }
    }
    return $GET_string;
  }

	/**
	 * This is taken directly from class.tx_realurl.php
	 * Analysing the path BEFORE the page identification part of the URL
	 *
	 * @param	array		The path splitted by "/". NOTICE: Passed by reference and shortend for each time a segment is matching configuration
	 * @param	array		Configuration
	 * @return	array		GET-vars resulting from the analysis
	 * @see decodeSpURL_doDecode()
	 */
	function decodeSpURL_settingPreVars(&$pathParts, $config) {
		if (is_array($config)) {

			// Pulling vars of the pathParts
			$GET_string = $this->decodeSpURL_getSequence($pathParts, $config);

			// If a get string is created, then:
			if ($GET_string) {
				$GET_VARS = false;
				parse_str($GET_string, $GET_VARS);
				return $GET_VARS;
			}
		}
	}

	/**
	 * This is taken directly from class.tx_realurl.php and modified a little bit for our case
	 * Pulling variables of the path parts
	 *
	 * @param	array		Parts of path. NOTICE: Passed by reference.
	 * @param	array		Setup array for segments in set.
	 * @return	string		GET parameter string
	 * @see decodeSpURL_settingPreVars(), decodeSpURL_settingPostVarSets()
	 */
   function decodeSpURL_getSequence(&$pathParts, $setupArr) {
    $GET_string = '';
    $prevVal = '';
    foreach ($setupArr as $setup) {
      if (count($pathParts) == 0) {
        break;
      }
      else {
        // Get value and remove from path parts:
        $value = $origValue = array_shift($pathParts);
        $value = rawurldecode($value);

        switch ($setup['type']) {
          case 'action':
            // Find index key:
            $idx = isset($setup['index'][$value]) ? $value : '_DEFAULT';

            // Look up type:
            switch ((string)$setup['index'][$idx]['type']) {
              case 'bypass':
                array_unshift($pathParts, $origValue);
                break;
              case 'feLogin':
                // Do nothing.
                break;
            }
            break;
          default:
            if (!is_array($setup['cond'])) {

              // Map value if applicable:
              if (isset($setup['valueMap'][$value])) {
                $value = $setup['valueMap'][$value];
              } elseif ($setup['noMatch'] == 'bypass') {
                // If no match and "bypass" is set, then return the value to $pathParts and break
                array_unshift($pathParts, $origValue);
                break;
              } elseif ($setup['noMatch'] == 'null') { // If no match and "null" is set, then break (without setting any value!)
                break;
              } elseif (isset($setup['valueDefault'])) { // If no matching value and a default value is given, set that:
                $value = $setup['valueDefault'];
              }

              // Set previous value:
              $prevVal = $value;

              // Add to GET string:
              if ($setup['GETvar'] && strlen($value)) { // Checking length of value; normally a *blank* parameter is not found in the URL! And if we don't do this we may disturb "cHash" calculations!
                $GET_string .= '&' . rawurlencode($setup['GETvar']) . '=' . rawurlencode($value);
              }
            } else {
              array_unshift($pathParts, $origValue);
              break;
            }
            break;
        }
      }
    }

    return $GET_string;
  }


	/**
	 * This is taken directly from class.tx_realurl.php
	 * Checks for wrong boolean values (like <code>'1'</code> or </code>'true'</code> instead of <code>1</code> and <code>true</code>.
	 *
	 * @param	mixed		$str Parameter to check
	 * @param	string		$paramName Parameter name (for logging)
	 * @return	<code>true</code>		if string (and not bad boolean)
	 */
	function isString(&$str, $paramName) {
		if (!is_string($str)) {
			return false;
		}
		if (preg_match('/^(1|0|true|false)$/i', $str)) {
			$logMessage = sprintf('Wrong boolean value for parameter "%s": "%s". It is a string, not a boolean!', $paramName, $str);
			if ($this->enableDevLog) {
				t3lib_div::devLog($logMessage, 'realurl');
			}
			$GLOBALS['TT']->setTSlogMessage($logMessage, 2);
			if ($str == intval($str)) {
				$str = intval($str);
			} else {
				$str = (strtolower($str) == 'true');
			}
			return false;
		}
		return true;
	}
	
	function sCheckPages($sPagesList, $iLanguageUid){
		$sPagesList = trim($sPagesList);
		
		if($iLanguageUid === false || $iLanguageUid == 0) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				"uid as id", 
				"pages", 
				"deleted=0 AND hidden=0 AND uid IN (".$sPagesList.")"
			);
		}
		else {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				"pid as id", 
				"pages_language_overlay", 
				"deleted=0 AND hidden=0 AND pid IN (".$sPagesList.") AND sys_language_uid='".$iLanguageUid."'"
			);
		}
		$aPagesList = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
			$aPagesList[] = $row["id"]; 
		}
		
		return implode(",", $aPagesList);
	}

	
	function curlGet($url){
		$ch = curl_init();
		$timeout = 5; // set to zero for no timeout
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);	
		return $data;
	}
	
}
?>