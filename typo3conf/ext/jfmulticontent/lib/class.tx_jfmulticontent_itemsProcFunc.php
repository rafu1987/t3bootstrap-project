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

require_once (PATH_t3lib . 'class.t3lib_page.php');

/**
 * 'itemsProcFunc' for the 'jfmulticontent' extension.
 *
 * @author     Juergen Furrer <juergen.furrer@gmail.com>
 * @package    TYPO3
 * @subpackage tx_jfmulticontent
 */
class tx_jfmulticontent_itemsProcFunc
{
	/**
	 * Get defined views for dropdown (from hook)
	 * @return array
	 */
	public function getViews($config, $item) {
		$optionList = array();
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['jfmulticontent']['getViews'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['jfmulticontent']['getViews'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				if (! method_exists($_procObj, 'isActive') || (method_exists($_procObj, 'isActive') && $_procObj->isActive())) {
					$optionList[] = array(
						trim($_procObj->getname()),
						trim($_procObj->getIdentifier()),
					);
				}
			}
		}
		$config['items'] = array_merge($config['items'], $optionList);
		return $config;
	}

	/**
	 * Get the defined styles by pagesetup
	 * @param array $config
	 * @param array $item
	 */
	public function getStyle($config, $item) {
		$allStyles = array(
			array(
				$GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.style.I.0'),
				'2column',
				'EXT:jfmulticontent/selicon_tt_content_tx_jfmulticontent_style_0.gif',
			),
			array(
				$GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.style.I.1'),
				'3column',
				'EXT:jfmulticontent/selicon_tt_content_tx_jfmulticontent_style_1.gif',
			),
			array(
				$GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.style.I.2'),
				'4column',
				'EXT:jfmulticontent/selicon_tt_content_tx_jfmulticontent_style_2.gif',
			),
			array(
				$GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.style.I.6'),
				'5column',
				'EXT:jfmulticontent/selicon_tt_content_tx_jfmulticontent_style_6.gif',
			),
			array(
				$GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.style.I.3'),
				'tab',
				'EXT:jfmulticontent/selicon_tt_content_tx_jfmulticontent_style_3.gif',
			),
			array(
				$GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.style.I.4'),
				'accordion',
				'EXT:jfmulticontent/selicon_tt_content_tx_jfmulticontent_style_4.gif',
			),
			array(
				$GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.style.I.5'),
				'slider',
				'EXT:jfmulticontent/selicon_tt_content_tx_jfmulticontent_style_5.gif',
			),
			array(
				$GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.style.I.7'),
				'slidedeck',
				'EXT:jfmulticontent/selicon_tt_content_tx_jfmulticontent_style_7.gif',
			),
			array(
				$GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.style.I.8'),
				'easyaccordion',
				'EXT:jfmulticontent/selicon_tt_content_tx_jfmulticontent_style_8.gif',
			),
			array(
				$GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.style.I.9'),
				'booklet',
				'EXT:jfmulticontent/selicon_tt_content_tx_jfmulticontent_style_9.gif',
			),
			array(
				$GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.style.I.10'),
				'typoscript',
				'EXT:jfmulticontent/selicon_tt_content_tx_jfmulticontent_style_10.png',
			),
		);
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);
		$styles = $confArr['style.'];
		if (count($styles) > 0) {
			foreach ($styles as $key => $val) {
				if ($val) {
					$availableStyles[] = $key;
				}
			}
		}
		if (count($availableStyles) < 1) {
			$availableStyles = array('2column','3column','4column','5column','tab','accordion','slider','slidedeck','easyaccordion','booklet');
		}
		$allowedStyles = array();
		foreach ($allStyles as $key => $style) {
			if (in_array(trim($style[1]), $availableStyles)) {
				$allowedStyles[] = $style;
			}
		}
		$pageTS = t3lib_BEfunc::getPagesTSconfig($config['row']['pid']);
		$jfmulticontentStyles = t3lib_div::trimExplode(",", $pageTS['mod.']['jfmulticontent.']['availableStyles'], TRUE);
		$optionList = array();
		if (count($jfmulticontentStyles) > 0) {
			foreach ($allowedStyles as $key => $style) {
				if (in_array(trim($style[1]), $jfmulticontentStyles)) {
					$optionList[] = $style;
				}
			}
		} else {
			$optionList = $allowedStyles;
		}
		$config['items'] = array_merge($config['items'], $optionList);
		return $config;
	}

	/**
	 * Get defined Class inner for dropdown
	 * @return array
	 */
	public function getClassInner($config, $item) {
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);
		$availableClasses = t3lib_div::trimExplode(",", $confArr['classInner']);
		if (count($availableClasses) < 1 || ! $confArr['classInner']) {
			$availableClasses = array('','16','20','25','33','38','40','50','60','62','66','75','80');
		}
		$pageTS = t3lib_BEfunc::getPagesTSconfig($config['row']['pid']);
		$jfmulticontentClasses = t3lib_div::trimExplode(",", $pageTS['mod.']['jfmulticontent.']['classInner'], TRUE);
		$optionList = array();
		if (count($jfmulticontentClasses) > 0) {
			foreach ($availableClasses as $key => $availableClass) {
				if (in_array(trim($availableClass), $jfmulticontentClasses)) {
					$optionList[] = array(
						trim($availableClass),
						trim($availableClass),
					);
				}
			}
		} else {
			foreach ($availableClasses as $key => $availableClass) {
				$optionList[] = array(
					trim($availableClass),
					trim($availableClass),
				);
			}
		}
		$config['items'] = array_merge($config['items'], $optionList);
		return $config;
	}

	/**
	 * Get all themes for anythingSlider
	 * @return array
	 */
	public function getAnythingSliderThemes($config, $item) {
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);
		if (! is_dir(t3lib_div::getFileAbsFileName($confArr['anythingSliderThemeFolder']))) {
			// if the defined folder does not exist, define the default folder
			$confArr['anythingSliderThemeFolder'] = "EXT:jfmulticontent/res/anythingslider/themes/";
		}
		$items = t3lib_div::get_dirs(t3lib_div::getFileAbsFileName($confArr['anythingSliderThemeFolder']));
		if (count($items) > 0) {
			$optionList = array();
			foreach ($items as $key => $item) {
				$item = trim($item);
				if (! preg_match('/^\./', $item)) {
					$optionList[] = array(
						$item,
						$item
					);
				}
			}
			$config['items'] = array_merge($config['items'], $optionList);
		}
		return $config;
	}

	/**
	 * Get all modes for anythingSlider
	 * @return array
	 */
	public function getAnythingSliderModes($config, $item) {
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);
		$availableModes = t3lib_div::trimExplode(",", $confArr['anythingSliderModes']);
		if (count($availableModes) < 1 || ! $confArr['anythingSliderModes']) {
			$availableModes = array('horizontal', 'vertical', 'fade');
		}
		$pageTS = t3lib_BEfunc::getPagesTSconfig($config['row']['pid']);
		$jfmulticontentModes = t3lib_div::trimExplode(",", $pageTS['mod.']['jfmulticontent.']['anythingSliderModes'], TRUE);
		$optionList = array();
		if (count($jfmulticontentModes) > 0) {
			foreach ($availableModes as $key => $availableMode) {
				if (in_array(trim($availableMode), $jfmulticontentModes)) {
					$optionList[] = array(
						trim($availableMode),
						trim($availableMode),
					);
				}
			}
		} else {
			foreach ($availableModes as $key => $availableMode) {
				$optionList[] = array(
					trim($availableMode),
					trim($availableMode),
				);
			}
		}
		$config['items'] = array_merge($config['items'], $optionList);
		return $config;
	}

	/**
	 * Get all skins for easyAccordion
	 * @return array
	 */
	public function getEasyaccordionSkin($config, $item) {
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);
		if (! is_dir(t3lib_div::getFileAbsFileName($confArr['easyAccordionSkinFolder']))) {
			// if the defined folder does not exist, define the default folder
			$confArr['easyAccordionSkinFolder'] = "EXT:jfmulticontent/res/easyaccordion/skins/";
		}
		$items = t3lib_div::get_dirs(t3lib_div::getFileAbsFileName($confArr['easyAccordionSkinFolder']));
		if (count($items) > 0) {
			$optionList = array();
			foreach ($items as $key => $item) {
				$item = trim($item);
				if (! preg_match('/^\./', $item)) {
					$optionList[] = array(
						ucfirst($item),
						$item
					);
				}
			}
			$config['items'] = array_merge($config['items'], $optionList);
		}
		return $config;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/lib/class.tx_jfmulticontent_itemsProcFunc.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/lib/class.tx_jfmulticontent_itemsProcFunc.php']);
}
?>