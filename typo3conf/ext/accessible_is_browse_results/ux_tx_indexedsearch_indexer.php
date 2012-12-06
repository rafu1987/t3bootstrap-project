<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2001-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Indexing class for TYPO3 frontend
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage tx_indexedsearch
 */
class ux_tx_indexedsearch_indexer extends tx_indexedsearch_indexer {

	/**
	 * Splits HTML content and returns an associative array, with title, a list of metatags, and a list of words in the body.
	 *
	 * @param	string		HTML content to index. To some degree expected to be made by TYPO3 (ei. splitting the header by ":")
	 * @return	array		Array of content, having keys "title", "body", "keywords" and "description" set.
	 * @see splitRegularContent()
	 */
	function splitHTMLContent($content) {
	
			// divide head from body ( u-ouh :) )
		$contentArr = $this->defaultContentArray;
		$contentArr['body'] = stristr($content,'<body');
		$headPart = substr($content,0,-strlen($contentArr['body']));

			// get title
		$this->embracingTags($headPart,'TITLE',$contentArr['title'],$dummy2,$dummy);
		$titleParts = explode(':',$contentArr['title'],2);
		$contentArr['title'] = trim(isset($titleParts[1]) ? $titleParts[1] : $titleParts[0]);
			// get keywords and description metatags
			
		// via TypoScript - plugin.tx_indexed_search setup
		$conf_plugin=$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_indexedsearch.'];
		
		for($i=0;$this->embracingTags($headPart,'meta',$dummy,$headPart,$meta[$i]);$i++) { /*nothing*/ }
		
		for($i=0;isset($meta[$i]);$i++) {
			$meta[$i] = t3lib_div::get_tag_attributes($meta[$i]);
			//if(stristr($meta[$i]['name'],'keywords')) $contentArr['keywords'].=','.$meta[$i]['content'];
			//if(stristr($meta[$i]['name'],'description')) $contentArr['description'].=','.$meta[$i]['content'];
			if($conf_plugin['addKeywordsToIndex'] && stristr($meta[$i]['name'],'description')) {
					$contentArr['keywords'].=','.$meta[$i]['content'];
			if($conf_plugin['addDescriptionToIndex'] && stristr($meta[$i]['name'],'description')) {
				$contentArr['description'].=','.$meta[$i]['content'];
			}
				}
		}
		
			
		
			// Process <!--TYPO3SEARCH_begin--> or <!--TYPO3SEARCH_end--> tags:
		$this->typoSearchTags($contentArr['body']);

			// Get rid of unwanted sections (ie. scripting and style stuff) in body
		$tagList = explode(',',$this->excludeSections);
		foreach($tagList as $tag)	{
			while($this->embracingTags($contentArr['body'],$tag,$dummy,$contentArr['body'],$dummy2));
		}

			// remove tags, but first make sure we don't concatenate words by doing it
		$contentArr['body'] = str_replace('<',' <',$contentArr['body']);
		$contentArr['body'] = trim(strip_tags($contentArr['body']));

		$contentArr['keywords'] = trim($contentArr['keywords']);
		$contentArr['description'] = trim($contentArr['description']);

			// Return array
		return $contentArr;
	}

}
?>
