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
use Pixelant\Dashboard\Domain\Model\Widget;

class SizeViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper
{
    /**
     * Returns path to widget icon
     *
     * @param string $widgetIdentifier
     * @return string Path to icon if ok, else fallback
     */
    public function render($widgetIdentifier)
    {
        $widgetSetting = new Widget($widgetIdentifier);
        $widget = $widgetSetting->getSettings();
        list($width, $height) = explode('x', $widget['size']);
        return $this->getColumnClassName((int)$width) . ' ' . $this->getHeightClassName((int)$height);
    }

    /**
     * Returns the correct css class name for width
     * @param  int $width
     * @return string css class name
     */
    protected function getColumnClassName($width)
    {
        $validWith = 1;
        if ($width >= 1 && $width <= 3) {
            $validWith = $width;
        }
        return $columnClass = 'col-md-' . $validWith * 4;
    }

    /**
     * Returns the correct css class name for height
     * @param  int $height
     * @return string css class name
     */
    protected function getHeightClassName($height)
    {
        $validHeight = 1;
        if ($height >= 1 && $height <= 3) {
            $validHeight = $height;
        }
        return 'height-' . $validHeight;
    }
}
