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

/**
 * Testcase for class Tx_In2facebook_Domain_Model_OpenGraph.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage in2facebook
 * 
 * @author Alex Kellner <alexander.kellner@in2code.de>
 */
class Tx_In2facebook_Domain_Model_OpenGraphTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_In2facebook_Domain_Model_OpenGraph
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_In2facebook_Domain_Model_OpenGraph();
	}

	public function tearDown() {
		unset($this->fixture);
	}
	
	
	/**
	 * @test
	 */
	public function getTitleReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setTitleForStringSetsTitle() { 
		$this->fixture->setTitle('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getTitle()
		);
	}
	
	/**
	 * @test
	 */
	public function getTypeReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setTypeForStringSetsType() { 
		$this->fixture->setType('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getType()
		);
	}
	
	/**
	 * @test
	 */
	public function getUrlReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setUrlForStringSetsUrl() { 
		$this->fixture->setUrl('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getUrl()
		);
	}
	
	/**
	 * @test
	 */
	public function getImageReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setImageForStringSetsImage() { 
		$this->fixture->setImage('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getImage()
		);
	}
	
	/**
	 * @test
	 */
	public function getSiteNameReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setSiteNameForStringSetsSiteName() { 
		$this->fixture->setSiteName('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getSiteName()
		);
	}
	
	/**
	 * @test
	 */
	public function getAdminsReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setAdminsForStringSetsAdmins() { 
		$this->fixture->setAdmins('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getAdmins()
		);
	}
	
	/**
	 * @test
	 */
	public function getDescriptionReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setDescriptionForStringSetsDescription() { 
		$this->fixture->setDescription('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getDescription()
		);
	}
	
}
?>