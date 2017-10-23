<?php
namespace Pixelant\Dashboard\DashboardWidgets;

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

use Pixelant\Dashboard\Domain\Model\DashboardWidgetSettings;

/**
 * @deprecated
 */
abstract class AbstractWidget
{
    /**
     * @param DashboardWidgetSettings $dashboardWidgetSetting
     * @return array
     * @deprecated
     */
    public function getFlexFormSettings(DashboardWidgetSettings $dashboardWidgetSetting)
    {
        return ['settings' => $dashboardWidgetSetting->getSettings()];
    }
}
