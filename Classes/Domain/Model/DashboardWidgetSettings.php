<?php
namespace TYPO3\CMS\Dashboard\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
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
 * Dashboard Widget Settings
 */
class DashboardWidgetSettings extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * Title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Widget Indetifier
	 *
	 * @var integer
	 */
	protected $widgetIdentifier = '';

	/**
	 * State of widget (collapsed, expanded etc)
	 *
	 * @var string
	 */
	protected $state = '';

	/**
	 * The position of the widget
	 *
	 * @var string
	 */
	protected $position = '';

	/**
	 * Widget settings
	 *
	 * @var string
	 */
	protected $settingsFlexform = '';

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the state
	 *
	 * @return string $state
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 * Sets the state
	 *
	 * @param string $state
	 * @return void
	 */
	public function setState($state) {
		$this->state = $state;
	}

	/**
	 * Returns the position
	 *
	 * @return string $position
	 */
	public function getPosition() {
		return $this->position;
	}

	/**
	 * Sets the position
	 *
	 * @param string $position
	 * @return void
	 */
	public function setPosition($position) {
		$this->position = $position;
	}

	/**
	 * Returns the settingsFlexform
	 *
	 * @return string $settingsFlexform
	 */
	public function getSettingsFlexform() {
		return $this->settingsFlexform;
	}

	/**
	 * Sets the settingsFlexform
	 *
	 * @param string $settingsFlexform
	 * @return void
	 */
	public function setSettingsFlexform($settingsFlexform) {
		$this->settingsFlexform = $settingsFlexform;
	}

	/**
	 * Returns the widgetIdentifier
	 *
	 * @return integer widgetIdentifier
	 */
	public function getWidgetIdentifier() {
		return $this->widgetIdentifier;
	}

	/**
	 * Sets the widgetIdentifier
	 *
	 * @param string $widgetIdentifier
	 * @return integer widgetIdentifier
	 */
	public function setWidgetIdentifier($widgetIdentifier) {
		$this->widgetIdentifier = $widgetIdentifier;
	}

}