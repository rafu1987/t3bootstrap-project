<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ux_tslib_content_ImageResource extends tslib_content_ImageResource {

	public function render($conf = array()) {
		$GLOBALS['TSFE']->lastImgResourceInfo = $this->cObj->getImgResource($conf['file'], $conf['file.']);

		###################################
		## Here begins RealUrl_image ######
		###################################
		if (is_array($GLOBALS['TSFE']->lastImgResourceInfo)) {
			// call fl_realurl_image to generate $new_fileName
			$tx_flrealurlimage = new tx_flrealurlimage();
			$tx_flrealurlimage->start($this->data, $this->table);
			$new_fileName = $tx_flrealurlimage->main($conf, $GLOBALS['TSFE']->lastImgResourceInfo);
			// generate the image URL
			$theValue = htmlspecialchars($GLOBALS['TSFE']->absRefPrefix) . $new_fileName;
			// stdWrap and return
			return $this->getContentObject()->stdWrap($theValue, $conf['stdWrap.']);
		}
		##################################
		### Here ends RealURL_Image ######
		##################################


		$imageResource = $GLOBALS['TSFE']->lastImgResourceInfo[3];

		$theValue = isset($conf['stdWrap.']) ? $this->cObj->stdWrap($imageResource, $conf['stdWrap.']) : $imageResource;

		return $theValue;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fl_realurl_image/Classes/XClass/ImageResource.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fl_realurl_image/Classes/XClass/ImageResource.php']);
}
?>