<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2007 Tim Lochmueller, Sareen Millet, Dr. Ronald Steiner
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

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once (t3lib_extMgm::extPath('fl_realurl_image') . 'Classes/class.tx_flrealurlimage.php');  # the main class of fl_realurl_image

/**
 * Extends tslib_cObj to change the path for the images
 *
 */
class ux_tslib_cObj extends tslib_cObj {

	/**
	 * Rendering the cObject, IMG_RESOURCE
	 *
	 * @param	array		Array of TypoScript properties
	 * @return	string		Output
	 * @link http://typo3.org/doc.0.html?&tx_extrepmgm_pi1[extUid]=270&tx_extrepmgm_pi1[tocEl]=354&cHash=46f9299706
	 * @see getImgResource()
	 */
	function IMG_RESOURCE($conf) {
		$GLOBALS['TSFE']->lastImgResourceInfo = $this->getImgResource($conf['file'], $conf['file.']);
		###################################
		## Here begins RealUrl_image ######
		###################################
		// call fl_realurl_image to generate $new_fileName
		$tx_flrealurlimage = new tx_flrealurlimage();
		$tx_flrealurlimage->start($this->data, $this->table);
		$new_fileName = $tx_flrealurlimage->main($conf, $GLOBALS['TSFE']->lastImgResourceInfo);
		// generate the image URL
		$theValue = htmlspecialchars($GLOBALS['TSFE']->absRefPrefix) . $new_fileName;
		// stdWrap and return	
		return $this->stdWrap($theValue, $conf['stdWrap.']);
		##################################
		### Here ends RealURL_Image ######
		##################################
		/*
		  return $this->stdWrap($GLOBALS['TSFE']->lastImgResourceInfo[3],$conf['stdWrap.']);
		 */
	}

	/**
	 * Returns a <img> tag with the image file defined by $file and processed according to the properties in the TypoScript array.
	 * Mostly this function is a sub-function to the IMAGE function which renders the IMAGE cObject in TypoScript. This function is called by "$this->cImage($conf['file'],$conf);" from IMAGE().
	 *
	 * @param		string		File TypoScript resource
	 * @param		array		TypoScript configuration properties for the IMAGE object
	 * @return		string		<img> tag, (possibly wrapped in links and other HTML) if any image found.
	 * @access private
	 * @see IMAGE()
	 */
	function cImage($file, $conf) {
		$info = $this->getImgResource($file, $conf['file.']);
		$GLOBALS['TSFE']->lastImageInfo = $info;
		if (is_array($info)) {
			$info[3] = t3lib_div::png_to_gif_by_imagemagick($info[3]);
			$GLOBALS['TSFE']->imagesOnPage[] = $info[3];  // This array is used to collect the image-refs on the page...

			if (!strlen($conf['altText']) && !is_array($conf['altText.'])) { // Backwards compatible:
				$conf['altText'] = $conf['alttext'];
				$conf['altText.'] = $conf['alttext.'];
			}
			$altParam = $this->getAltParam($conf);
			###################################
			## Here begins RealUrl_image ######
			###################################
			// call fl_realurl_image to generate $new_fileName
			$tx_flrealurlimage = new tx_flrealurlimage();
			$tx_flrealurlimage->start($this->data, $this->table);
			$new_fileName = $tx_flrealurlimage->main($conf, $info);
			// generate the <img>-tag

			if (isset($conf['params.']) && is_array($conf['params.']))
				$conf['params'] = $this->stdWrap($conf['params'], $conf['params.']);

			$theValue = '<img src="'
				   . htmlspecialchars($GLOBALS['TSFE']->absRefPrefix) . $new_fileName . '"'
				   . ' width="' . $info[0] . '" height="' . $info[1] . '"'
				   . $this->getBorderAttr(' border="' . intval($conf['border']) . '"')
				   . ($conf['params'] ? ' ' . $conf['params'] : '') . ($altParam)
				   . ' />';
			##################################
			### Here ends RealURL_Image ######
			##################################
			/*
			  $theValue = '<img src="'.htmlspecialchars($GLOBALS['TSFE']->absRefPrefix.t3lib_div::rawUrlEncodeFP($info[3])).'" width="'.$info[0].'" height="'.$info[1].'"'.$this->getBorderAttr(' border="'.intval($conf['border']).'"').(($conf['params'] || is_array($conf['params.']))?' '.$this->stdwrap($conf['params'],$conf['params.']):'').($altParam).' />';
			 */
			if ($conf['linkWrap']) {
				$theValue = $this->linkWrap($theValue, $conf['linkWrap']);
			} elseif ($conf['imageLinkWrap']) {
				$theValue = $this->imageLinkWrap($theValue, $info['origFile'], $conf['imageLinkWrap.']);
			}
			return $this->wrap($theValue, $conf['wrap']);
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fl_realurl_image/Classes/class.ux_tslib_cObj.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fl_realurl_image/Classes/class.ux_tslib_cObj.php']);
}
?>