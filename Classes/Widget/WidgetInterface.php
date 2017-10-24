<?php
namespace Pixelant\Dashboard\Widget;

use Pixelant\Dashboard\Domain\Model\DashboardWidgetSettings;

/**
 * Interface for classes which provide a widget.
 */
interface WidgetInterface
{
    /**
     * Render widget content
     *
     * @param DashboardWidgetSettings $widgetSettings
     * @return string
     */
    public function render(DashboardWidgetSettings $widgetSettings): string;
}
