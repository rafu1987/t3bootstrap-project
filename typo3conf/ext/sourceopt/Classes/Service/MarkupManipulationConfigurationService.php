<?php

/* * *************************************************************
 *  Copyright notice
 *
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

/**
 * Base configuration an properties for markup manipulation
 */
class Tx_Sourceopt_Service_MarkupManipulationConfigurationService {

	/**
	 * The new line char
	 *
	 * @var string
	 */
	protected $newLine = "\n";

	/**
	 * The tab char
	 *
	 * @var string
	 */
	protected $tab = "\t";

	/**
	 * The HTML for the manipulation
	 *
	 * @var string
	 */
	protected $html = "";

	/**
	 * Init the object
	 *
	 * @param string $html
	 */
	public function __construct($html = NULL) {
		if (defined('TYPO3_OS') && TYPO3_OS === 'WIN') {
			$this->setNewLine("\r\n");
		}
		if ($html !== NULL) {
			$this->setHtml($html);
		}
	}

	/**
	 * get the new line string
	 *
	 * @return string
	 */
	public function getNewLine() {
		return $this->newLine;
	}

	/**
	 * Set the new line string
	 *
	 * @param string $newLine
	 */
	public function setNewLine($newLine) {
		$this->newLine = $newLine;
	}

	/**
	 * Get the tab string
	 *
	 * @return string
	 */
	public function getTab() {
		return $this->tab;
	}

	/**
	 * Set the tab string
	 *
	 * @param string $tab
	 */
	public function setTab($tab) {
		$this->tab = $tab;
	}

	/**
	 * Set the html
	 *
	 * @return string
	 */
	public function getHtml() {
		return $this->html;
	}

	/**
	 * Get the html
	 *
	 * @param string $html
	 */
	public function setHtml($html) {
		$this->html = $html;
	}

}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/sourceopt/Classes/Service/MarkupManipulationConfigurationService.php"]) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/sourceopt/Classes/Service/MarkupManipulationConfigurationService.php"]);
}
?>