<?php

class ux_tx_indexedsearch extends tx_indexedsearch {


	/**
	 * Initialize internal variables, especially selector box values for the search form and search words
	 *
	 * @return	void
	 */
	function initialize()	{
		global $TYPO3_CONF_VARS;
		
			// Initialize external document parsers for icon display and other soft operations
		if (is_array($TYPO3_CONF_VARS['EXTCONF']['indexed_search']['external_parsers']))	{
			foreach ($TYPO3_CONF_VARS['EXTCONF']['indexed_search']['external_parsers'] as $extension => $_objRef)	{
				$this->external_parsers[$extension] = &t3lib_div::getUserObj($_objRef);

					// Init parser and if it returns false, unset its entry again:
				if (!$this->external_parsers[$extension]->softInit($extension))	{
					unset($this->external_parsers[$extension]);
				}
			}
		}
			// Init lexer (used to post-processing of search words)
		$lexerObjRef = $TYPO3_CONF_VARS['EXTCONF']['indexed_search']['lexer'] ?
						$TYPO3_CONF_VARS['EXTCONF']['indexed_search']['lexer'] :
						'EXT:indexed_search/class.lexer.php:&tx_indexedsearch_lexer';
		$this->lexerObj = &t3lib_div::getUserObj($lexerObjRef);

			// If "_sections" is set, this value overrides any existing value.
		if ($this->piVars['_sections'])		$this->piVars['sections'] = $this->piVars['_sections'];

			// If "_sections" is set, this value overrides any existing value.
		if ($this->piVars['_freeIndexUid']!=='_')		$this->piVars['freeIndexUid'] = $this->piVars['_freeIndexUid'];

			// Add previous search words to current
		if ($this->piVars['sword_prev_include'] && $this->piVars['sword_prev'])	{
			$this->piVars['sword'] = trim($this->piVars['sword_prev']).' '.$this->piVars['sword'];
		}

		$this->piVars['results'] = t3lib_div::intInRange($this->piVars['results'],1,100000,$this->defaultResultNumber);

			// Selector-box values defined here:
		$this->optValues = Array(
			'type' => Array(
				'0' => $this->pi_getLL('opt_type_0'),
				'1' => $this->pi_getLL('opt_type_1'),
				'2' => $this->pi_getLL('opt_type_2'),
				'3' => $this->pi_getLL('opt_type_3'),
				'10' => $this->pi_getLL('opt_type_10'),
				'20' => $this->pi_getLL('opt_type_20'),
			),
			'defOp' => Array(
				'0' => $this->pi_getLL('opt_defOp_0'),
				'1' => $this->pi_getLL('opt_defOp_1'),
			),
			'sections' => Array(
				'0' => $this->pi_getLL('opt_sections_0'),
				'-1' => $this->pi_getLL('opt_sections_-1'),
				'-2' => $this->pi_getLL('opt_sections_-2'),
				'-3' => $this->pi_getLL('opt_sections_-3'),
				// Here values like "rl1_" and "rl2_" + a rootlevel 1/2 id can be added to perform searches in rootlevel 1+2 specifically. The id-values can even be commaseparated. Eg. "rl1_1,2" would search for stuff inside pages on menu-level 1 which has the uid's 1 and 2.
			),
			'freeIndexUid' => Array(
				'-1' => $this->pi_getLL('opt_freeIndexUid_-1'),
				'-2' => $this->pi_getLL('opt_freeIndexUid_-2'),
				'0' => $this->pi_getLL('opt_freeIndexUid_0'),
			),
			'media' => Array(
				'-1' => $this->pi_getLL('opt_media_-1'),
				'0' => $this->pi_getLL('opt_media_0'),
				'-2' => $this->pi_getLL('opt_media_-2'),
			),
			'order' => Array(
				'rank_flag' => $this->pi_getLL('opt_order_rank_flag'),
				'rank_freq' => $this->pi_getLL('opt_order_rank_freq'),
				'rank_first' => $this->pi_getLL('opt_order_rank_first'),
				'rank_count' => $this->pi_getLL('opt_order_rank_count'),
				'mtime' => $this->pi_getLL('opt_order_mtime'),
				'title' => $this->pi_getLL('opt_order_title'),
				'crdate' => $this->pi_getLL('opt_order_crdate'),
			),
			'group' => Array (
				'sections' => $this->pi_getLL('opt_group_sections'),
				'flat' => $this->pi_getLL('opt_group_flat'),
			),
			'lang' => Array (
				-1 => $this->pi_getLL('opt_lang_-1'),
				0 => $this->conf['defaultLanguageLabel']?$this->conf['defaultLanguageLabel']:$this->pi_getLL('opt_lang_0'),
			),
			'desc' => Array (
				'0' => $this->pi_getLL('opt_desc_0'),
				'1' => $this->pi_getLL('opt_desc_1'),
			),
			'results' => Array (
				'10' => '10',
				'20' => '20',
				'50' => '50',
				'100' => '100',
			)
		);

			// Free Index Uid:
		if ($this->conf['search.']['defaultFreeIndexUidList'])	{
			$uidList = t3lib_div::intExplode(',', $this->conf['search.']['defaultFreeIndexUidList']);
			$indexCfgRecords = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,title','index_config','uid IN ('.implode(',',$uidList).')'.$this->cObj->enableFields('index_config'),'','','','uid');

			foreach ($uidList as $uidValue)	{
				if (is_array($indexCfgRecords[$uidValue]))	{
					$this->optValues['freeIndexUid'][$uidValue] = $indexCfgRecords[$uidValue]['title'];
				}
			}
		}


			// Add media to search in:
		if (strlen(trim($this->conf['search.']['mediaList'])))	{
			$mediaList = implode(',', t3lib_div::trimExplode(',', $this->conf['search.']['mediaList'], 1));
		}
		foreach ($this->external_parsers as $extension => $obj)	{
				// Skip unwanted extensions
			if ($mediaList && !t3lib_div::inList($mediaList, $extension))	{ continue; }

			if ($name = $obj->searchTypeMediaTitle($extension))	{
				$this->optValues['media'][$extension] = $this->pi_getLL('opt_sections_'.$extension,$name);
			}
		}

			// Add operators for various languages
			// Converts the operators to UTF-8 and lowercase
		$this->operator_translate_table[] = Array($GLOBALS['TSFE']->csConvObj->conv_case('utf-8',$GLOBALS['TSFE']->csConvObj->utf8_encode($this->pi_getLL('local_operator_AND'), $GLOBALS['TSFE']->renderCharset),'toLower') , 'AND');
		$this->operator_translate_table[] = Array($GLOBALS['TSFE']->csConvObj->conv_case('utf-8',$GLOBALS['TSFE']->csConvObj->utf8_encode($this->pi_getLL('local_operator_OR'), $GLOBALS['TSFE']->renderCharset),'toLower') , 'OR');
		$this->operator_translate_table[] = Array($GLOBALS['TSFE']->csConvObj->conv_case('utf-8',$GLOBALS['TSFE']->csConvObj->utf8_encode($this->pi_getLL('local_operator_NOT'), $GLOBALS['TSFE']->renderCharset),'toLower') , 'AND NOT');

			// This is the id of the site root. This value may be a commalist of integer (prepared for this)
		$this->wholeSiteIdList = intval($GLOBALS['TSFE']->config['rootLine'][0]['uid']);

			// Creating levels for section menu:
			// This selects the first and secondary menus for the "sections" selector - so we can search in sections and sub sections.
		if ($this->conf['show.']['L1sections'])	{
			$firstLevelMenu = $this->getMenu($this->wholeSiteIdList);
			while(list($kk,$mR) = each($firstLevelMenu))	{
				if ($mR['doktype']!=5)	{
					$this->optValues['sections']['rl1_'.$mR['uid']] = trim($this->pi_getLL('opt_RL1').' '.$mR['title']);
					if ($this->conf['show.']['L2sections'])	{
						$secondLevelMenu = $this->getMenu($mR['uid']);
						while(list($kk2,$mR2) = each($secondLevelMenu))	{
							if ($mR['doktype']!=5)	{
								$this->optValues['sections']['rl2_'.$mR2['uid']] = trim($this->pi_getLL('opt_RL2').' '.$mR2['title']);
							} else unset($secondLevelMenu[$kk2]);
						}
						$this->optValues['sections']['rl2_'.implode(',',array_keys($secondLevelMenu))] = $this->pi_getLL('opt_RL2ALL');
					}
				} else unset($firstLevelMenu[$kk]);
			}
			$this->optValues['sections']['rl1_'.implode(',',array_keys($firstLevelMenu))] = $this->pi_getLL('opt_RL1ALL');
		}

			// Setting the list of root PIDs for the search. Notice, these page IDs MUST have a TypoScript template with root flag on them! Basically this list is used to select on the "rl0" field and page ids are registered as "rl0" only if a TypoScript template record with root flag is there.
			// This happens AFTER the use of $this->wholeSiteIdList above because the above will then fetch the menu for the CURRENT site - regardless of this kind of searching here. Thus a general search will lookup in the WHOLE database while a specific section search will take the current sections...
		if ($this->conf['search.']['rootPidList'])	{
			$this->wholeSiteIdList = implode(',',t3lib_div::intExplode(',',$this->conf['search.']['rootPidList']));
		}

			// Load the template
		$this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);

			// Add search languages:
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'sys_language', '1=1'.$this->cObj->enableFields('sys_language'));
		while($lR = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$this->optValues['lang'][$lR['uid']] = $lR['title'];
		}
			// Calling hook for modification of initialized content
		if ($hookObj = &$this->hookRequest('initialize_postProc'))	{
			$hookObj->initialize_postProc();
		}

			// Default values set:
			// Setting first values in optValues as default values IF there is not corresponding piVar value set already.
		foreach ($this->optValues as $kk => $vv)	{
			if (!isset($this->piVars[$kk]))	{
				reset($vv);
				$this->piVars[$kk] = key($vv);
			}
		}

			// Blind selectors:
		if (is_array($this->conf['blind.']))	{
			foreach ($this->conf['blind.'] as $kk => $vv)	{
				if (is_array($vv))	{
					foreach ($vv as $kkk => $vvv)	{
						if (!is_array($vvv) && $vvv && is_array($this->optValues[substr($kk,0,-1)]))	{
							unset($this->optValues[substr($kk,0,-1)][$kkk]);
						}
					}
				} elseif ($vv) {	// If value is not set, unset the option array.
					unset($this->optValues[$kk]);
				}
			}
		}

			// This gets the search-words into the $sWArr:
		$this->sWArr = $this->getSearchWords($this->piVars['defOp']);
	}

	/**
	 * Returns a results browser
	 *
	 * @param	boolean		Show result count
	 * @param	string		String appended to "displaying results..." notice.
	 * @param	string		String appended after section "displaying results..."
	 * @param	string		List of integers pointing to free indexing configurations to search. -1 represents no filtering, 0 represents TYPO3 pages only, any number above zero is a uid of an indexing configuration!
	 * @return	string		HTML output
	 */
	function pi_list_browseresults($showResultCount=1,$addString='',$addPart='',$freeIndexUid=-1,$pointerName = 'pointer')	{

			// Initializing variables:
		$pointer=$this->piVars['pointer'];
		$count=$this->internal['res_count'];
		$results_at_a_time = t3lib_div::intInRange($this->internal['results_at_a_time'],1,1000);
		$maxPages = t3lib_div::intInRange($this->internal['maxPages'],1,100);
		$pageCount = ceil($count/$results_at_a_time);
		$sTables = '';
		$topBrowserWrap = explode('|',$this->conf['topBrowserWrap']);	
		$browseBoxWrap  = explode('|',$this->conf['browseBoxWrap']);
		$pageWrap = explode('|',$this->conf['pageWrap']);
		$activePageWrap = explode('|',$this->conf['activePageWrap']);

		if ($pageCount > 1)	{	// only show the result browser if more than one page is needed
			$pointer=intval($pointer);
			$links=array();

				// Make browse-table/links:
			if ($pointer>0)	{	// all pages after the 1st one
				//$links[]='<li>'.$this->makePointerSelector_link($this->pi_getLL('pi_list_browseresults_prev','< Previous',1),$pointer-1,$freeIndexUid).'</li>';
				//$typolink_conf['title']= $this->conf['pageTitleAdd'].' '.$this->piVars['sword'].' - '.$this->conf['pageTitlePage'].' '.($pointer);
				//$typolink_conf['parameter']=$this->pi_linkTP_keepPIvars_url(array( $pointerName => ( $pointer - 1 ? $pointer - 1 : '' ) ),$this->allowCaching);
				//$links[] = $pageWrap[0].$this->cObj->typoLink($this->pi_getLL( 'pi_list_browseresults_prev', '< Previous' ), $typolink_conf).$pageWrap[1].chr(10);
				$links[] = $pageWrap[0].$this->pi_linkTP_keepPIvars($this->pi_getLL( 'pi_list_browseresults_prev', '< Previous' ),array( $pointerName => ( $pointer - 1 ? $pointer - 1 : '' ) ),$this->allowCaching).$pageWrap[1].chr(10); 
			}

			for($a=0;$a<$pageCount;$a++)	{
				$min = max(0, $pointer+1-ceil($maxPages/2));
				$max = $min+$maxPages;
				if($max>$pageCount)	{
					$min = $min - ($max-$pageCount);
				}

				if($a >= $min && $a < $max)	{
					if($a==$pointer)	{
						//$links[]='<li'.$this->pi_classParam('browselist-currentPage').'><strong>'.$this->makePointerSelector_link(trim($this->pi_getLL('pi_list_browseresults_page','Page',1).' '.($a+1)),$a,$freeIndexUid).'</strong></li>';
						//$conf['title']= $this->conf['pageTitleAdd'].' '.$this->piVars['sword'].' - '.$this->conf['pageTitlePage'].' '.($a+1);
						//$typolink_conf['parameter']=$this->pi_linkTP_keepPIvars_url(array($pointerName => $a),$this->allowCaching);
						//$links[] = $activePageWrap[0].$this->cObj->typoLink($this->pi_getLL('pi_list_browseresults_page','Page',1).' '.($a+1), $conf).$activePageWrap[1].chr(10);
						$links[] = $activePageWrap[0].$this->pi_linkTP_keepPIvars($this->pi_getLL('pi_list_browseresults_page','Page',1).' '.($a+1),array($pointerName => $a),$this->allowCaching).$activePageWrap[1].chr(10);  
					} else {
						//$links[]='<li>'.$this->makePointerSelector_link(trim($this->pi_getLL('pi_list_browseresults_page','Page',1).' '.($a+1)),$a,$freeIndexUid).'</li>';
						//$typolink_conf['title']= $this->conf['pageTitleAdd'].' '.$this->piVars['sword'].' - '.$this->conf['pageTitlePage'].' '.($a+1);
						//$typolink_conf['parameter']=$this->pi_linkTP_keepPIvars_url(array($pointerName => $a),$this->allowCaching);
						//$links[] = $pageWrap[0].$this->cObj->typoLink($this->pi_getLL('pi_list_browseresults_page','Page',1).' '.($a+1), $typolink_conf).$pageWrap[1].chr(10);
						$links[] = $pageWrap[0].$this->pi_linkTP_keepPIvars($this->pi_getLL('pi_list_browseresults_page','Page',1).' '.($a+1), array($pointerName => $a),$this->allowCaching).$pageWrap[1].chr(10); 
					}
				}
			}
			if ($pointer+1 < $pageCount)	{
				//$links[]='<li>'.$this->makePointerSelector_link($this->pi_getLL('pi_list_browseresults_next','Next >',1),$pointer+1,$freeIndexUid).'</li>';
				//$typolink_conf['title']= $this->conf['pageTitleAdd'].' '.$this->piVars['sword'].' - '.$this->conf['pageTitlePage'].' '.($pointer+2);
				//$typolink_conf['parameter']=$this->pi_linkTP_keepPIvars_url(array($pointerName => $pointer + 1),$this->allowCaching);
				//$links[] = $pageWrap[0].$this->cObj->typoLink($this->pi_getLL(  'pi_list_browseresults_next', 'Next >'  ), $typolink_conf).$pageWrap[1].chr(10);
				$links[] = $pageWrap[0].$this->pi_linkTP_keepPIvars($this->pi_getLL(  'pi_list_browseresults_next', 'Next >'  ),array($pointerName => $pointer + 1),$this->allowCaching).$pageWrap[1].chr(10);
			}
		}

		$pR1 = $pointer*$results_at_a_time+1;
		$pR2 = $pointer*$results_at_a_time+$results_at_a_time;
		
		
		if(is_array($links))	{
			$addPart .= $browseBoxWrap[0].chr(10).implode('',$links).$browseBoxWrap[1].chr(10);
		}
	
		$label = $this->pi_getLL('pi_list_browseresults_display','Displaying results ###TAG_BEGIN###%s to %s###TAG_END### out of ###TAG_BEGIN###%s###TAG_END###');
		$label = str_replace('###TAG_BEGIN###','<strong>',$label);
		$label = str_replace('###TAG_END###','</strong>',$label);

		$sTables = $topBrowserWrap[0].chr(10).
			($showResultCount ? '<p>'.sprintf(
				$label,
				$pR1,
				min(array($this->internal['res_count'],$pR2)),
				$this->internal['res_count']
				).$addString.'</p>'.chr(10):''
			).$addPart.$topBrowserWrap[1].chr(10);

		return $sTables;
	}

}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/accessible_is_browse_results/pi/class.ux_tx_indexedsearch.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/accessible_is_browse_results/pi/class.ux_tx_indexedsearch.php']);
}
?>
