<?php
namespace Pixelant\Dashboard\Domain\Model;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\FlexFormService;

/**
 * Represents dashboard widget Settings
 */
class Widget extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var FlexFormService
     */
    private $flexFormService;

    /**
     * Title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Widget Identifier
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
     * @param string $widgetIdentifier
     * @param string $title
     * @param string $settingsFlexform
     * @param FlexFormService|null $flexFormService
     */
    public function __construct(
        $widgetIdentifier,
        $title = '',
        $settingsFlexform = '',
        FlexFormService $flexFormService = null
    ) {
        $this->flexFormService = $flexFormService ?: GeneralUtility::makeInstance(FlexFormService::class);
    }

    /**
     * Make sure a thawed object also gets this dependency injected
     *
     * @param FlexFormService $flexFormService
     */
    public function injectFlexFormService(FlexFormService $flexFormService)
    {
        $this->flexFormService = $flexFormService;
    }

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
     * Returns the state
     *
     * @return string $state
     */
    public function getState()
    {
        return $this->state;
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

    public function getSettings(): array
    {
        $widgetSettings = [];
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dashboard']['widgets'][$this->widgetIdentifier])) {
            $widgetSettings = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dashboard']['widgets'][$this->widgetIdentifier];
        }
        if (empty($this->settingsFlexform)) {
            return $widgetSettings;
        }
        return array_replace($widgetSettings, $this->flexFormService->convertFlexFormContentToArray($this->settingsFlexform)['settings'] ?? []);
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
}
