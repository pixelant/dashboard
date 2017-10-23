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

use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;

class GridAttributesViewHelper extends AbstractBackendViewHelper
{
    /**
     * Returns a widget drop and drop attributes
     *
     * @param \Pixelant\Dashboard\Domain\Model\DashboardWidgetSettings $widgetSetting
     * @param int $index
     * @param string $className
     *
     * @return string element grid attributes
     */
    public function render($widgetSetting, $index, $className = 'grid-item')
    {
        $numberOfCols = 3;
        $widget = $widgetSetting->getSettings();
        list($width, $height) = explode('x', $widget['size']);
        $width = $this->getItemWidth((int)$width, $numberOfCols);
        $height = $this->getItemHeight((int)$height);

        // NOTE: e.g 2x2 row-column grid = 0x0,0x1,1x0,1x1
        if ($index < $numberOfCols) {
            $col = $index;
            $row = 0;
        } else {
            $col = $index % $numberOfCols;
            $row = intval($index / $numberOfCols);
        }

        $attributes = 'class="%s col-md-%d height-%d"' .
            ' data-id="%s-%d" data-width="%d" data-height="%d" data-row="%d" data-column="%d"';
        $attributes = sprintf(
            $attributes,
            $className,
            ($width * 4),
            $height,
            $widgetIdentifier,
            $widgetSetting->getUid(),
            $width,
            $height,
            $row,
            $col
        );
        return $attributes;
    }

    /**
     * Returns the correct grid item width
     *
     * @param int $width
     * @param int $numberOfCols
     * @return string css class name
     */
    protected function getItemWidth($width, $numberOfCols)
    {
        if ($width >= $numberOfCols) {
            $validWith = $numberOfCols;
        } elseif ($width) {
            $validWith = $width;
        } else {
            $validWith = 1;
        }
        return $validWith;
    }

    /**
     * Returns the correct grid item height
     *
     * @param int $height
     * @return string css class name
     */
    protected function getItemHeight($height)
    {
        if ($height >= 3) {
            $validHeight = 3;
        } elseif ($height) {
            $validHeight = $height;
        } else {
            $validHeight = 1;
        }
        return $validHeight;
    }
}
