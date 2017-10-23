<?php
namespace Pixelant\Dashboard\ViewHelpers\Be\DashboardWidget;

/*                                                                        *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Pixelant\Dashboard\Domain\Model\DashboardWidgetSettings;

class IconViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper
{
    /**
     * Returns widget icon name, either defined one or default
     *
     * @param DashboardWidgetSettings $widgetSettings
     * @return string
     */
    public function render(DashboardWidgetSettings $widgetSettings)
    {
        $defaultIcon = 'dashboard-widget-default';
        $widget = $widgetSettings->getSettings();
        return $widget['icon'] ?? $defaultIcon;
    }
}
