<?php
namespace Pixelant\Dashboard\Widget;

use Pixelant\Dashboard\Domain\Model\DashboardWidgetSettings;

interface WidgetControllerInterface
{
    /**
     * Render widget content
     *
     * @param DashboardWidgetSettings $widgetSettings
     * @return string
     */
    public function render(DashboardWidgetSettings $widgetSettings): string;
}
