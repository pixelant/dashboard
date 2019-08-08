<?php
namespace Pixelant\Dashboard\Widget;

use Pixelant\Dashboard\Domain\Model\Widget;

interface WidgetControllerInterface
{
    /**
     * Render widget content
     *
     * @param Widget $widget
     * @return string
     */
    public function render(Widget $widget): string;
}
