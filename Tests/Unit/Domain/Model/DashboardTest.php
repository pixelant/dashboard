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
 * Test case for class \TYPO3\CMS\Dashboard\Domain\Model\Dashboard.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class DashboardTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Dashboard\Domain\Model\Dashboard
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = new \TYPO3\CMS\Dashboard\Domain\Model\Dashboard();
    }

    protected function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
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
    public function getDescriptionReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription()
    {
        $this->subject->setDescription('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'description',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDashboardWidgetSettingsReturnsInitialValueForDashboardWidgetSettings()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getDashboardWidgetSettings()
        );
    }

    /**
     * @test
     */
    public function setDashboardWidgetSettingsForObjectStorageContainingDashboardWidgetSettingsSetsDashboardWidgetSettings()
    {
        $dashboardWidgetSetting = new \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings();
        $objectStorageHoldingExactlyOneDashboardWidgetSettings = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneDashboardWidgetSettings->attach($dashboardWidgetSetting);
        $this->subject->setDashboardWidgetSettings($objectStorageHoldingExactlyOneDashboardWidgetSettings);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneDashboardWidgetSettings,
            'dashboardWidgetSettings',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addDashboardWidgetSettingToObjectStorageHoldingDashboardWidgetSettings()
    {
        $dashboardWidgetSetting = new \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings();
        $dashboardWidgetSettingsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', ['attach'], [], '', false);
        $dashboardWidgetSettingsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($dashboardWidgetSetting));
        $this->inject($this->subject, 'dashboardWidgetSettings', $dashboardWidgetSettingsObjectStorageMock);

        $this->subject->addDashboardWidgetSetting($dashboardWidgetSetting);
    }

    /**
     * @test
     */
    public function removeDashboardWidgetSettingFromObjectStorageHoldingDashboardWidgetSettings()
    {
        $dashboardWidgetSetting = new \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings();
        $dashboardWidgetSettingsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', ['detach'], [], '', false);
        $dashboardWidgetSettingsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($dashboardWidgetSetting));
        $this->inject($this->subject, 'dashboardWidgetSettings', $dashboardWidgetSettingsObjectStorageMock);

        $this->subject->removeDashboardWidgetSetting($dashboardWidgetSetting);
    }

    /**
     * @test
     */
    public function getBeuserReturnsInitialValueForBackendUser()
    {
    }

    /**
     * @test
     */
    public function setBeuserForBackendUserSetsBeuser()
    {
    }
}
