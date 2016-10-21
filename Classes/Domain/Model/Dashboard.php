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
 * Dashboard
 */
class Dashboard extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * Title
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title = '';

	/**
	 * Description
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Widgets Settings
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings>
	 */
	protected $dashboardWidgetSettings = NULL;

	/**
	 * Widgets
	 *
	 * @var \TYPO3\CMS\Beuser\Domain\Model\BackendUser
	 */
	protected $beuser = NULL;

	/**
	 * __construct
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->dashboardWidgetSettings = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

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
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Adds a DashboardWidget
	 *
	 * @param \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSetting
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings> dashboardWidgetSettings
	 */
	public function addDashboardWidgetSetting(\TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSetting) {
		$this->dashboardWidgetSettings->attach($dashboardWidgetSetting);
	}

	/**
	 * Removes a DashboardWidget
	 *
	 * @param \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSettingToRemove The DashboardWidgetSettings to be removed
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings> dashboardWidgetSettings
	 */
	public function removeDashboardWidgetSetting(\TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSettingToRemove) {
		$this->dashboardWidgetSettings->detach($dashboardWidgetSettingToRemove);
	}

	/**
	 * Returns the dashboardWidgetSettings
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings> dashboardWidgetSettings
	 */
	public function getDashboardWidgetSettings() {
		return $this->dashboardWidgetSettings;
	}

	/**
	 * Sets the dashboardWidgetSettings
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings> $dashboardWidgetSettings
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings> dashboardWidgetSettings
	 */
	public function setDashboardWidgetSettings(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $dashboardWidgetSettings) {
		$this->dashboardWidgetSettings = $dashboardWidgetSettings;
	}

	/**
	 * Returns the beuser
	 *
	 * @return \TYPO3\CMS\Beuser\Domain\Model\BackendUser $beuser
	 */
	public function getBeuser() {
		return $this->beuser;
	}

	/**
	 * Sets the beuser
	 *
	 * @param \TYPO3\CMS\Beuser\Domain\Model\BackendUser $beuser
	 * @return void
	 */
	public function setBeuser(\TYPO3\CMS\Beuser\Domain\Model\BackendUser $beuser) {
		$this->beuser = $beuser;
	}

}