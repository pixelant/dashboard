<?php
namespace Pixelant\Dashboard\Widget;

/**
 * Interface for classes which provide a widget.
 */
interface WidgetInterface
{
    /**
     * Render widget content
     *
     * @return string
     */
    public function render(): string;
}
