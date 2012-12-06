<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Alex Kellner <alexander.kellner@in2code.de>, in2code
*
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 3 of the License, or
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
 * Opengraph
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class Tx_In2facebook_Domain_Model_Opengraph extends Tx_Extbase_DomainObject_AbstractValueObject {

	/**
	 * title
	 *
	 * @var string $title
	 */
	protected $title;

	/**
	 * type
	 *
	 * @var string $type
	 */
	protected $type;

	/**
	 * url
	 *
	 * @var string $url
	 */
	protected $url;

	/**
	 * image
	 *
	 * @var string $image
	 */
	protected $image;

	/**
	 * siteName
	 *
	 * @var string $siteName
	 */
	protected $siteName;

	/**
	 * admins
	 *
	 * @var string $admins
	 */
	protected $admins;

	/**
	 * description
	 *
	 * @var string $description
	 */
	protected $description;

	/**
	 * Setter for title
	 *
	 * @param string $title title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Getter for title
	 *
	 * @return string title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Setter for type
	 *
	 * @param string $type type
	 * @return void
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * Getter for type
	 *
	 * @return string type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Setter for url
	 *
	 * @param string $url url
	 * @return void
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * Getter for url
	 *
	 * @return string url
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Setter for image
	 *
	 * @param string $image image
	 * @return void
	 */
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	 * Getter for image
	 *
	 * @return string image
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * Setter for siteName
	 *
	 * @param string $siteName siteName
	 * @return void
	 */
	public function setSiteName($siteName) {
		$this->siteName = $siteName;
	}

	/**
	 * Getter for siteName
	 *
	 * @return string siteName
	 */
	public function getSiteName() {
		return $this->siteName;
	}

	/**
	 * Setter for admins
	 *
	 * @param string $admins admins
	 * @return void
	 */
	public function setAdmins($admins) {
		$this->admins = $admins;
	}

	/**
	 * Getter for admins
	 *
	 * @return string admins
	 */
	public function getAdmins() {
		return $this->admins;
	}

	/**
	 * Setter for description
	 *
	 * @param string $description description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Getter for description
	 *
	 * @return string description
	 */
	public function getDescription() {
		return $this->description;
	}

}
?>