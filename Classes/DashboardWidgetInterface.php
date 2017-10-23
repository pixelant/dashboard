<?php
namespace TYPO3\CMS\Dashboard;

/**
 * Interface for classes which provide a widget.
 */
interface DashboardWidgetInterface
{
    /**
     * Render content
     *
     * @return void
     */
    public function render();
}
