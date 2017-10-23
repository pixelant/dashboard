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
class DashboardWidgetSettings extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Widget Indetifier
     *
     * @var string
     */
    protected $widgetIdentifier = '';

    /**
     * State of widget (collapsed, expanded etc)
     *
     * @var string
     */
    protected $state = '';

    /**
     * x
     *
     * @var int
     */
    protected $x = 0;

    /**
     * y
     *
     * @var int
     */
    protected $y = 0;

    /**
     * width
     *
     * @var int
     */
    protected $width = 0;

    /**
     * height
     *
     * @var int
     */
    protected $height = 0;

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the state
     *
     * @return string $state
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the state
     *
     * @param string $state
     * @return void
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Returns the x
     *
     * @return int $x
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Sets the x
     *
     * @param int $x
     * @return void
     */
    public function setX($x)
    {
        $this->x = $x;
    }

    /**
     * Returns the y
     *
     * @return int $y
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Sets the y
     *
     * @param int $y
     * @return void
     */
    public function setY($y)
    {
        $this->y = $y;
    }

    /**
     * Returns the width
     *
     * @return int $width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Sets the width
     *
     * @param int $width
     * @return void
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Returns the height
     *
     * @return int $height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Sets the height
     *
     * @param int $height
     * @return void
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Returns the settingsFlexform
     *
     * @return string $settingsFlexform
     */
    public function getSettingsFlexform()
    {
        return $this->settingsFlexform;
    }

    /**
     * Sets the settingsFlexform
     *
     * @param string $settingsFlexform
     * @return void
     */
    public function setSettingsFlexform($settingsFlexform)
    {
        $this->settingsFlexform = $settingsFlexform;
    }

    /**
     * Returns the widgetIdentifier
     *
     * @return string widgetIdentifier
     */
    public function getWidgetIdentifier()
    {
        return $this->widgetIdentifier;
    }

    /**
     * Sets the widgetIdentifier
     *
     * @param string $widgetIdentifier
     * @return string widgetIdentifier
     */
    public function setWidgetIdentifier($widgetIdentifier)
    {
        $this->widgetIdentifier = $widgetIdentifier;
    }
}
