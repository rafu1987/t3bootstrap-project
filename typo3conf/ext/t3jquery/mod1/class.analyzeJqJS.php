<?php
/***************************************************************
*  Copyright notice
*
*  Based on t3mootools from Peter Klein <peter@umloud.dk>
*  (c) 2007-2009 Juergen Furrer (juergen.furrer@gmail.com)
*  All rights reserved
*
*  This script is free software; you can redistribute it and/or modify
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
 * Module 'jQuery Analyze' for the 't3jquery' extension.
 *
 * @author     Juergen Furrer (juergen.furrer@gmail.com)
 * @package    TYPO3
 * @subpackage tx_t3jquery
 */
class analyzeJqJS
{
	private $version = '0.2';
	private $dependencies = array();
	private $configXML = array();

	/**
	 * Analyze a given JS script
	 * @param $file
	 * @param $string
	 * @param $config
	 * @return void
	 */
	public function __construct($inputFile='', $string=FALSE, $config=array()) {
		$this->configXML = $config;
		if ($string || $string = t3lib_div::getURL($inputFile)) {
			// we just look for double quote
			$string = str_replace("'", '"', $string);
			$result = array();
			$components = array();
			if (count($this->configXML) > 0) {
				// build the components array
				foreach ($this->configXML as $group) {
					if (count($group['files']) > 0) {
						foreach ($group['files'] as $file) {
							$components[$file['name']] = $file;
							$components[$file['name']]['groupname'] = $group['name'];
						}
					}
				}
				// search for 
				foreach ($this->configXML as $group) {
					if (count($group['files']) > 0) {
						foreach ($group['files'] as $file) {
							if ($this->contains($string, $file['sources']) === TRUE) {
								$result = array_merge($result, array($components[$file['depends']]['name'] => $components[$file['depends']]['groupname']));
								$result = array_merge($result, array($file['name'] => $components[$file['name']]['groupname']));
							}
						}
					}
				}
			}
			$this->dependencies = $result;
		}
	}

	/**
	 * Returns if the component needed
	 * 
	 * @return boolean
	 */
	private function contains($fileData, $array=array()) {
		if (!is_array($array)) {
			return FALSE;
		}
		foreach($array as $item) {
			if (strpos($fileData, $item) !== FALSE) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Returns all dependencies
	 * 
	 * @return array
	 */
	public function getDependencies() {
		return $this->dependencies;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3jquery/mod1/class.analyzeJqJS.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3jquery/mod1/class.analyzeJqJS.php']);
}
?>