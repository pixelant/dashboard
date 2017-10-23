<?php
namespace Pixelant\Dashboard\ViewHelpers\Be;

class DashboardWidgetViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper
{
    /**
     * Renders a record list as known from the TYPO3 list module
     * Note: This feature is experimental!
     *
     * @param \Pixelant\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSetting
     * @return string the rendered content
     * @see \TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList
     */
    public function render($dashboardWidgetSetting)
    {
        $widget = $dashboardWidgetSetting->getSettings();
        $widgetClassName = $widget['class'];

        if (class_exists($widgetClassName)) {
            $widgetClass = $this->objectManager->get($widgetClassName);
            return $widgetClass->render($dashboardWidgetSetting);
        }
        return 'Class : ' . htmlspecialchars($widgetClassName) . ' could not be found!';
    }
}
