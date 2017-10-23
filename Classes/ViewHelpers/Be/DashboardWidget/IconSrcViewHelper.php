<?php
namespace TYPO3\CMS\Dashboard\ViewHelpers\Be\DashboardWidget;

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
class IconSrcViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper
{
    /**
     * Returns path to widget icon
     *
     * @param string $widgetIdentifier
     * @return string Path to icon if ok, else fallback
     */
    public function render($widgetIdentifier)
    {
        $fileName = 'EXT:dashboard/Resources/Public/Icons/dashboardWidget.png';
        $widget = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dashboard']['widgets'][$widgetIdentifier];
        $widgetIconName = $widget['icon'];
        $absoluteFilename = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($widgetIconName, true);
        if (file_exists($absoluteFilename)) {
            if (\TYPO3\CMS\Core\Utility\GeneralUtility::isAllowedAbsPath($absoluteFilename)) {
                $fileName = $widgetIconName;
            }
        }
        return $fileName;
    }
}
