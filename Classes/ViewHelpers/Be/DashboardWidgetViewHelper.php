<?php
namespace Pixelant\Dashboard\ViewHelpers\Be;

class DashboardWidgetViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper
{
    /**
     * Renders a record list as known from the TYPO3 list module
     * Note: This feature is experimental!
     *
     * @param \Pixelant\Dashboard\Domain\Model\Widget $widget
     * @return string the rendered content
     * @see \TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList
     */
    public function render($widget)
    {
        $widgetSettings = $widget->getSettings();
        $widgetClassName = $widgetSettings['class'];

        if (class_exists($widgetClassName)) {
            $widgetController = $this->objectManager->get($widgetClassName);
            return $widgetController->render($widget);
        }
        return 'Class : ' . htmlspecialchars($widgetClassName) . ' could not be found!';
    }
}
