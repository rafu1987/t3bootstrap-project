<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Juergen Furrer <juergen.furrer@gmail.com>
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

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('jfmulticontent').'lib/class.tx_jfmulticontent_pagerenderer.php');

/**
 * Plugin 'Multiple Content' for the 'jfmulticontent' extension.
 *
 * @author     Juergen Furrer <juergen.furrer@gmail.com>
 * @package    TYPO3
 * @subpackage tx_jfmulticontent
 */
class tx_jfmulticontent_pi1 extends tslib_pibase
{
	public $prefixId      = 'tx_jfmulticontent_pi1';
	public $scriptRelPath = 'pi1/class.tx_jfmulticontent_pi1.php';
	public $extKey        = 'jfmulticontent';
	public $pi_checkCHash = TRUE;
	public $conf = array();
	private $lConf = array();
	private $confArr = array();
	private $templateFile = NULL;
	private $templateFileJS = NULL;
	private $templatePart = NULL;
	private $additionalMarker = array();
	private $contentKey = NULL;
	private $contentCount = NULL;
	private $contentClass = array();
	private $classes = array();
	private $contentWrap = array();
	private $titles = array();
	private $attributes = array();
	private $cElements = array();
	private $rels = array();
	private $content_id = array();
	private $piFlexForm = array();
	private $pagerenderer = NULL;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	public function main($content, $conf)
	{
		$this->content = $content;
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		// get the config from EXT
		$this->confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);

		$this->pagerenderer = t3lib_div::makeInstance('tx_jfmulticontent_pagerenderer');
		$this->pagerenderer->setConf($this->conf);

		// Plugin or template?
		if ($this->cObj->data['list_type'] == $this->extKey.'_pi1') {

			// It's a content, all data from flexform

			$this->lConf['style'] = $this->getFlexformData('general', 'style');

			if ($this->lConf['style'] != 'typoscript') {
				$this->lConf['columnOrder'] = $this->getFlexformData('general', 'columnOrder', in_array($this->lConf['style'], array('2column','3column','4column','5column')));
				$this->lConf['column1']     = $this->getFlexformData('general', 'column1', in_array($this->lConf['style'], array('2column','3column','4column','5column')));
				$this->lConf['column2']     = $this->getFlexformData('general', 'column2', in_array($this->lConf['style'], array('2column','3column','4column','5column')));
				$this->lConf['column3']     = $this->getFlexformData('general', 'column3', in_array($this->lConf['style'], array('3column','4column','5column')));
				$this->lConf['column4']     = $this->getFlexformData('general', 'column4', in_array($this->lConf['style'], array('4column','5column')));
				$this->lConf['column5']     = $this->getFlexformData('general', 'column5', in_array($this->lConf['style'], array('5column')));
				$this->lConf['equalize']    = $this->getFlexformData('general', 'equalize', in_array($this->lConf['style'], array('1column','2column','3column','4column','5column')));

				$debuglog = ($this->lConf['style'] == 'tab');
				$this->lConf['tabCollapsible']   = $this->getFlexformData('general', 'tabCollapsible', $debuglog);
				$this->lConf['tabOpen']          = $this->getFlexformData('general', 'tabOpen', $debuglog);
				$this->lConf['tabRandomContent'] = $this->getFlexformData('general', 'tabRandomContent', $debuglog);
				$this->lConf['tabEvent']         = $this->getFlexformData('general', 'tabEvent', $debuglog);
				$this->lConf['tabHeightStyle']   = $this->getFlexformData('general', 'tabHeightStyle', $debuglog);
				$this->lConf['tabCookieExpires'] = $this->getFlexformData('general', 'tabCookieExpires', $debuglog);
				$this->lConf['tabCookieRoot']    = $this->getFlexformData('general', 'tabCookieRoot', $debuglog);
				$this->lConf['tabHideEffect']             = $this->getFlexformData('general', 'tabHideEffect', $debuglog);
				$this->lConf['tabHideTransition']         = $this->getFlexformData('general', 'tabHideTransition', $debuglog);
				$this->lConf['tabHideTransitiondir']      = $this->getFlexformData('general', 'tabHideTransitiondir', $debuglog);
				$this->lConf['tabHideTransitionduration'] = $this->getFlexformData('general', 'tabHideTransitionduration', $debuglog);
				$this->lConf['tabShowEffect']             = $this->getFlexformData('general', 'tabShowEffect', $debuglog);
				$this->lConf['tabShowTransition']         = $this->getFlexformData('general', 'tabShowTransition', $debuglog);
				$this->lConf['tabShowTransitiondir']      = $this->getFlexformData('general', 'tabShowTransitiondir', $debuglog);
				$this->lConf['tabShowTransitionduration'] = $this->getFlexformData('general', 'tabShowTransitionduration', $debuglog);

				$debuglog = ($this->lConf['style'] == 'accordion');
				$this->lConf['accordionCollapsible']        = $this->getFlexformData('general', 'accordionCollapsible', $debuglog);
				$this->lConf['accordionClosed']             = $this->getFlexformData('general', 'accordionClosed', $debuglog);
				$this->lConf['accordionOpen']               = $this->getFlexformData('general', 'accordionOpen', $debuglog);
				$this->lConf['accordionRandomContent']      = $this->getFlexformData('general', 'accordionRandomContent', $debuglog);
				$this->lConf['accordionEvent']              = $this->getFlexformData('general', 'accordionEvent', $debuglog);
				$this->lConf['accordionHeightStyle']        = $this->getFlexformData('general', 'accordionHeightStyle', $debuglog);
				$this->lConf['accordionAnimate']            = $this->getFlexformData('general', 'accordionAnimate', $debuglog);
				$this->lConf['accordionTransition']         = $this->getFlexformData('general', 'accordionTransition', $debuglog);
				$this->lConf['accordionTransitiondir']      = $this->getFlexformData('general', 'accordionTransitiondir', $debuglog);
				$this->lConf['accordionTransitionduration'] = $this->getFlexformData('general', 'accordionTransitionduration', $debuglog);

				$debuglog = ($this->lConf['style'] == 'slider');
				$this->lConf['sliderWidth']              = $this->getFlexformData('general', 'sliderWidth', $debuglog);
				$this->lConf['sliderHeight']             = $this->getFlexformData('general', 'sliderHeight', $debuglog);
				$this->lConf['sliderResizeContents']     = $this->getFlexformData('general', 'sliderResizeContents', $debuglog);
				$this->lConf['sliderTheme']              = $this->getFlexformData('general', 'sliderTheme', $debuglog);
				$this->lConf['sliderMode']               = $this->getFlexformData('general', 'sliderMode', $debuglog);
				$this->lConf['sliderOpen']               = $this->getFlexformData('general', 'sliderOpen', $debuglog);
				$this->lConf['sliderRandomContent']      = $this->getFlexformData('general', 'sliderRandomContent', $debuglog);
				$this->lConf['sliderHashTags']           = $this->getFlexformData('general', 'sliderHashTags', $debuglog);
				$this->lConf['sliderBuildArrows']        = $this->getFlexformData('general', 'sliderBuildArrows', $debuglog);
				$this->lConf['sliderToggleArrows']       = $this->getFlexformData('general', 'sliderToggleArrows', $debuglog);
				$this->lConf['sliderNavigation']         = $this->getFlexformData('general', 'sliderNavigation', $debuglog);
				$this->lConf['sliderStartStop']          = $this->getFlexformData('general', 'sliderStartStop', $debuglog);
				$this->lConf['sliderPanelFromHeader']    = $this->getFlexformData('general', 'sliderPanelFromHeader', $debuglog);
				$this->lConf['sliderToggleControls']     = $this->getFlexformData('general', 'sliderToggleControls', $debuglog);
				$this->lConf['sliderAutoStart']          = $this->getFlexformData('general', 'sliderAutoStart', $debuglog);
				$this->lConf['sliderPauseOnHover']       = $this->getFlexformData('general', 'sliderPauseOnHover', $debuglog);
				$this->lConf['sliderAllowRapidChange']   = $this->getFlexformData('general', 'sliderAllowRapidChange', $debuglog);
				$this->lConf['sliderResumeOnVideoEnd']   = $this->getFlexformData('general', 'sliderResumeOnVideoEnd', $debuglog);
				$this->lConf['sliderStopAtEnd']          = $this->getFlexformData('general', 'sliderStopAtEnd', $debuglog);
				$this->lConf['sliderPlayRtl']            = $this->getFlexformData('general', 'sliderPlayRtl', $debuglog);
				$this->lConf['sliderTransition']         = $this->getFlexformData('general', 'sliderTransition', $debuglog);
				$this->lConf['sliderTransitiondir']      = $this->getFlexformData('general', 'sliderTransitiondir', $debuglog);
				$this->lConf['sliderTransitionduration'] = $this->getFlexformData('general', 'sliderTransitionduration', $debuglog);
				$this->lConf['sliderAutoplay']           = $this->getFlexformData('general', 'sliderAutoplay', $debuglog);

				$debuglog = ($this->lConf['style'] == 'slidedeck');
				$this->lConf['slidedeckHeight']             = $this->getFlexformData('general', 'slidedeckHeight', $debuglog);
				$this->lConf['slidedeckTransition']         = $this->getFlexformData('general', 'slidedeckTransition', $debuglog);
				$this->lConf['slidedeckTransitiondir']      = $this->getFlexformData('general', 'slidedeckTransitiondir', $debuglog);
				$this->lConf['slidedeckTransitionduration'] = $this->getFlexformData('general', 'slidedeckTransitionduration', $debuglog);
				$this->lConf['slidedeckStart']              = $this->getFlexformData('general', 'slidedeckStart', $debuglog);
				$this->lConf['slidedeckActivecorner']       = $this->getFlexformData('general', 'slidedeckActivecorner', $debuglog);
				$this->lConf['slidedeckIndex']              = $this->getFlexformData('general', 'slidedeckIndex', $debuglog);
				$this->lConf['slidedeckScroll']             = $this->getFlexformData('general', 'slidedeckScroll', $debuglog);
				$this->lConf['slidedeckKeys']               = $this->getFlexformData('general', 'slidedeckKeys', $debuglog);
				$this->lConf['slidedeckHidespines']         = $this->getFlexformData('general', 'slidedeckHidespines', $debuglog);

				$debuglog = ($this->lConf['style'] == 'easyaccordion');
				$this->lConf['easyaccordionSkin']     = $this->getFlexformData('general', 'easyaccordionSkin', $debuglog);
				$this->lConf['easyaccordionOpen']     = $this->getFlexformData('general', 'easyaccordionOpen', $debuglog);
				$this->lConf['easyaccordionWidth']    = $this->getFlexformData('general', 'easyaccordionWidth', $debuglog);
				$this->lConf['easyaccordionSlideNum'] = $this->getFlexformData('general', 'easyaccordionSlideNum', $debuglog);

				$debuglog = ($this->lConf['style'] == 'booklet');
				$this->lConf['bookletWidth']         = $this->getFlexformData('general', 'bookletWidth', $debuglog);
				$this->lConf['bookletHeight']        = $this->getFlexformData('general', 'bookletHeight', $debuglog);
				$this->lConf['bookletSpeed']         = $this->getFlexformData('general', 'bookletSpeed', $debuglog);
				$this->lConf['bookletStartingPage']  = $this->getFlexformData('general', 'bookletStartingPage', $debuglog);
				$this->lConf['bookletRTL']           = $this->getFlexformData('general', 'bookletRTL', $debuglog);
				$this->lConf['bookletTransition']    = $this->getFlexformData('general', 'bookletTransition', $debuglog);
				$this->lConf['bookletTransitiondir'] = $this->getFlexformData('general', 'bookletTransitiondir', $debuglog);
				$this->lConf['bookletPagePadding']   = $this->getFlexformData('general', 'bookletPagePadding', $debuglog);
				$this->lConf['bookletPageNumbers']   = $this->getFlexformData('general', 'bookletPageNumbers', $debuglog);
				$this->lConf['bookletManual']        = $this->getFlexformData('general', 'bookletManual', $debuglog);
				$this->lConf['bookletShadows']       = $this->getFlexformData('general', 'bookletShadows', $debuglog);
				$this->lConf['bookletClosed']        = $this->getFlexformData('general', 'bookletClosed', $debuglog);
				$this->lConf['bookletCovers']        = $this->getFlexformData('general', 'bookletCovers', $debuglog);
				$this->lConf['bookletAutoCenter']    = $this->getFlexformData('general', 'bookletAutoCenter', $debuglog);
				$this->lConf['bookletHash']          = $this->getFlexformData('general', 'bookletHash', $debuglog);
				$this->lConf['bookletKeyboard']      = $this->getFlexformData('general', 'bookletKeyboard', $debuglog);
				$this->lConf['bookletAuto']          = $this->getFlexformData('general', 'bookletAuto', $debuglog);
				$this->lConf['bookletDelay']         = $this->getFlexformData('general', 'bookletDelay', $debuglog);
				$this->lConf['bookletOverlays']      = $this->getFlexformData('general', 'bookletOverlays', $debuglog);
				$this->lConf['bookletArrows']        = $this->getFlexformData('general', 'bookletArrows', $debuglog);
				$this->lConf['bookletArrowsHide']    = $this->getFlexformData('general', 'bookletArrows', $debuglog);
				$this->lConf['bookletHovers']        = $this->getFlexformData('general', 'bookletHovers', $debuglog);

				$this->lConf['delayDuration'] = $this->getFlexformData('general', 'delayDuration', in_array($this->lConf['style'], array('slider','slidedeck','easyaccordion')));
				$this->lConf['autoplayCycle'] = $this->getFlexformData('general', 'autoplayCycle', ($this->lConf['style'] == 'slidedeck'));

				// columns
				$this->conf['config.']['column1']     = $this->lConf['column1'];
				$this->conf['config.']['column2']     = $this->lConf['column2'];
				$this->conf['config.']['column3']     = $this->lConf['column3'];
				$this->conf['config.']['column4']     = $this->lConf['column4'];
				$this->conf['config.']['column5']     = $this->lConf['column5'];
				$this->conf['config.']['columnOrder'] = $this->lConf['columnOrder'];
				if ($this->lConf['equalize'] < 2) {
					$this->conf['config.']['equalize'] = $this->lConf['equalize'];
				}
				// tab
				if ($this->lConf['tabCollapsible'] < 2) {
					$this->conf['config.']['tabCollapsible'] = $this->lConf['tabCollapsible'];
				}
				if ($this->lConf['tabOpen'] >= 0) {
					$this->conf['config.']['tabOpen'] = $this->lConf['tabOpen'];
				}
				if ($this->lConf['tabRandomContent'] < 2) {
					$this->conf['config.']['tabRandomContent'] = $this->lConf['tabRandomContent'];
				}
				if (strlen($this->lConf['tabCookieExpires']) > 0) {
					$this->conf['config.']['tabCookieExpires'] = $this->lConf['tabCookieExpires'];
				}
				if ($this->lConf['tabCookieRoot'] < 2) {
					$this->conf['config.']['tabCookieRoot'] = $this->lConf['tabCookieRoot'];
				}
				if ($this->lConf['tabHideEffect']) {
					$this->conf['config.']['tabHideEffect'] = $this->lConf['tabHideEffect'];
				}
				if ($this->lConf['tabHideTransition']) {
					$this->conf['config.']['tabHideTransition'] = $this->lConf['tabHideTransition'];
				}
				if ($this->lConf['tabHideTransitiondir']) {
					$this->conf['config.']['tabHideTransitiondir'] = $this->lConf['tabHideTransitiondir'];
				}
				if ($this->lConf['tabHideTransitionduration'] > 0) {
					$this->conf['config.']['tabHideTransitionduration'] = $this->lConf['tabHideTransitionduration'];
				}
				if ($this->lConf['tabShowEffect']) {
					$this->conf['config.']['tabShowEffect'] = $this->lConf['tabShowEffect'];
				}
				if ($this->lConf['tabShowTransition']) {
					$this->conf['config.']['tabShowTransition'] = $this->lConf['tabShowTransition'];
				}
				if ($this->lConf['tabShowTransitiondir']) {
					$this->conf['config.']['tabShowTransitiondir'] = $this->lConf['tabShowTransitiondir'];
				}
				if ($this->lConf['tabShowTransitionduration'] > 0) {
					$this->conf['config.']['tabShowTransitionduration'] = $this->lConf['tabShowTransitionduration'];
				}
				if (in_array($this->lConf['tabEvent'], array('click', 'mouseover'))) {
					$this->conf['config.']['tabEvent'] = $this->lConf['tabEvent'];
				}
				if (in_array($this->lConf['tabHeightStyle'], array('auto', 'fill', 'content'))) {
					$this->conf['config.']['tabHeightStyle'] = $this->lConf['tabHeightStyle'];
				}

				// accordion
				if ($this->lConf['accordionCollapsible'] < 2) {
					$this->conf['config.']['accordionCollapsible'] = $this->lConf['accordionCollapsible'];
				}
				if ($this->lConf['accordionClosed'] < 2) {
					$this->conf['config.']['accordionClosed'] = $this->lConf['accordionClosed'];
				}
				if ($this->lConf['accordionOpen'] > 0) {
					$this->conf['config.']['accordionOpen'] = $this->lConf['accordionOpen'];
				}
				if ($this->lConf['accordionRandomContent'] < 2) {
					$this->conf['config.']['accordionRandomContent'] = $this->lConf['accordionRandomContent'];
				}
				if ($this->lConf['accordionEvent']) {
					$this->conf['config.']['accordionEvent'] = $this->lConf['accordionEvent'];
				}
				if (in_array($this->lConf['accordionHeightStyle'], array('auto', 'fill', 'content'))) {
					$this->conf['config.']['accordionHeightStyle'] = $this->lConf['accordionHeightStyle'];
				}
				if ($this->lConf['accordionAnimate'] < 2) {
					$this->conf['config.']['accordionAnimate'] = $this->lConf['accordionAnimate'];
				}
				if ($this->lConf['accordionTransition']) {
					$this->conf['config.']['accordionTransition'] = $this->lConf['accordionTransition'];
				}
				if ($this->lConf['accordionTransitiondir']) {
					$this->conf['config.']['accordionTransitiondir'] = $this->lConf['accordionTransitiondir'];
				}
				if ($this->lConf['accordionTransitionduration'] > 0) {
					$this->conf['config.']['accordionTransitionduration'] = $this->lConf['accordionTransitionduration'];
				}
				// slider
				if ($this->lConf['sliderWidth']) {
					$this->conf['config.']['sliderWidth'] = $this->lConf['sliderWidth'];
				}
				if ($this->lConf['sliderHeight']) {
					$this->conf['config.']['sliderHeight'] = $this->lConf['sliderHeight'];
				}
				if ($this->lConf['sliderResizeContents'] < 2) {
					$this->conf['config.']['sliderResizeContents'] = $this->lConf['sliderResizeContents'];
				}
				if ($this->lConf['sliderTheme']) {
					$this->conf['config.']['sliderTheme'] = $this->lConf['sliderTheme'];
				}
				if ($this->lConf['sliderMode']) {
					$this->conf['config.']['sliderMode'] = $this->lConf['sliderMode'];
				}
				if ($this->lConf['sliderOpen'] > 0) {
					$this->conf['config.']['sliderOpen'] = $this->lConf['sliderOpen'];
				}
				if ($this->lConf['sliderRandomContent'] < 2) {
					$this->conf['config.']['sliderRandomContent'] = $this->lConf['sliderRandomContent'];
				}
				if ($this->lConf['sliderHashTags'] < 2) {
					$this->conf['config.']['sliderHashTags'] = $this->lConf['sliderHashTags'];
				}
				if ($this->lConf['sliderBuildArrows'] < 2) {
					$this->conf['config.']['sliderBuildArrows'] = $this->lConf['sliderBuildArrows'];
				}
				if ($this->lConf['sliderToggleArrows'] < 2) {
					$this->conf['config.']['sliderToggleArrows'] = $this->lConf['sliderToggleArrows'];
				}
				if ($this->lConf['sliderNavigation'] < 2) {
					$this->conf['config.']['sliderNavigation'] = $this->lConf['sliderNavigation'];
				}
				if ($this->lConf['sliderStartStop'] < 2) {
					$this->conf['config.']['sliderStartStop'] = $this->lConf['sliderStartStop'];
				}
				if ($this->lConf['sliderPanelFromHeader'] < 2) {
					$this->conf['config.']['sliderPanelFromHeader'] = $this->lConf['sliderPanelFromHeader'];
				}
				if ($this->lConf['sliderToggleControls'] < 2) {
					$this->conf['config.']['sliderToggleControls'] = $this->lConf['sliderToggleControls'];
				}
				if ($this->lConf['sliderAutoStart'] < 2) {
					$this->conf['config.']['sliderAutoStart'] = $this->lConf['sliderAutoStart'];
				}
				if ($this->lConf['sliderPauseOnHover'] < 2) {
					$this->conf['config.']['sliderPauseOnHover'] = $this->lConf['sliderPauseOnHover'];
				}
				if ($this->lConf['sliderAllowRapidChange'] < 2) {
					$this->conf['config.']['sliderAllowRapidChange'] = $this->lConf['sliderAllowRapidChange'];
				}
				if ($this->lConf['sliderResumeOnVideoEnd'] < 2) {
					$this->conf['config.']['sliderResumeOnVideoEnd'] = $this->lConf['sliderResumeOnVideoEnd'];
				}
				if ($this->lConf['sliderStopAtEnd'] < 2) {
					$this->conf['config.']['sliderStopAtEnd'] = $this->lConf['sliderStopAtEnd'];
				}
				if ($this->lConf['sliderPlayRtl'] < 2) {
					$this->conf['config.']['sliderPlayRtl'] = $this->lConf['sliderPlayRtl'];
				}
				if ($this->lConf['sliderTransition']) {
					$this->conf['config.']['sliderTransition'] = $this->lConf['sliderTransition'];
				}
				if ($this->lConf['sliderTransitiondir']) {
					$this->conf['config.']['sliderTransitiondir'] = $this->lConf['sliderTransitiondir'];
				}
				if ($this->lConf['sliderTransitionduration'] > 0) {
					$this->conf['config.']['sliderTransitionduration'] = $this->lConf['sliderTransitionduration'];
				}
				if ($this->lConf['sliderAutoplay'] < 2) {
					$this->conf['config.']['sliderAutoplay'] = $this->lConf['sliderAutoplay'];
				}
				// slidedeck
				if ($this->lConf['slidedeckHeight'] > 0) {
					$this->conf['config.']['slidedeckHeight'] = $this->lConf['slidedeckHeight'];
				}
				if ($this->lConf['slidedeckTransition']) {
					$this->conf['config.']['slidedeckTransition'] = $this->lConf['slidedeckTransition'];
				}
				if ($this->lConf['slidedeckTransitiondir']) {
					$this->conf['config.']['slidedeckTransitiondir'] = $this->lConf['slidedeckTransitiondir'];
				}
				if ($this->lConf['slidedeckTransitionduration'] > 0) {
					$this->conf['config.']['slidedeckTransitionduration'] = $this->lConf['slidedeckTransitionduration'];
				}
				if ($this->lConf['slidedeckStart'] > 0) {
					$this->conf['config.']['slidedeckStart'] = $this->lConf['slidedeckStart'];
				}
				if ($this->lConf['slidedeckActivecorner'] < 2) {
					$this->conf['config.']['slidedeckActivecorner'] = $this->lConf['slidedeckActivecorner'];
				}
				if ($this->lConf['slidedeckIndex'] < 2) {
					$this->conf['config.']['slidedeckIndex'] = $this->lConf['slidedeckIndex'];
				}
				if ($this->lConf['slidedeckScroll'] < 2) {
					$this->conf['config.']['slidedeckScroll'] = $this->lConf['slidedeckScroll'];
				}
				if ($this->lConf['slidedeckKeys'] < 2) {
					$this->conf['config.']['slidedeckKeys'] = $this->lConf['slidedeckKeys'];
				}
				if ($this->lConf['slidedeckHidespines'] < 2) {
					$this->conf['config.']['slidedeckHidespines'] = $this->lConf['slidedeckHidespines'];
				}
				// easyAccordion
				if ($this->lConf['easyaccordionSkin']) {
					$this->conf['config.']['easyaccordionSkin'] = $this->lConf['easyaccordionSkin'];
				}
				if ($this->lConf['easyaccordionOpen'] > 0) {
					$this->conf['config.']['easyaccordionOpen'] = $this->lConf['easyaccordionOpen'];
				}
				if ($this->lConf['easyaccordionWidth'] > 0) {
					$this->conf['config.']['easyaccordionWidth'] = $this->lConf['easyaccordionWidth'];
				}
				if ($this->lConf['easyaccordionSlideNum'] < 2) {
					$this->conf['config.']['easyaccordionSlideNum'] = $this->lConf['easyaccordionSlideNum'];
				}
				// booklet
				if ($this->lConf['bookletWidth'] > 0) {
					$this->conf['config.']['bookletWidth'] = $this->lConf['bookletWidth'];
				}
				if ($this->lConf['bookletHeight'] > 0) {
					$this->conf['config.']['bookletHeight'] = $this->lConf['bookletHeight'];
				}
				if ($this->lConf['bookletSpeed'] > 0) {
					$this->conf['config.']['bookletSpeed'] = $this->lConf['bookletSpeed'];
				}
				if ($this->lConf['bookletStartingPage'] > 0) {
					$this->conf['config.']['bookletStartingPage'] = $this->lConf['bookletStartingPage'];
				}
				if ($this->lConf['bookletRTL'] < 2) {
					$this->conf['config.']['bookletRTL'] = $this->lConf['bookletRTL'];
				}
				if ($this->lConf['bookletTransition']) {
					$this->conf['config.']['bookletTransition']    = $this->lConf['bookletTransition'];
				}
				if ($this->lConf['bookletTransitiondir']) {
					$this->conf['config.']['bookletTransitiondir'] = $this->lConf['bookletTransitiondir'];
				}
				if ($this->lConf['bookletPagePadding'] != '') {
					$this->conf['config.']['bookletPagePadding'] = $this->lConf['bookletPagePadding'];
				}
				if ($this->lConf['bookletPageNumbers'] < 2) {
					$this->conf['config.']['bookletPageNumbers'] = $this->lConf['bookletPageNumbers'];
				}
				if ($this->lConf['bookletManual'] < 2) {
					$this->conf['config.']['bookletManual'] = $this->lConf['bookletManual'];
				}
				if ($this->lConf['bookletShadows'] < 2) {
					$this->conf['config.']['bookletShadows'] = $this->lConf['bookletShadows'];
				}
				if ($this->lConf['bookletClosed'] < 2) {
					$this->conf['config.']['bookletClosed'] = $this->lConf['bookletClosed'];
				}
				if ($this->lConf['bookletCovers'] < 2) {
					$this->conf['config.']['bookletCovers'] = $this->lConf['bookletCovers'];
				}
				if ($this->lConf['bookletAutoCenter'] < 2) {
					$this->conf['config.']['bookletAutoCenter'] = $this->lConf['bookletAutoCenter'];
				}
				if ($this->lConf['bookletHash'] < 2) {
					$this->conf['config.']['bookletHash'] = $this->lConf['bookletHash'];
				}
				if ($this->lConf['bookletKeyboard'] < 2) {
					$this->conf['config.']['bookletKeyboard'] = $this->lConf['bookletKeyboard'];
				}
				if ($this->lConf['bookletAuto'] < 2) {
					$this->conf['config.']['bookletAuto'] = $this->lConf['bookletAuto'];
				}
				if ($this->lConf['bookletDelay'] < 2) {
					$this->conf['config.']['bookletDelay'] = $this->lConf['bookletDelay'];
				}
				if ($this->lConf['bookletOverlays'] < 2) {
					$this->conf['config.']['bookletOverlays'] = $this->lConf['bookletOverlays'];
				}
				if ($this->lConf['bookletArrows'] < 2) {
					$this->conf['config.']['bookletArrows'] = $this->lConf['bookletArrows'];
				}
				if ($this->lConf['bookletArrowsHide'] < 2) {
					$this->conf['config.']['bookletArrowsHide'] = $this->lConf['bookletArrowsHide'];
				}
				if ($this->lConf['bookletHovers'] < 2) {
					$this->conf['config.']['bookletHovers'] = $this->lConf['bookletHovers'];
				}
				// autoplay
				if ($this->lConf['delayDuration'] > 0) {
					$this->conf['config.']['delayDuration'] = $this->lConf['delayDuration'];
				}
				if ($this->lConf['autoplayCycle'] < 2) {
					$this->conf['config.']['autoplayCycle'] = $this->lConf['autoplayCycle'];
				}

				$this->conf['config.']['style'] = $this->lConf['style'];
			}

			$this->lConf['titles']     = $this->getFlexformData('title', 'titles');
			$this->lConf['attributes'] = $this->getFlexformData('attribute', 'attributes');

			$this->lConf['options']         = $this->getFlexformData('special', 'options');
			$this->lConf['optionsOverride'] = $this->getFlexformData('special', 'optionsOverride');

			if ($this->cObj->data['tx_jfmulticontent_view']) {
				$this->conf['config.']['view'] = $this->cObj->data['tx_jfmulticontent_view'];
			} else {
				$this->conf['config.']['view'] = 'content';
			}

			// define the titles to overwrite
			if (trim($this->lConf['titles'])) {
				$this->titles = t3lib_div::trimExplode(chr(10), $this->lConf['titles']);
			}
			// define the attributes
			if (trim($this->lConf['attributes'])) {
				$this->attributes = t3lib_div::trimExplode(chr(10), $this->lConf['attributes']);
			}
			// options
			if ($this->lConf['optionsOverride'] || trim($this->lConf['options'])) {
				$this->conf['config.'][$this->lConf['style'].'Options'] = $this->lConf['options'];
				$this->conf['config.'][$this->lConf['style'].'OptionsOverride'] = $this->lConf['optionsOverride'];
			}

			$view = $this->conf['views.'][$this->conf['config.']['view'].'.'];

			if ($this->conf['config.']['view'] == 'page') {
				// get the page ID's
				$page_ids = t3lib_div::trimExplode(",", $this->cObj->data['tx_jfmulticontent_pages']);
				// get the informations for every page
				for ($a=0; $a < count($page_ids); $a++) {

					$GLOBALS['TSFE']->register['pid'] = $page_ids[$a];

					if ($this->confArr['useOwnUserFuncForPages']) {
						// TemplaVoila will render the content with a userFunc
						$this->cElements[] = $this->cObj->cObjGetSingle($view['content'], $view['content.']);
						$this->rels[] = $this->cObj->cObjGetSingle($view['rel'], $view['rel.']);
					} else {
						$row = NULL;
						if ($GLOBALS['TSFE']->sys_language_content) {
							$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'pages_language_overlay', 'deleted=0 AND hidden=0 AND pid='.intval($page_ids[$a]).' AND sys_language_uid='.$GLOBALS['TSFE']->sys_language_content, '', '', 1);
							$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
						}
						if (! is_array($row)) {
							$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'pages', 'deleted=0 AND hidden=0 AND uid='.intval($page_ids[$a]), '', '', 1);
							$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
						}
						if (is_array($row)) {
							foreach ($row as $key => $val) {
								$GLOBALS['TSFE']->register['page_'.$key] = $val;
							}
						}

						$this->cElements[] = $this->cObj->cObjGetSingle($view['content'], $view['content.']);
						$this->rels[] = $this->cObj->cObjGetSingle($view['rel'], $view['rel.']);
						$this->content_id[$a] = $page_ids[$a];
					}

					if ($this->titles[$a] == '' || !isset($this->titles[$a])) {
						$this->titles[$a] = $this->cObj->cObjGetSingle($view['title'], $view['title.']);
					}
				}
			} else if ($this->conf['config.']['view'] == 'content') {
				// get the content ID's
				$content_ids = t3lib_div::trimExplode(",", $this->cObj->data['tx_jfmulticontent_contents']);
				// get the informations for every content
				for ($a=0; $a < count($content_ids); $a++) {
					// Select the content
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						'tt_content',
						'uid='.intval($content_ids[$a]),
						'',
						'',
						1
					);
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					if ($GLOBALS['TSFE']->sys_language_content) {
						$row = $GLOBALS['TSFE']->sys_page->getRecordOverlay('tt_content', $row, $GLOBALS['TSFE']->sys_language_content, $GLOBALS['TSFE']->sys_language_contentOL);
					}
					$GLOBALS['TSFE']->register['uid'] = $row['_LOCALIZED_UID'] ? $row['_LOCALIZED_UID'] : $row['uid'];
					$GLOBALS['TSFE']->register['title'] = (strlen(trim($this->titles[$a])) > 0 ? $this->titles[$a] : $row['header']);
					if ($this->titles[$a] == '' || !isset($this->titles[$a])) {
						$this->titles[$a] = $this->cObj->cObjGetSingle($view['title'], $view['title.']);
						$GLOBALS['TSFE']->register['title'] = $this->titles[$a];
					}
					$this->cElements[] = $this->cObj->cObjGetSingle($view['content'], $view['content.']);
					$this->rels[] = $this->cObj->cObjGetSingle($view['rel'], $view['rel.']);
					$this->content_id[$a] = $content_ids[$a];
				}
			} else if ($this->conf['config.']['view'] == 'irre') {
				// get the content ID's
				$elementUID = ($this->cObj->data['_LOCALIZED_UID'])?$this->cObj->data['_LOCALIZED_UID']:$this->cObj->data['uid'];
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					'tt_content',
					'tx_jfmulticontent_irre_parentid='.intval($elementUID).' AND deleted = 0 AND hidden = 0',
					'',
					'sorting ASC'
				);
				$a = 0;
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					if ($GLOBALS['TSFE']->sys_language_content) {
						$row = $GLOBALS['TSFE']->sys_page->getRecordOverlay('tt_content', $row, $GLOBALS['TSFE']->sys_language_content, $GLOBALS['TSFE']->sys_language_contentOL);
					}
					$GLOBALS['TSFE']->register['uid'] = $row['_LOCALIZED_UID'] ? $row['_LOCALIZED_UID'] : $row['uid'];
					$GLOBALS['TSFE']->register['title'] = (strlen(trim($this->titles[$a])) > 0 ? $this->titles[$a] : $row['header']);
					if ($this->titles[$a] == '' || !isset($this->titles[$a])) {
						$this->titles[$a] = $this->cObj->cObjGetSingle($view['title'], $view['title.']);
						$GLOBALS['TSFE']->register['title'] = $this->titles[$a];
					}
					$this->cElements[] = $this->cObj->cObjGetSingle($view['content'], $view['content.']);
					$this->rels[] = $this->cObj->cObjGetSingle($view['rel'], $view['rel.']);
					$this->content_id[$a] = $row['uid'];
					$a ++;
				}
			}
			// HOOK for additional views
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['jfmulticontent']['getViews'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['jfmulticontent']['getViews'] as $_classRef) {
					$_procObj = & t3lib_div::getUserObj($_classRef);
					if ($this->conf['config.']['view'] == $_procObj->getIdentifier()) {
						if (! method_exists($_procObj, 'isActive') || (method_exists($_procObj, 'isActive') && $_procObj->isActive())) {
							// If the methode "isActive" not exists, this will be true...
							$_procObj->main($this->content, $this->conf, $this);
							$this->titles = $_procObj->getTitles();
							$this->cElements = $_procObj->getElements();
							$this->content_id = $_procObj->getIds();
							if (method_exists($_procObj, 'getRels')) {
								$this->rels = $_procObj->getRels();
							}
						}
					}
				}
			}
			// define the key of the element
			$this->setContentKey('jfmulticontent_c' . $this->cObj->data['uid']);
		} else {
			// TS config will be used
			// define the key of the element
			if ($this->conf['config.']['contentKey']) {
				$this->setContentKey($this->conf['config.']['contentKey']);
			} else {
				$this->setContentKey('jfmulticontent_ts1');
			}
			// Render the contents
			if (count($this->conf['contents.']) > 0) {
				foreach ($this->conf['contents.'] as $key => $contents) {
					$title = trim($this->cObj->cObjGetSingle($contents['title'], $contents['title.']));
					$content = trim($this->cObj->cObjGetSingle($contents['content'], $contents['content.']));
					if ($content) {
						$this->titles[] = $title;
						$this->cElements[] = $content;
						$this->rels[] = $this->cObj->cObjGetSingle($contents['rel'], $contents['rel.']);
						$this->content_id[] = $this->cObj->stdWrap($contents['id'], $contents['id.']);
					}
				}
			}
		}
		$this->contentCount = count($this->cElements);
		// return FALSE, if there is no element
		if ($this->contentCount == 0) {
			return FALSE;
		}

		// The template
		if (! $this->templateFile = $this->cObj->fileResource($this->conf['templateFile'])) {
			$this->templateFile = $this->cObj->fileResource("EXT:jfmulticontent/res/tx_jfmulticontent_pi1.tmpl");
		}
		// The template for JS
		if (! $this->templateFileJS = $this->cObj->fileResource($this->conf['templateFileJS'])) {
			$this->templateFileJS = $this->cObj->fileResource("EXT:jfmulticontent/res/tx_jfmulticontent_pi1.js");
		}

		// define the jQuery mode and function
		if ($this->conf['jQueryNoConflict']) {
			$jQueryNoConflict = "jQuery.noConflict();";
		} else {
			$jQueryNoConflict = "";
		}

		// style
		switch ($this->conf['config.']['style']) {
			case "2column" : {
				$this->templatePart = "TEMPLATE_COLUMNS";
				$this->contentCount = 2;
				$this->classes = array(
					$this->conf['config.']["column1"],
					$this->conf['config.']["column2"],
				);
				$this->contentClass = t3lib_div::trimExplode("|*|", $this->conf['2columnClasses']);
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['columnWrap.']['wrap']);
				break;
			}
			case "3column" : {
				$this->templatePart = "TEMPLATE_COLUMNS";
				$this->contentCount = 3;
				$this->classes = array(
					$this->conf['config.']["column1"],
					$this->conf['config.']["column2"],
					$this->conf['config.']["column3"],
				);
				$this->contentClass = t3lib_div::trimExplode("|*|", $this->conf['3columnClasses']);
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['columnWrap.']['wrap']);
				break;
			}
			case "4column" : {
				$this->templatePart = "TEMPLATE_COLUMNS";
				$this->contentCount = 4;
				$this->classes = array(
					$this->conf['config.']["column1"],
					$this->conf['config.']["column2"],
					$this->conf['config.']["column3"],
					$this->conf['config.']["column4"],
				);
				$this->contentClass = t3lib_div::trimExplode("|*|", $this->conf['4columnClasses']);
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['columnWrap.']['wrap']);
				break;
			}
			case "5column" : {
				$this->templatePart = "TEMPLATE_COLUMNS";
				$this->contentCount = 5;
				$this->classes = array(
					$this->conf['config.']["column1"],
					$this->conf['config.']["column2"],
					$this->conf['config.']["column3"],
					$this->conf['config.']["column4"],
					$this->conf['config.']["column5"],
				);
				$this->contentClass = t3lib_div::trimExplode("|*|", $this->conf['5columnClasses']);
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['columnWrap.']['wrap']);
				break;
			}
			case "tab" : {
				// jQuery Tabs
				$this->templatePart = "TEMPLATE_TAB";
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['tabWrap.']['wrap']);
				// the id attribute is not permitted in tabs-style
				if (count($this->attributes) > 0) {
					foreach ($this->attributes as $key => $attribute) {
						if (preg_match("/id=[\"|\'](.*?)[\"|\']/i", $attribute, $preg)) {
							$this->attributes[$key] = trim(str_replace($preg[0], "", $attribute));
						}
					}
				}
				$this->pagerenderer->addJS($jQueryNoConflict);
				$options = array();
				if ($this->conf['config.']['tabCollapsible']) {
					$options['collapsible'] = "collapsible:true";
					if (! $this->conf['config.']['tabOpen']) {
						$options['active'] = "active:false";
					}
				}
				if ($this->conf['config.']['tabRandomContent']) {
					$options['active'] = "active:Math.floor(Math.random()*{$this->contentCount})";
				} elseif (is_numeric($this->conf['config.']['tabOpen'])) {
					$options['active'] = "active:".($this->conf['config.']['tabOpen'] - 1);
				}
				if (in_array($this->conf['config.']['tabEvent'], array('click', 'mouseover'))) {
					$options['event'] = "event:'{$this->conf['config.']['tabEvent']}'";
				}
				if (in_array($this->conf['config.']['tabHeightStyle'], array('auto', 'fill', 'content'))) {
					$options['heightStyle'] = "heightStyle:'{$this->conf['config.']['tabHeightStyle']}'";
				}

				// Add Cookies script, if cookie is active
				if ($this->conf['config.']['tabCookieExpires'] > 0 && $this->conf['config.']['tabOpen'] != -1) {
					if (T3JQUERY !== TRUE) {
						$this->pagerenderer->addJsFile($this->conf['jQueryCookies']);
					}
					unset($options['active']);
					$cookie_path = t3lib_div::getIndpEnv('REQUEST_URI');
					if ($this->lConf['tabCookieRoot'] || preg_match("/^\/index.php/i", $cookie_path)) {
						$cookie_path = "/";
					}
					$options['activate'] = "activate:function(e,ui) { jQuery.cookie('{$this->getContentKey()}', ui.newTab.index(), { expires: ".$this->conf['config.']['tabCookieExpires'].", path:'$cookie_path' }); }";
					$options['active'] = "active:jQuery.cookie('{$this->getContentKey()}')";
				}

				if ($this->conf['config.']['tabHideEffect'] == 'none') {
					$options['hide'] = "hide:false";
				} elseif ($this->conf['config.']['tabHideEffect']) {
					$fx = array();
					$fx[] = "effect:'{$this->conf['config.']['tabHideEffect']}'";
					if (is_numeric($this->conf['config.']['tabHideTransitionduration'])) {
						$fx[] = "duration:'{$this->conf['config.']['tabHideTransitionduration']}'";
					}
					if ($this->conf['config.']['tabHideTransition']) {
						$fx[] = "easing:'".(in_array($this->conf['config.']['tabHideTransition'], array("swing", "linear")) ? "" : "ease{$this->conf['config.']['tabHideTransitiondir']}")."{$this->conf['config.']['tabHideTransition']}'";
					}
					$options['hide'] = "hide:{".implode(',', $fx)."}";
				}

				if ($this->conf['config.']['tabShowEffect'] == 'none') {
					$options['show'] = "show:false";
				} elseif ($this->conf['config.']['tabShowEffect']) {
					$fx = array();
					$fx[] = "effect:'{$this->conf['config.']['tabShowEffect']}'";
					if (is_numeric($this->conf['config.']['tabShowTransitionduration'])) {
						$fx[] = "duration:'{$this->conf['config.']['tabShowTransitionduration']}'";
					}
					if ($this->conf['config.']['tabShowTransition']) {
						$fx[] = "easing:'".(in_array($this->conf['config.']['tabShowTransition'], array("swing", "linear")) ? "" : "ease{$this->conf['config.']['tabShowTransitiondir']}")."{$this->conf['config.']['tabShowTransition']}'";
					}
					$options['show'] = "show:{".implode(',', $fx)."}";
				}

				// overwrite all options if set
				if ($this->conf['config.']['tabOptionsOverride']) {
					$options = array($this->conf['config.']['tabOptions']);
				} else {
					if ($this->conf['config.']['tabOptions']) {
						$options['options'] = $this->conf['config.']['tabOptions'];
					}
				}

				// get the Template of the Javascript
				$markerArray = array();
				// get the template
				if (! $templateCode = trim($this->cObj->getSubpart($this->templateFileJS, "###TEMPLATE_TAB_JS###"))) {
					$templateCode = $this->outputError("Template TEMPLATE_TAB_JS is missing", TRUE);
				}

				// open tab by hash
				if ($this->confArr['tabSelectByHash']) {
					$tabSelector = trim($this->cObj->getSubpart($templateCode, "###TAB_SELECT_BY_HASH###"));
				} else {
					$tabSelector = NULL;
				}
				$templateCode = trim($this->cObj->substituteSubpart($templateCode, '###TAB_SELECT_BY_HASH###', $tabSelector, 0));

				// app the open-link-template
				if ($this->confArr['openExternalLink']) {
					$openExtLink = trim($this->cObj->getSubpart($templateCode, "###OPEN_EXTERNAL_LINK###"));
				} else {
					$openExtLink = NULL;
				}
				$templateCode = trim($this->cObj->substituteSubpart($templateCode, '###OPEN_EXTERNAL_LINK###', $openExtLink, 0));

				// Replace default values
				$markerArray["KEY"] = $this->getContentKey();
				$markerArray["PREG_QUOTE_KEY"] = preg_quote($this->getContentKey(), "/");
				$markerArray["OPTIONS"] = implode(", ", $options);
				$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);

				// Add all CSS and JS files
				if (T3JQUERY === TRUE) {
					tx_t3jquery::addJqJS();
				} else {
					$this->pagerenderer->addJsFile($this->conf['jQueryLibrary'], TRUE);
					$this->pagerenderer->addJsFile($this->conf['jQueryEasing']);
					$this->pagerenderer->addJsFile($this->conf['jQueryUI']);
				}
				$this->pagerenderer->addCssFile($this->conf['jQueryUIstyle']);
				$this->pagerenderer->addJS($templateCode);
				break;
			}
			case "accordion" : {
				// jQuery Accordion
				$this->templatePart = "TEMPLATE_ACCORDION";
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['accordionWrap.']['wrap']);
				$this->pagerenderer->addJS($jQueryNoConflict);
				$options = array();
				if ($this->conf['config.']['accordionCollapsible']) {
					$options['collapsible'] = "collapsible:true";
				}
				if ($this->conf['config.']['accordionClosed']) {
					$options['active'] = "active:false";
					$options['collapsible'] = "collapsible:true";
				} elseif ($this->conf['config.']['accordionRandomContent']) {
					$options['active'] = "active:Math.floor(Math.random()*{$this->contentCount})";
				} elseif ($this->conf['config.']['accordionOpen'] > 0) {
					$options['active'] = "active:".($this->conf['config.']['accordionOpen'] - 1);
				}
				if (in_array($this->conf['config.']['accordionEvent'], array('click', 'mouseover'))) {
					$options['event'] = "event:'{$this->conf['config.']['accordionEvent']}'";
				}
				if (in_array($this->conf['config.']['accordionHeightStyle'], array('auto', 'fill', 'content'))) {
					$options['heightStyle'] = "heightStyle:'{$this->conf['config.']['accordionHeightStyle']}'";
				}
				// get the Template of the Javascript
				$markerArray = array();
				$markerArray["KEY"]            = $this->getContentKey();
				$markerArray["CONTENT_COUNT"]  = $this->contentCount;
				$markerArray["EASING"]         = (in_array($this->conf['config.']['accordionTransition'], array("swing", "linear")) ? "" : "ease".$this->conf['config.']['accordionTransitiondir'].$this->conf['config.']['accordionTransition']);
				$markerArray["TRANS_DURATION"] = (is_numeric($this->conf['config.']['accordionTransitionduration']) ? $this->conf['config.']['accordionTransitionduration'] : 1000);

				// get the template for the Javascript
				if (! $templateCode = trim($this->cObj->getSubpart($this->templateFileJS, "###TEMPLATE_ACCORDION_JS###"))) {
					$templateCode = $this->outputError("Template TEMPLATE_ACCORDION_JS is missing", TRUE);
				}
				$easingAnimation = NULL;
				if (! $this->conf['config.']['accordionAnimate']) {
					$options['animate'] = "animate:false";
				} else {
					$fx = array();
					if (is_numeric($this->conf['config.']['accordionTransitionduration'])) {
						$fx[] = "duration:'{$this->conf['config.']['accordionTransitionduration']}'";
					}
					if ($this->conf['config.']['accordionTransition']) {
						$fx[] = "easing:'".(in_array($this->conf['config.']['accordionTransition'], array("swing", "linear")) ? "" : "ease{$this->conf['config.']['accordionTransitiondir']}")."{$this->conf['config.']['accordionTransition']}'";
					}
					$options['animate'] = "animate:{".implode(',', $fx)."}";
				}

				// app the open-link-template
				if ($this->confArr['openExternalLink']) {
					$openExtLink = trim($this->cObj->getSubpart($templateCode, "###OPEN_EXTERNAL_LINK###"));
				} else {
					$openExtLink = NULL;
				}
				$templateCode = trim($this->cObj->substituteSubpart($templateCode, '###OPEN_EXTERNAL_LINK###', $openExtLink, 0));

				// open tab by hash
				if ($this->confArr['tabSelectByHash']) {
					$tabSelector = trim($this->cObj->getSubpart($templateCode, "###TAB_SELECT_BY_HASH###"));
				} else {
					$tabSelector = NULL;
				}
				$templateCode = trim($this->cObj->substituteSubpart($templateCode, '###TAB_SELECT_BY_HASH###', $tabSelector, 0));

				// overwrite all options if set
				if ($this->conf['config.']['accordionOptionsOverride']) {
					$options = array($this->conf['config.']['accordionOptions']);
				} else {
					if ($this->conf['config.']['accordionOptions']) {
						$options['options'] = $this->conf['config.']['accordionOptions'];
					}
				}

				// Replace default values
				$markerArray["OPTIONS"] = implode(", ", $options);
				// Replace all markers
				$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);

				// Add all CSS and JS files
				if (T3JQUERY === TRUE) {
					tx_t3jquery::addJqJS();
				} else {
					$this->pagerenderer->addJsFile($this->conf['jQueryLibrary'], TRUE);
					$this->pagerenderer->addJsFile($this->conf['jQueryEasing']);
					$this->pagerenderer->addJsFile($this->conf['jQueryUI']);
				}
				$this->pagerenderer->addCssFile($this->conf['jQueryUIstyle']);
				$this->pagerenderer->addJS(trim($templateCode));
				break;
			}
			case "slider" : {
				// anythingslider
				$this->templatePart = "TEMPLATE_SLIDER";
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['sliderWrap.']['wrap']);
				$this->pagerenderer->addJS($jQueryNoConflict);
				//
				if ($this->conf['config.']['sliderTransition']) {
					$options[] = "easing: '".(in_array($this->conf['config.']['sliderTransition'], array("swing", "linear")) ? "" : "ease{$this->conf['config.']['sliderTransitiondir']}")."{$this->conf['config.']['sliderTransition']}'";
				}
				if ($this->conf['config.']['sliderTransitionduration'] > 0) {
					$options[] = "animationTime: {$this->conf['config.']['sliderTransitionduration']}";
				}
				if ($this->conf['config.']['sliderAutoplay']) {
					$options[] = "autoPlay: true";
				} else {
					$options[] = "autoPlay: false";
				}
				if ($this->conf['config.']['delayDuration'] > 0) {
					$options[] = "delay: {$this->conf['config.']['delayDuration']}";
					$options[] = "startStopped: ".($this->conf['config.']['sliderAutoStart'] ? 'false' : 'true');
					$options[] = "stopAtEnd: ".($this->conf['config.']['sliderStopAtEnd'] ? 'true' : 'false');
				} else {
					// Toggle only if not autoplay
					$options[] = "toggleArrows: ".($this->conf['config.']['sliderToggleArrows'] ? 'true' : 'false');
					$options[] = "toggleControls: ".($this->conf['config.']['sliderToggleControls'] ? 'true' : 'false');
				}
				$sliderWidth = trim($this->conf['config.']['sliderWidth']);
				$sliderHeight = trim($this->conf['config.']['sliderHeight']);
				if ($sliderWidth || $sliderHeight) {
					if (is_numeric($sliderWidth)) {
						$sliderWidth .= 'px';
					}
					if (is_numeric($sliderHeight)) {
						$sliderHeight .= 'px';
					}
					$this->pagerenderer->addCSS("#{$this->getContentKey()} {\n" .
($sliderWidth ?  "	width: {$sliderWidth};\n"   : "") .
($sliderHeight ? "	height: {$sliderHeight};\n" : "") .
"}");
				}
				if ($this->conf['config.']['sliderResizeContents']) {
					$options[] = "resizeContents: true";
				}
				$this->pagerenderer->addCssFile($this->conf['sliderCSS']);
				$this->pagerenderer->addCssFileInc($this->conf['sliderCSSie7'], 'lte IE 7');

				if ($this->conf['config.']['sliderTheme']) {
					$options[] = "theme: '".t3lib_div::slashJS($this->conf['config.']['sliderTheme'])."'";
					if (substr($this->confArr['anythingSliderThemeFolder'], 0, 4) === 'EXT:') {
						list($extKey, $local) = explode('/', substr($this->confArr['anythingSliderThemeFolder'], 4), 2);
						$anythingSliderThemeFolder = t3lib_extMgm::siteRelPath($extKey) . $local;
					} else {
						$anythingSliderThemeFolder = $this->confArr['anythingSliderThemeFolder'];
					}
					$this->pagerenderer->addCssFile(t3lib_div::slashJS($anythingSliderThemeFolder).$this->conf['config.']['sliderTheme'].'/style.css');
				}
				if ($this->conf['config.']['sliderMode']) {
					$options[] = "mode: '".$this->conf['config.']['sliderMode']."'";
				}
				$options[] = "buildArrows: ".($this->conf['config.']['sliderBuildArrows'] ? 'true' : 'false');
				$options[] = "allowRapidChange: ".($this->conf['config.']['sliderAllowRapidChange'] ? 'true' : 'false');
				$options[] = "resumeOnVideoEnd: ".($this->conf['config.']['sliderResumeOnVideoEnd'] ? 'true' : 'false');
				$options[] = "playRtl: ".($this->conf['config.']['sliderPlayRtl'] ? 'true' : 'false');
				$options[] = "hashTags: ".($this->conf['config.']['sliderHashTags'] ? 'true' : 'false');
				$options[] = "pauseOnHover: ".($this->conf['config.']['sliderPauseOnHover'] ? 'true' : 'false');
				$options[] = "buildNavigation: ".($this->conf['config.']['sliderNavigation'] ? 'true' : 'false');
				$options[] = "buildStartStop: ".($this->conf['config.']['sliderStartStop'] ? 'true' : 'false');

				$options[] = "startText: '".t3lib_div::slashJS($this->pi_getLL('slider_start'))."'";
				$options[] = "stopText: '".t3lib_div::slashJS($this->pi_getLL('slider_stop'))."'";
				if ($this->pi_getLL('slider_forward')) {
					$options[] = "forwardText: '".t3lib_div::slashJS($this->pi_getLL('slider_forward'))."'";
				}
				if ($this->pi_getLL('slider_back')) {
					$options[] = "backText: '".t3lib_div::slashJS($this->pi_getLL('slider_back'))."'";
				}

				// define the paneltext
				if ($this->conf['config.']['sliderPanelFromHeader']) {
					$tab = array();
					for ($a=0; $a < $this->contentCount; $a++) {
						$tab[] = "if(i==".($a+1).") return ".t3lib_div::quoteJSvalue($this->titles[$a]).";";
					}
					$options[] = "navigationFormatter: function(i,p){\n			".implode("\n			", $tab)."\n		}";
				} elseif (trim($this->pi_getLL('slider_panel'))) {
					$options[] = "navigationFormatter: function(i,p){ var str = '".(t3lib_div::slashJS($this->pi_getLL('slider_panel')))."'; return str.replace('%i%',i); }";
				}
				if ($this->conf['config.']['sliderRandomContent']) {
					$options[] = "startPanel: Math.floor(Math.random()*".($this->contentCount + 1).")";
				} elseif ($this->conf['config.']['sliderOpen'] > 1) {
					$options[] = "startPanel: ".($this->conf['config.']['sliderOpen'] < $this->contentCount ? $this->conf['config.']['sliderOpen'] : $this->contentCount);
				}

				// overwrite all options if set
				if ($this->conf['config.']['sliderOptionsOverride']) {
					$options = array($this->conf['config.']['sliderOptions']);
				} else {
					if ($this->conf['config.']['sliderOptions']) {
						$options[] = $this->conf['config.']['sliderOptions'];
					}
				}

				// get the Template of the Javascript
				$markerArray = array();
				// get the template
				if (! $templateCode = trim($this->cObj->getSubpart($this->templateFileJS, "###TEMPLATE_SLIDER_JS###"))) {
					$templateCode = $this->outputError("Template TEMPLATE_SLIDER_JS is missing", TRUE);
				}

				// Replace default values
				$markerArray["KEY"] = $this->getContentKey();
				$markerArray["OPTIONS"] = implode(", ", $options);
				$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);

				// Add all CSS and JS files
				if (T3JQUERY === TRUE) {
					tx_t3jquery::addJqJS();
				} else {
					$this->pagerenderer->addJsFile($this->conf['jQueryLibrary'], TRUE);
					$this->pagerenderer->addJsFile($this->conf['jQueryEasing']);
				}
				$this->pagerenderer->addJsFile($this->conf['sliderJS']);
				if ($this->conf['config.']['sliderResumeOnVideoEnd']) {
					$this->pagerenderer->addJsFile($this->conf['sliderJSvideo']);
				}
				$this->pagerenderer->addJS($templateCode);
				break;
			}
			case "slidedeck" : {
				// SlideDeck
				$this->templatePart = "TEMPLATE_SLIDEDECK";
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['slidedeckWrap.']['wrap']);
				$this->pagerenderer->addJS($jQueryNoConflict);
				$options = array();
				if ($this->conf['config.']['slidedeckTransitionduration']) {
					$options['speed'] = "speed: {$this->conf['config.']['slidedeckTransitionduration']}";
				}
				if ($this->conf['config.']['slidedeckTransition']) {
					$options['transition'] = "transition: '".(in_array($this->conf['config.']['slidedeckTransition'], array("swing", "linear")) ? "" : "ease{$this->conf['config.']['slidedeckTransitiondir']}")."{$this->conf['config.']['slidedeckTransition']}'";
				}
				if ($this->conf['config.']['slidedeckStart']) {
					$options['start'] = "start: {$this->conf['config.']['slidedeckStart']}";
				}
				$options['activeCorner'] = "activeCorner: ".($this->conf['config.']['slidedeckActivecorner'] ? 'true' : 'false');
				$options['index']        = "index: ".($this->conf['config.']['slidedeckIndex'] ? 'true' : 'false');
				$options['scroll']       = "scroll: ".($this->conf['config.']['slidedeckScroll'] ? 'true' : 'false');
				$options['keys']         = "keys: ".($this->conf['config.']['slidedeckKeys'] ? 'true' : 'false');
				$options['hideSpines']   = "hideSpines: ".($this->conf['config.']['slidedeckHidespines'] ? 'true' : 'false');
				if ($this->conf['config.']['delayDuration'] > 0) {
					$options['autoPlay']         = "autoPlay: true";
					$options['autoPlayInterval'] = "autoPlayInterval: {$this->conf['config.']['delayDuration']}";
					$options['cycle']            = "cycle: ".($this->conf['config.']['autoplayCycle'] ? 'true' : 'false');
				}

				// overwrite all options if set
				if ($this->conf['config.']['slidedeckOptionsOverride']) {
					$options = array($this->conf['config.']['slidedeckOptions']);
				} else {
					if ($this->conf['config.']['slidedeckOptions']) {
						$options['options'] = $this->conf['config.']['slidedeckOptions'];
					}
				}

				// get the template for the Javascript
				if (! $templateCode = trim($this->cObj->getSubpart($this->templateFileJS, "###TEMPLATE_SLIDEDECK_JS###"))) {
					$templateCode = $this->outputError("Template TEMPLATE_SLIDEDECK_JS is missing", TRUE);
				}
				// Replace default values
				$markerArray = array();
				$markerArray["KEY"]     = $this->getContentKey();
				$markerArray["HEIGHT"]  = ($this->conf['config.']['slidedeckHeight'] > 0 ? $this->conf['config.']['slidedeckHeight'] : 300);
				$markerArray["OPTIONS"] = implode(", ", $options);
				// Replace all markers
				$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);

				// Add all CSS and JS files
				if (T3JQUERY === TRUE) {
					tx_t3jquery::addJqJS();
				} else {
					$this->pagerenderer->addJsFile($this->conf['jQueryLibrary'], TRUE);
					$this->pagerenderer->addJsFile($this->conf['jQueryEasing']);
				}
				$this->pagerenderer->addJsFile($this->conf['slidedeckJS']);
				$this->pagerenderer->addCssFile($this->conf['slidedeckCSS']);
				if ($this->conf['config.']['slidedeckScroll']) {
					$this->pagerenderer->addJsFile($this->conf['jQueryMouseWheel']);
				}
				$this->pagerenderer->addJS(trim($templateCode));
				break;
			}
			case "easyaccordion" : {
				// easyaccordion
				$this->templatePart = "TEMPLATE_EASYACCORDION";
				$this->additionalMarker["SKIN"] = $this->conf['config.']['easyaccordionSkin'];
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['easyaccordionWrap.']['wrap']);
				$this->pagerenderer->addJS($jQueryNoConflict);
				$options = array();
				if ($this->conf['config.']['delayDuration'] > 0) {
					$options['autoStart']     = "autoStart: true";
					$options['slideInterval'] = "slideInterval: {$this->conf['config.']['delayDuration']}";
				}
				$options['slideNum'] = "slideNum: ".($this->conf['config.']['easyaccordionSlideNum'] ? 'true' : 'false');

				// overwrite all options if set
				if ($this->conf['config.']['optionsOverride']) {
					$options = array($this->conf['config.']['slideOptions']);
				} else {
					if ($this->conf['config.']['slideOptions']) {
						$options['options'] = $this->conf['config.']['slideOptions'];
					}
				}

				// get the template for the Javascript
				if (! $templateCode = trim($this->cObj->getSubpart($this->templateFileJS, "###TEMPLATE_EASYACCORDION_JS###"))) {
					$templateCode = $this->outputError("Template TEMPLATE_EASYACCORDION_JS is missing", TRUE);
				}
				// Replace default values
				$markerArray = array();
				$markerArray["KEY"]     = $this->getContentKey();
				$markerArray["WIDTH"]   = ($this->conf['config.']['easyaccordionWidth'] > 0  ? $this->conf['config.']['easyaccordionWidth']  : 600);
				$markerArray["OPTIONS"] = implode(", ", $options);
				// Replace all markers
				$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);

				// Add all CSS and JS files
				if (T3JQUERY === TRUE) {
					tx_t3jquery::addJqJS();
				} else {
					$this->pagerenderer->addJsFile($this->conf['jQueryLibrary'], TRUE);
				}
				$this->pagerenderer->addJsFile($this->conf['easyaccordionJS']);
				$this->pagerenderer->addCssFile($this->conf['easyaccordionCSS']);
				$this->pagerenderer->addCssFile($this->confArr['easyAccordionSkinFolder'] . $this->conf['config.']['easyaccordionSkin'] . "/style.css");
				$this->pagerenderer->addJS(trim($templateCode));
				break;
			}
			case "booklet" : {
				// easyaccordion
				$this->templatePart = "TEMPLATE_BOOKLET";
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['bookletWrap.']['wrap']);
				$this->pagerenderer->addJS($jQueryNoConflict);
				$options = array();
				if (is_numeric($this->conf['config.']['bookletWidth'])) {
					$options['width'] = "width: ".$this->conf['config.']['bookletWidth'];
				}
				if (is_numeric($this->conf['config.']['bookletHeight'])) {
					$options['height'] = "height: ".$this->conf['config.']['bookletHeight'];
				}
				if (is_numeric($this->conf['config.']['bookletSpeed'])) {
					$options['speed'] = "speed: ".$this->conf['config.']['bookletSpeed'];
				}
				if (is_numeric($this->conf['config.']['bookletStartingPage'])) {
					$options['startingPage'] = "startingPage: ".$this->conf['config.']['bookletStartingPage'];
				}
				if ($this->conf['config.']['bookletRTL']) {
					$options['direction'] = "direction: 'RTL'";
				}
				if ($this->conf['config.']['bookletTransition']) {
					$options['transition'] = "easing: '".(in_array($this->conf['config.']['bookletTransition'], array("swing", "linear")) ? "" : "ease{$this->conf['config.']['bookletTransitiondir']}")."{$this->conf['config.']['bookletTransition']}'";
				}
				if (is_numeric($this->conf['config.']['bookletPagePadding'])) {
					$options['pagePadding'] = "pagePadding: ".$this->conf['config.']['bookletPagePadding'];
				}
				$options['pageNumbers'] = "pageNumbers: ".($this->conf['config.']['bookletPageNumbers'] ? 'true' : 'false');
				$options['manual']      = "manual: ".($this->conf['config.']['bookletManual'] ? 'true' : 'false');
				$options['shadows']     = "shadows: ".($this->conf['config.']['bookletShadows'] ? 'true' : 'false');
				$options['closed']      = "closed: ".($this->conf['config.']['bookletClosed'] ? 'true' : 'false');
				$options['covers']      = "covers: ".($this->conf['config.']['bookletCovers'] ? 'true' : 'false');
				$options['autoCenter']  = "autoCenter: ".($this->conf['config.']['bookletAutoCenter'] ? 'true' : 'false');
				$options['hash']        = "hash: ".($this->conf['config.']['bookletHash'] ? 'true' : 'false');
				$options['keyboard']    = "keyboard: ".($this->conf['config.']['bookletKeyboard'] ? 'true' : 'false');
				$options['overlays']    = "overlays: ".($this->conf['config.']['bookletOverlays'] ? 'true' : 'false');
				$options['arrows']      = "arrows: ".($this->conf['config.']['bookletArrows'] ? 'true' : 'false');
				$options['arrowsHide']  = "arrowsHide: ".($this->conf['config.']['bookletArrowsHide'] ? 'true' : 'false');
				$options['hovers']      = "hovers: ".($this->conf['config.']['bookletHovers'] ? 'true' : 'false');

				// overwrite all options if set
				if ($this->conf['config.']['bookletOptionsOverride']) {
					$options = array($this->conf['config.']['bookletOptions']);
				} else {
					if ($this->conf['config.']['bookletOptions']) {
						$options['options'] = $this->conf['config.']['bookletOptions'];
					}
				}

				// get the template for the Javascript
				if (! $templateCode = trim($this->cObj->getSubpart($this->templateFileJS, "###TEMPLATE_BOOKLET_JS###"))) {
					$templateCode = $this->outputError("Template TEMPLATE_BOOKLET_JS is missing", TRUE);
				}

				// Replace default values
				$markerArray = array();
				$markerArray["KEY"]     = $this->getContentKey();
				$markerArray["OPTIONS"] = implode(",\n		", $options);

				// Replace all markers
				$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);

				// Add all CSS and JS files
				if (T3JQUERY === TRUE) {
					tx_t3jquery::addJqJS();
				} else {
					$this->pagerenderer->addJsFile($this->conf['jQueryLibrary'], TRUE);
					$this->pagerenderer->addJsFile($this->conf['jQueryEasing']);
				}
				$this->pagerenderer->addJsFile($this->conf['bookletJS']);
				$this->pagerenderer->addCssFile($this->conf['bookletCSS']);
				$this->pagerenderer->addJS(trim($templateCode));
				break;
			}
			default: {
				return $this->outputError("NO VALID TEMPLATE SELECTED", FALSE);
			}
		}

		// add the CSS file
		$this->pagerenderer->addCssFile($this->conf['cssFile']);

		// Add the ressources
		if (! $this->conf['disableJs']) {
			$this->pagerenderer->addResources();
		}

		// Render the Template
		$content = $this->renderTemplate();

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Set the contentKey
	 * @param string $contentKey
	 */
	public function setContentKey($contentKey=NULL)
	{
		$this->contentKey = ($contentKey == NULL ? $this->extKey : $contentKey);
	}

	/**
	 * Get the contentKey
	 * @return string
	 */
	public function getContentKey()
	{
		return $this->contentKey;
	}

	/**
	 * Render the template with the defined contents
	 *
	 * @return string
	 */
	public function renderTemplate()
	{
		// set the register:key for TS manipulation
		$GLOBALS['TSFE']->register['key'] = $this->getContentKey();

		$markerArray = $this->additionalMarker;

		// Define string with all classes
		$markerArray["COLUMN_CLASSES"] = implode('', $this->classes);
		$GLOBALS['TSFE']->register['COLUMN_CLASSES'] = $markerArray["COLUMN_CLASSES"];

		// get the template
		if (! $templateCode = $this->cObj->getSubpart($this->templateFile, "###{$this->templatePart}###")) {
			$templateCode = $this->outputError("Template {$this->templatePart} is missing", FALSE);
		}
		// Replace default values
		$markerArray["KEY"] = $this->getContentKey();
		// replace equalizeClass
		if ($this->conf['config.']['equalize']) {
			$markerArray["EQUALIZE_CLASS"] = ' '.$this->cObj->stdWrap($this->conf['equalizeClass'], $this->conf['equalizeClass.']);
		} else {
			$markerArray["EQUALIZE_CLASS"] = '';
		}
		$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);
		// Get the title template
		$titleCode = $this->cObj->getSubpart($templateCode, "###TITLES###");
		// Get the column template
		$columnCode = $this->cObj->getSubpart($templateCode, "###COLUMNS###");
		// Define the contentWrap
		switch (count($this->contentWrap)) {
			case 1 : {
				$contentWrap_array = array(
					$this->contentWrap[0],
					$this->contentWrap[0],
					$this->contentWrap[0],
				);
				break;
			}
			case 2 : {
				$contentWrap_array = array(
					$this->contentWrap[0],
					$this->contentWrap[0],
					$this->contentWrap[1],
				);
				break;
			}
			case 3 : {
				$contentWrap_array = $this->contentWrap;
				break;
			}
			default: {
				$contentWrap_array = array(
					NULL,
					NULL,
					NULL
				);
				break;
			}
		}
		if ($this->conf['config.']['easyaccordionOpen'] > $this->contentCount) {
			$this->conf['config.']['easyaccordionOpen'] = $this->contentCount;
		}
		// fetch all contents
		for ($a=0; $a < $this->contentCount; $a++) {
			$markerArray = array();
			// get the attribute if exist
			$markerArray["ATTRIBUTE"] = "";
			if ($this->attributes[$a] != '') {
				$markerArray["ATTRIBUTE"] .= ' ' . $this->attributes[$a];
			}
			// if the attribute does not have a class entry, the class will be wraped for yaml (c33l, c33l, c33r)
			if ($this->classes[$a] && isset($this->contentClass[$a]) && ! preg_match("/class\=/i", $markerArray["ATTRIBUTE"])) {
				// wrap the class
				$markerArray["ATTRIBUTE"] .= $this->cObj->stdWrap($this->classes[$a], array("wrap" => ' class="'.$this->contentClass[$a].'"', "required" => 1));
			}
			// Set the active class for the active slide
			if (($a+1) ==  $this->conf['config.']['easyaccordionOpen']) {
				$markerArray["EASYACCORDION_ACTIVE"] = 'class="active"';
			} else {
				$markerArray["EASYACCORDION_ACTIVE"] = '';
			}

			// render the content
			$markerArray["CONTENT_ID"] = $this->content_id[$a];
			$markerArray["ID"]         = $a+1;
			$markerArray["TITLE"]      = NULL;

			// Title will be selected if not COLUMNS (TAB, ACCORDION and SLIDER)
			if ($this->templatePart != "TEMPLATE_COLUMNS") {
				// overwrite the title if set in $this->titles
				$markerArray["TITLE"] = $this->titles[$a];
			}

			$GLOBALS['TSFE']->register['content_id'] = $markerArray["CONTENT_ID"];
			$GLOBALS['TSFE']->register['id']         = $markerArray["ID"];
			$GLOBALS['TSFE']->register['title']      = $markerArray["TITLE"];

			$prefixLocalAnchors = $GLOBALS['TSFE']->config['config']['prefixLocalAnchors'];
			$GLOBALS['TSFE']->config['config']['prefixLocalAnchors'] = NULL;
			$markerArray["TAB_KEY"] = $this->cObj->cObjGetSingle($this->conf['tabKey'], $this->conf['tabKey.']);
			$GLOBALS['TSFE']->config['config']['prefixLocalAnchors'] = $prefixLocalAnchors;

			// define the used wrap
			if ($a == 0) {
				$wrap = $contentWrap_array[0];
			} elseif (($a+1) == $this->contentCount) {
				$wrap = $contentWrap_array[2];
			} else {
				$wrap = $contentWrap_array[1];
			}
			$addContent = FALSE;
			// override the CONTENT
			if ($this->templatePart == "TEMPLATE_COLUMNS" && $this->conf['config.']['columnOrder']) {
				switch ($this->conf['config.']['columnOrder']) {
					case 1 : {
						// left to right, top to down
						foreach ($this->cElements as $key => $cElements) {
							$test = ($key - $a) / $this->contentCount;
							if (intval($test) == $test) {
								$markerArray["CONTENT"] .= $this->cObj->stdWrap($this->cElements[$key], array('wrap' => $wrap));
								$addContent = TRUE;
							}
						}
						break;
					}
					case 2 : {
						// right to left, top to down
						foreach ($this->cElements as $key => $cElements) {
							$test = ($key - ($this->contentCount - ($a + 1))) / $this->contentCount;
							if (intval($test) == $test) {
								$markerArray["CONTENT"] .= $this->cObj->stdWrap($this->cElements[$key], array('wrap' => $wrap));
								$addContent = TRUE;
							}
						}
						break;
					}
					case 3 : {
						// top to down, left to right

						break;
					}
					case 4 : {
						// top to down, right to left

						break;
					}
				}
			} else {
				// wrap the content
				$markerArray["CONTENT"] = $this->cObj->stdWrap($this->cElements[$a], array('wrap' => $wrap));
				$addContent = TRUE;
			}
			$markerArray["REL"] = htmlspecialchars($this->rels[$a]);
			// Generate the QUOTE_TITLE
			$markerArray["DEFAULT_QUOTE_TITLE"]   = htmlspecialchars($this->cObj->substituteMarkerArray($this->pi_getLL('default_quote_title_template'), $markerArray, '###|###', 0));
			$markerArray["TAB_QUOTE_TITLE"]       = htmlspecialchars($this->cObj->substituteMarkerArray($this->pi_getLL('tab_quote_title_template'), $markerArray, '###|###', 0));
			$markerArray["ACCORDION_QUOTE_TITLE"] = htmlspecialchars($this->cObj->substituteMarkerArray($this->pi_getLL('accordion_quote_title_template'), $markerArray, '###|###', 0));

			if (isset($this->conf['additionalContentMarkers'])) {
				$additonalMarkerArray = array();
				// get additional markers
				$additionalMarkers = t3lib_div::trimExplode(',', $this->conf['additionalContentMarkers']);
				// get additional marker configuration
				if(count($additionalMarkers) > 0) {
					foreach($additionalMarkers as $additonalMarker) {
						$markerArray[strtoupper($additonalMarker)] = $this->cObj->cObjGetSingle($this->conf['additionalMarkerConf.'][$additonalMarker], $this->conf['additionalMarkerConf.'][$additonalMarker.'.']);
					}
				}
			}

			if ($markerArray["CONTENT"] || ($addContent && $this->confArr['showEmptyContent'])) {
				// add content to COLUMNS
				$columns .= $this->cObj->substituteMarkerArray($columnCode, $markerArray, '###|###', 0);
				// add content to TITLE
				$titles .= $this->cObj->substituteMarkerArray($titleCode, $markerArray, '###|###', 0);
			}
		}
		$return_string = $templateCode;
		$return_string = $this->cObj->substituteSubpart($return_string, '###TITLES###', $titles, 0);
		$return_string = $this->cObj->substituteSubpart($return_string, '###COLUMNS###', $columns, 0);

		if (isset($this->conf['additionalMarkers'])) {
			$additonalMarkerArray = array();
			// get additional markers
			$additionalMarkers = t3lib_div::trimExplode(',', $this->conf['additionalMarkers']);
			// get additional marker configuration
			if(count($additionalMarkers) > 0) {
				foreach($additionalMarkers as $additonalMarker) {
					$additonalMarkerArray[strtoupper($additonalMarker)] = $this->cObj->cObjGetSingle($this->conf['additionalMarkerConf.'][$additonalMarker], $this->conf['additionalMarkerConf.'][$additonalMarker.'.']);
				}
			}
			// add addtional marker content to template
			$return_string = $this->cObj->substituteMarkerArray($return_string, $additonalMarkerArray, '###|###', 0);
		}

		return $return_string;
	}

	/**
	* Return a errormessage if needed
	* @param string $msg
	* @param boolean $js
	* @return string
	*/
	public function outputError($msg='', $js=FALSE) {
		t3lib_div::devLog($msg, $this->extKey, 3);
		if ($this->confArr['frontendErrorMsg'] || ! isset($this->confArr['frontendErrorMsg'])) {
			return ($js ? "alert(".t3lib_div::quoteJSvalue($msg).")" : "<p>{$msg}</p>");
		} else {
			return NULL;
		}
	}

	/**
	* Set the piFlexform data
	*
	* @return void
	*/
	protected function setFlexFormData()
	{
		if (! count($this->piFlexForm)) {
			$this->pi_initPIflexForm();
			$this->piFlexForm = $this->cObj->data['pi_flexform'];
		}
	}

	/**
	 * Extract the requested information from flexform
	 * @param string $sheet
	 * @param string $name
	 * @param boolean $devlog
	 * @return string
	 */
	protected function getFlexformData($sheet='', $name='', $devlog=TRUE)
	{
		$this->setFlexFormData();
		if (! isset($this->piFlexForm['data'])) {
			if ($devlog === TRUE) {
				t3lib_div::devLog("Flexform Data not set", $this->extKey, 1);
			}
			return NULL;
		}
		if (! isset($this->piFlexForm['data'][$sheet])) {
			if ($devlog === TRUE) {
				t3lib_div::devLog("Flexform sheet '{$sheet}' not defined", $this->extKey, 1);
			}
			return NULL;
		}
		if (! isset($this->piFlexForm['data'][$sheet]['lDEF'][$name])) {
			if ($devlog === TRUE) {
				t3lib_div::devLog("Flexform Data [{$sheet}][{$name}] does not exist", $this->extKey, 1);
			}
			return NULL;
		}
		if (isset($this->piFlexForm['data'][$sheet]['lDEF'][$name]['vDEF'])) {
			return $this->pi_getFFvalue($this->piFlexForm, $name, $sheet);
		} else {
			return $this->piFlexForm['data'][$sheet]['lDEF'][$name];
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/pi1/class.tx_jfmulticontent_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/pi1/class.tx_jfmulticontent_pi1.php']);
}
?>