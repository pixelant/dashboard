<?php
namespace TYPO3\CMS\Dashboard\DashboardWidgets;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

use TYPO3\CMS\Dashboard\DashboardWidgetInterface;
use TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings;
use TYPO3\CMS\Fluid\View\StandaloneView;

class IframeWidget extends AbstractWidget implements DashboardWidgetInterface
{
    const IDENTIFIER = '1487642496';

    /**
     * Iframe Url
     *
     * @var string
     */
    protected $url = '';

    /**
     * Iframe scrolling attribute
     *
     * @var string
     */
    protected $scrolling = '';

    /**
     * Widget configuration
     *
     * @var array
     */
    protected $widget = [];

    /**
     * Renders content
     * @param DashboardWidgetSettings $dashboardWidgetSetting
     * @return string the rendered content
     */
    public function render($dashboardWidgetSetting = null)
    {
        $this->initialize($dashboardWidgetSetting);
        $content = $this->generateContent();
        return $content;
    }

    /**
     * Initializes settings from flexform
     * @param DashboardWidgetSettings $dashboardWidgetSetting
     * @return void
     */
    private function initialize($dashboardWidgetSetting = null)
    {
        $flexformSettings = $this->getFlexFormSettings($dashboardWidgetSetting);
        $this->url = $flexformSettings['settings']['url'];
        $this->scrolling = $flexformSettings['settings']['scrolling'];
        $this->widget = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dashboard']['widgets'][$dashboardWidgetSetting->getWidgetIdentifier()];
    }

    /**
     * Generates the content
     * @return string
     */
    private function generateContent()
    {
        $widgetTemplateName = $this->widget['template'];
        /** @var StandaloneView $actionView */
        $actionView = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $template = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($widgetTemplateName);
        $actionView->setTemplatePathAndFilename($template);
        $actionView->assignMultiple([
            'url' => $this->url,
            'scrolling' => $this->scrolling,
        ]);
        return $actionView->render();
    }
}
