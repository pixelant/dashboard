<?php
namespace TYPO3\CMS\Dashboard\DashboardWidgets;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings;

class AbstractWidget {

    /**
     * @param DashboardWidgetSettings $dashboardWidgetSetting
     * @return array
     */
    public function getFlexFormSettings($dashboardWidgetSetting) {
        $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        /** @var FlexFormService  $flexFormService */
        $flexFormService = $objectManager->get('TYPO3\\CMS\\Extbase\\Service\\FlexFormService');
        return $flexFormService->convertFlexFormContentToArray($dashboardWidgetSetting->getSettingsFlexform());
    }
}
