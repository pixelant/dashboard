<?php

namespace TYPO3\CMS\Dashboard\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 
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
 * Test case for class \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class DashboardWidgetSettingsTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getTitleReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getTitle()
		);
	}

	/**
	 * @test
	 */
	public function setTitleForStringSetsTitle() {
		$this->subject->setTitle('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'title',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getWidgetIdentifierReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getWidgetIdentifier()
		);
	}

	/**
	 * @test
	 */
	public function setWidgetIdentifierForIntegerSetsWidgetIdentifier() {
		$this->subject->setWidgetIdentifier(12);

		$this->assertAttributeEquals(
			12,
			'widgetIdentifier',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getStateReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getState()
		);
	}

	/**
	 * @test
	 */
	public function setStateForStringSetsState() {
		$this->subject->setState('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'state',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPositionReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getPosition()
		);
	}

	/**
	 * @test
	 */
	public function setPositionForStringSetsPosition() {
		$this->subject->setPosition('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'position',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getSettingsFlexformReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getSettingsFlexform()
		);
	}

	/**
	 * @test
	 */
	public function setSettingsFlexformForStringSetsSettingsFlexform() {
		$this->subject->setSettingsFlexform('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'settingsFlexform',
			$this->subject
		);
	}
}
