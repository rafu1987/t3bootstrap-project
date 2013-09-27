<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2003 Boris Nicolai
 *  (c) 2012 Tim Lochmüller
 * 
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
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

require_once(PATH_tslib . "class.tslib_pibase.php");
require_once(PATH_tslib . "class.tslib_content.php");

/**
 * Class tx_sourceopt_pi1
 */
class tx_sourceopt_pi1 extends tslib_pibase {

	/**
	 * Manipulation service
	 *
	 * @var Tx_Sourceopt_Service_MarkupManipulationService
	 */
	protected $markupManipulationService = NULL;

	/**
	 * Inject the markup manipulation service
	 *
	 * @param Tx_Sourceopt_Service_MarkupManipulationService $markupManipulationService
	 */
	public function injectMarkupManipulationService(Tx_Sourceopt_Service_MarkupManipulationService $markupManipulationService) {
		$this->markupManipulationService = $markupManipulationService;
	}

	var $prefixId = "tx_sourceopt_pi1"; // Same as class name
	var $scriptRelPath = "pi1/class.tx_sourceopt_pi1.php"; // Path to this script relative to the extension dir.
	var $extKey = "sourceopt"; // The extension key.
	var $content; // HTML code of the page
	var $conf; // TypoScript Configuration for theis extension
	var $tab; // tab
	var $newline; // new line

	function main(&$content, $conf) {
		// initialise    
		#$GLOBALS["TSFE"]->set_no_cache();   // cache of for debugging
		$this->init($content, $conf);

		// switch through wished modifications; sorting should not be changed
		if ($conf['enabled'] == 1) {

			// Build up the new Service
			$this->markupManipulationService->setHtml($this->content);
			if ($this->conf['formatHtml.']['tabSize'] && is_numeric($this->conf['formatHtml.']['tabSize'])) {
				$this->markupManipulationService->setTab(str_pad('', $this->conf['formatHtml.']['tabSize'], ' '));
			}

			if ($conf['removeGenerator']) {
				$this->markupManipulationService->manipulateRemoveGenerator();
			}
			if ($conf['removeComments']) {
				$this->markupManipulationService->manipulateRemoveComments($this->conf['removeComments.']['keep.']);
			}
			if ($conf['removeBlurScript']) {
				$this->markupManipulationService->manipulateRemoveBlurScript();
			}

			$this->content = $this->markupManipulationService->getHtml();

			if ($conf['headerComment']) {
				$this->includeHeaderComment($this->content);
			}
			if ($conf['formatHtml']) {
				$this->formatHtml($this->content);
			}

			### old (but help functions anyway) ###
			if ($conf['trimLines']) {
				$this->trimLines($this->content);
			}
			if ($conf['removeEmptyLines']) {
				$this->removeEmptyLines($this->content);
			}
			if ($conf['removeNewLines']) {
				$this->removeNewLines($this->content);
			}
			### old (to be removed soon) ##
			if ($conf['moveInlineCss']) {
				$this->moveInlineCss($this->content);
			}
			if ($conf['removeLinkSchema']) {
				$this->removeLinkSchema($this->content);
			}
			if ($conf['emtpyBodyTag']) {
				$this->emtpyBodyTag($this->content);
			}
			if ($conf['removeEmptyAltAtr']) {
				$this->removeEmptyAltAtr($this->content);
			}
			if ($conf['removeRealUrlBrokenRootLink']) {
				$this->removeRealUrlBrokenRootLink($this->content);
			}
			if (!empty($conf['replaceClearGif'])) {
				$this->replaceClearGif($this->content);
			}
			### old end ###
		}
	}

	/**
	 * iniitialize
	 * set class variables
	 *
	 * @param    string $content    html of output
	 * @param    array  $conf       TS-Conf array
	 *
	 * @return   void
	 */
	function init(&$content, $conf) {
		$this->content = & $content;
		$this->conf = $conf;
		switch (TYPO3_OS) { // set newline
			case "WIN" :
				$this->newline = "\r\n";
				break;
			default :
				$this->newline = "\n";
		}
		$this->tab = "\t";
		if ($this->conf['formatHtml.']['tabSize'] && is_numeric($this->conf['formatHtml.']['tabSize'])) {
			$this->tab = str_pad('', $this->conf['formatHtml.']['tabSize'], ' ');
		}

		if ($this->markupManipulationService === NULL) {
			require_once(t3lib_extMgm::extPath('sourceopt', 'Classes/Service/MarkupManipulationService.php'));
			$this->injectMarkupManipulationService(t3lib_div::makeInstance('Tx_Sourceopt_Service_MarkupManipulationService'));
		}
	}

	/**
	 * @param $html
	 */
	function includeHeaderComment(&$html) {
		$html = preg_replace_callback('/<meta http-equiv(.*)>/Usi', array(
		                                                                 $this,
		                                                                 'includeHeaderComment_callback'
		                                                            ), $html);
	}

	/**
	 * help function for "includeHeaderComment" preg_replace_callback
	 *
	 * @param $match
	 *
	 * @return string
	 */
	function includeHeaderComment_callback($match) {
		return trim($match[0] . $this->newline . $this->tab . $this->tab . "<!-- " . $this->conf['headerComment'] . "-->");
	}

	/**
	 * formats the (X)HTML code:
	 *  - taps according to the hirarchy of the tags
	 *  - removes empty spaces between tags
	 *  - removes linebreaks within tags (spares where necessary: pre, textarea, comments, ..)
	 *  choose from five options:
	 *    0 => off
	 *    1 => no line break at all  (code in one line)
	 *    2 => minimalistic line breaks (structure defining box-elements)
	 *    3 => aesthetic line breaks (important box-elements)
	 *    4 => logic line breaks (all box-elements)
	 *    5 => max line breaks (all elements)
	 *
	 * @param     string-reff     html-Code
	 *
	 * @return    void
	 */
	function formatHtml(&$html) {
		// Save original formated comments, pre, textarea, styles and java-scripts & replace them with markers
		preg_match_all("/(?s)((<!--.*?-->)|(<[ \n\r]*pre[^>]*>.*?<[ \n\r]*\/pre[^>]*>)|(<[ \n\r]*textarea[^>]*>.*?<[ \n\r]*\/textarea[^>]*>)|(<[ \n\r]*style[^>]*>.*?<[ \n\r]*\/style[^>]*>)|(<[ \n\r]*script[^>]*>.*?<[ \n\r]*\/script[^>]*>))/im", $html, $matches);
		$no_format = $matches[0]; // do not format these block elements
		for ($i = 0; $i < count($no_format); $i++) {
			$html = str_replace($no_format[$i], "\n<!-- ELEMENT $i -->", $html);
		}

		// define box elements for formatting
		$trueBoxElements = 'address|blockquote|center|dir|div|dl|fieldset|form|h1|h2|h3|h4|h5|h6|hr|isindex|menu|noframes|noscript|ol|p|pre|table|ul|article|aside|details|figcaption|figure|footer|header|hgroup|menu|nav|section';
		$functionalBoxElements = 'dd|dt|frameset|li|tbody|td|tfoot|th|thead|tr|colgroup';
		$usableBoxElements = 'applet|button|del|iframe|ins|map|object|script';
		$imagineBoxElements = 'html|body|head|meta|title|link|script|base|!--';
		$allBoxLikeElements = '(?>' . $trueBoxElements . '|' . $functionalBoxElements . '|' . $usableBoxElements . '|' . $imagineBoxElements . ')';
		$esteticBoxLikeElements = '(?>html|head|body|meta name|title|div|table|h1|h2|h3|h4|h5|h6|p|form|pre|center|!--)';
		$structureBoxLikeElements = '(?>html|head|body|div|!--)';

		// inline elements according to some webpages, <br/> was left out
		$inlineElements = '(?>a|abbr|acronym|area|applet|b|basefont|bdo|big|button|cite|code|del|dfn|em|font|i|img|input|ins|iframe|kbd|label|map|object|q|s|samp|script|select|small|span|strike|strong|sub|sup|textarea|tt|u|var)';

		// split html into it's elements	
		$html_array_temp = preg_split('/(<(?:[^<>]+(?:"[^"]*"|\'[^\']*\')?)+>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		// remove empty lines
		$html_array = array('');
		$z = 1;
		$t = '';
		for ($x = 0; $x < count($html_array_temp); $x++) {
			$t = trim($html_array_temp[$x]);
			if ($t !== '') {
				$html_array[$z] = $html_array_temp[$x];
				$z++;
				// if the trimmed line was empty but the original wasn't, search for inline element closing tags in the last $html_array element
			} elseif ($t !== $html_array_temp[$x] && preg_match('/<\/' . $inlineElements . '( .*)?>/Usi', $html_array[$z - 1]) === 1) {
				$html_array[$z] = ' ';
				$z++;
			}
		}

		// rebuild html	
		$html = '';
		$tabs = 0;
		for ($x = 0; $x < count($html_array); $x++) {
			// check if the element should stand in a new line
			$newline = FALSE;
			if (substr($html_array[$x - 1], 0, 5) == '<?xml') {
				$newline = TRUE;
			} elseif ($this->conf['formatHtml'] == 2 && ( // minimalistic line break
					# this element has a line break before itself
					preg_match('/<' . $structureBoxLikeElements . '(.*)>/Usi', $html_array[$x]) || preg_match('/<' . $structureBoxLikeElements . '(.*) \/>/Usi', $html_array[$x]) || # one element before is a element that has a line break after
					preg_match('/<\/' . $structureBoxLikeElements . '(.*)>/Usi', $html_array[$x - 1]) || substr($html_array[$x - 1], 0, 4) == '<!--' || preg_match('/<' . $structureBoxLikeElements . '(.*) \/>/Usi', $html_array[$x - 1]))
			) {
				$newline = TRUE;
			} elseif ($this->conf['formatHtml'] == 3 && ( // aestetic line break
					# this element has a line break before itself
					preg_match('/<' . $esteticBoxLikeElements . '(.*)>/Usi', $html_array[$x]) || preg_match('/<' . $esteticBoxLikeElements . '(.*) \/>/Usi', $html_array[$x]) || # one element before is a element that has a line break after
					preg_match('/<\/' . $esteticBoxLikeElements . '(.*)>/Usi', $html_array[$x - 1]) || substr($html_array[$x - 1], 0, 4) == '<!--' || preg_match('/<' . $esteticBoxLikeElements . '(.*) \/>/Usi', $html_array[$x - 1]))
			) {
				$newline = TRUE;
			} elseif ($this->conf['formatHtml'] >= 4 && ( // logical line break
					# this element has a line break before itself
					preg_match('/<' . $allBoxLikeElements . '(.*)>/Usi', $html_array[$x]) || preg_match('/<' . $allBoxLikeElements . '(.*) \/>/Usi', $html_array[$x]) || # one element before is a element that has a line break after
					preg_match('/<\/' . $allBoxLikeElements . '(.*)>/Usi', $html_array[$x - 1]) || substr($html_array[$x - 1], 0, 4) == '<!--' || preg_match('/<' . $allBoxLikeElements . '(.*) \/>/Usi', $html_array[$x - 1]))
			) {
				$newline = TRUE;
			}

			// count down a tab
			if (substr($html_array[$x], 0, 2) == '</') {
				$tabs--;
			}

			// add tabs and line breaks in front of the current tag
			if ($newline) {
				$html .= $this->newline;
				for ($y = 0; $y < $tabs; $y++) {
					$html .= $this->tab;
				}
			}

			// remove white spaces and line breaks and add current tag to the html-string
			if (substr($html_array[$x - 1], 0, 4) == '<pre' // remove white space after line ending in PRE / TEXTAREA / comment
				|| substr($html_array[$x - 1], 0, 9) == '<textarea' || substr($html_array[$x - 1], 0, 4) == '<!--'
			) {
				$html .= $this->rTrimLines($html_array[$x]);
			} elseif (substr($html_array[$x], 0, 9) == '<![CDATA[' // remove multiple white space in CDATA / XML 
				|| substr($html_array[$x], 0, 5) == '<?xml'
			) {
				$html .= $this->killWhiteSpace($html_array[$x]);
			} else { // remove all line breaks
				$html .= $this->killLineBreaks($html_array[$x]);
			}

			// count up a tab
			if (substr($html_array[$x], 0, 1) == '<' && substr($html_array[$x], 1, 1) != '/') {
				if (substr($html_array[$x], 1, 1) != ' ' && substr($html_array[$x], 1, 3) != 'img' && substr($html_array[$x], 1, 2) != 'br' && substr($html_array[$x], 1, 2) != 'hr' && substr($html_array[$x], 1, 5) != 'input' && substr($html_array[$x], 1, 4) != 'link' && substr($html_array[$x], 1, 4) != 'meta' && substr($html_array[$x], 1, 4) != 'col ' && substr($html_array[$x], 1, 5) != 'frame' && substr($html_array[$x], 1, 7) != 'isindex' && substr($html_array[$x], 1, 5) != 'param' && substr($html_array[$x], 1, 4) != 'area' && substr($html_array[$x], 1, 4) != 'base' && substr($html_array[$x], 0, 2) != '<!' && substr($html_array[$x], 0, 5) != '<?xml') {
					$tabs++;
				}
			}
		}

		// Restore saved comments, styles and java-scripts
		for ($i = 0; $i < count($no_format); $i++) {
			$no_format[$i] = $this->rTrimLines($no_format[$i]); // remove white space after line ending
			$html = str_replace("<!-- ELEMENT $i -->", $no_format[$i], $html);
		}

		// include debug comment at the end
		if ($tabs != 0 && $this->conf['formatHtml.']['debugComment'] == 1) {
			$html .= '<!--' . $tabs . " open elements found-->\r\n";
		}
	}

	/**
	 * help function to formatHtml (and removeNewLines)
	 *  removes ALL line breaks and multiple white space
	 *
	 * @param     string $html    html-Code
	 *
	 * @return    string     html-Code
	 */
	function killLineBreaks($html) {
		$html = $this->convNlOs($html);
		$html = str_replace($this->newline, "", $html);
		// remove empty spaces between tags
		if ($this->conf['enable_utf-8_support'] == 1) {
			$html = preg_replace("/>\s*</uism", "><", $html);
		} else {
			$html = preg_replace("/>\s*</ism", "><", $html);
		}
		// remove double empty spaces
		if ($this->conf['enable_utf-8_support'] == 1) {
			$html = preg_replace('/\s\s+/u', ' ', $html);
		} else {
			$html = preg_replace('/\s\s+/', ' ', $html);
		}
		return $html;
	}

	/**
	 * help function to formatHtml
	 * removes multiple white space, keeps line breaks
	 *
	 * @param     string $html    html-Code
	 *
	 * @return    string     html-Code
	 */
	function killWhiteSpace($html) {
		$html = $this->convNlOs($html);
		$temp = explode($this->newline, $html);
		for ($i = 0; $i < count($temp); $i++) {
			if (!trim($temp[$i])) {
				unset($temp[$i]);
			} else {
				$temp[$i] = trim($temp[$i]);
				$temp[$i] = preg_replace('/\s\s+/', ' ', $temp[$i]);
			}
		}
		$html = implode($this->newline, $temp);
		return $html;
	}

	/**
	 * help function to formatHtml
	 *  removes white space at the end of lines, keeps other white space and line breaks
	 *
	 * @param     string $html    html-Code
	 *
	 * @return    string     html-Code
	 */
	function rTrimLines($html) {
		$html = $this->convNlOs($html);
		$temp = explode($this->newline, $html);
		for ($i = 0; $i < count($temp); $i++) {
			$temp[$i] = rtrim($temp[$i]);
		}
		$html = implode($this->newline, $temp);
		return $html;
	}

	/**
	 * help function to formatHtml
	 *  convert newlines according to the current OS
	 *
	 * @param     string $html    html-Code
	 *
	 * @return    string     html-Code
	 */
	function convNlOs($html) {
		$html = preg_replace("(\r\n|\n|\r)", $this->newline, $html);
		return $html;
	}

	/**
	 * get all css from the current document, move it all into an external file and do some optimization to the file
	 *
	 * @param   string $html
	 *
	 * @return  void
	 */
	function moveInlineCss(&$html) {
		// check, if directory exists
		$path_parts = pathinfo($this->conf['moveInlineCss.']['file']);
		if ("inline" != $this->conf['moveInlineCss.']['file'] && !is_dir(PATH_site . $path_parts['dirname'])) {
			debug(array('The directory "' . $path_parts['dirname'] . '" specified for the inline-css-file doesn\'t exist (extension source-optimization)'));
		} else {
			// get all inline-css
			$num_00 = preg_match_all("/<style.+?>.+<\/style>/is", $html, $matches);
			// if there are any reasults, try to open/create the css-file and insert the optimized css into this file
			if ($num_00) {
				if (count($matches[0]) > 1) {
					$css_content_new = implode($this->newline, $matches[0]);
				} else {
					$css_content_new = $matches[0][0];
				}
				// remove style-tags, html-comments and css-comments
				$css_content_new = preg_replace("/<\/style>/i", "", $css_content_new);
				$css_content_new = preg_replace("/<style.+?>/i", "", $css_content_new);
				$css_content_new = str_replace("<!--", "", $css_content_new);
				$css_content_new = str_replace("-->", "", $css_content_new);
				$css_content_new = preg_replace("/\/\*.+?\*\//is", "", $css_content_new);
				// optimization of the css
				$this->trimLines($css_content_new);
				$this->removeEmptyLines($css_content_new);
				#$this->removeNewLines (&$css_content_new);                             
				// check, if the css-file already exists: if so, compare the css with the new css and write it if any
				// chances and of course if no file exists; if they are the same, don't do anything
				if ("inline" != $this->conf['moveInlineCss.']['file']) {
					if (!file_exists(PATH_site . $this->conf['moveInlineCss.']['file'])) {
						$handle = fopen(PATH_site . $this->conf['moveInlineCss.']['file'], "w+");
						fwrite($handle, $css_content_new);
						fclose($handle);
					} else if (file_exists(PATH_site . $this->conf['moveInlineCss.']['file'])) {
						$handle = fopen(PATH_site . $this->conf['moveInlineCss.']['file'], "r");
						if ($handle) {
							$file_content = fread($handle, filesize(PATH_site . $this->conf['moveInlineCss.']['file']));
							if ($file_content != $css_content_new) {
								$handle_w = fopen(PATH_site . $this->conf['moveInlineCss.']['file'], "w");
								fwrite($handle_w, $css_content_new);
								fclose($handle_w);
							}
						}
						fclose($handle);
					}
					// replace css in source with path to css-file
					// media:
					$css_media_cObj = t3lib_div::makeInstance('tslib_cObj');
					$css_media_cObj->start($GLOBALS['TSFE']->page, 'pages');
					$css_media = $css_media_cObj->stdWrap($this->conf['moveInlineCss.']['media'], $this->conf['moveInlineCss.']['media.']);
					$css_media = ($css_media != "") ? ' media="' . $css_media . '"' : "";
					// href:
					$css_path = 'http://' . t3lib_div::getThisUrl() . $this->conf['moveInlineCss.']['file'];
					// link
					$css_link = '<link rel="stylesheet" href="' . $css_path . '"' . $css_media . ' />';
					$html = preg_replace("/<style.+?>.+<\/style>/is", $css_link, $html, 1);
					$html = preg_replace("/<style.+?>.+<\/style>/is", "", $html);
				} else {
					$css_final = "<style type=\"text/css\">/*<![CDATA[*/<!--\n" . $css_content_new . "\n-->/*]]>*/</style>";
					$html = preg_replace("/<style.+?>.+<\/style>/is", "", $html);
					if (FALSE == stripos($html, "</head>")) {
						debug("Unable to find </head> tag in HTML output. Inserting CSS will fail!");
					}
					$html = preg_replace("/<\/head>/is", $css_final . "</head>", $html, 1);
				}
			}
		}
	}

	/**
	 * help function for moveInlineCSS
	 * removes tabs and empty spaces bevore and after lines, transforms linebreaks system conform
	 *
	 * @param    string $html    Html-Code
	 *
	 * @return   void
	 */
	function trimLines(&$html) {
		$html = str_replace("\t", "", $html);
		// convert newlines according to the current OS
		if (TYPO3_OS == "WIN") {
			$html = str_replace("\n", "\r\n", $html);
		} else {
			$html = str_replace("\r\n", "\n", $html);
		}
		$temp = explode($this->newline, $html);
		array_walk($temp, array(
		                       $this,
		                       'trimLines_callback'
		                  ));
		$html = implode($this->newline, $temp);
		unset($temp);
	}

	/**
	 * help function to trimLines
	 *
	 * @param $value
	 * @param $key
	 */
	function trimLines_callback(&$value, $key) {
		$value = trim($value);
	}

	/**
	 * help function for moveInlineCSS
	 * removes empty lines
	 *
	 * @param    string-reff    html-Code
	 *
	 * @return   void
	 */
	function removeEmptyLines(&$html) {
		$temp = explode($this->newline, $html);
		$result = array();
		for ($i = 0; $i < count($temp); ++$i) {
			if ("" == trim($temp[$i])) {
				continue;
			}
			$result[] = $temp[$i];
		}
		$html = implode($this->newline, $result);
	}

	/**
	 * help function for moveInlineCSS
	 * removes new lines where unnecessary
	 * spares line breaks within: pre, textarea, ...
	 *
	 * @param     string-reff     html-Code
	 *
	 * @return    void
	 */
	function removeNewLines(&$html) {
		$splitArray = array(
			'textarea',
			'pre'
		); // eventuell auch: span, script, style
		$peaces = preg_split('#(<(' . implode('|', $splitArray) . ').*>.*</\2>)#Uis', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
		$html = "";
		for ($i = 0; $i < count($peaces); $i++) {
			if (($i + 1) % 3 == 0) {
				continue;
			}
			$html .= (($i - 1) % 3 != 0) ? $this->killLineBreaks($peaces[$i]) : $peaces[$i];
		}
	}

	/**
	 *
	 * @param string $html
	 */
	function removeLinkSchema(&$html) {
		$html = preg_replace("/<link rel=\"?schema.dc\"?.+?>/is", "", $html);
	}

	/**
	 *
	 * @param string $html
	 */
	function replaceClearGif(&$html) {
		$html = str_replace("clear.gif", $this->conf['replace_clear_gif'], $html);
	}

	/**
	 *
	 * @param string $html
	 */
	function removeEmptyAltAtr(&$html) {
		$html = str_replace("alt=\"\"", "", $html);
	}

	/**
	 *
	 * @param string $html
	 */
	function removeRealUrlBrokenRootLink(&$html) {
		$html = str_replace('href=".html"', 'href=""', $html);
	}

	/**
	 * remove all attributes from the body-tag
	 *
	 * @param     nothing
	 *
	 * @return    void
	 */
	function emtpyBodyTag(&$html) {
		$html = preg_replace("/<body.*>/Usi", "<body>", $html);
	}

}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/sourceopt/pi1/class.tx_sourceopt_pi1.php"]) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/sourceopt/pi1/class.tx_sourceopt_pi1.php"]);
}
?>