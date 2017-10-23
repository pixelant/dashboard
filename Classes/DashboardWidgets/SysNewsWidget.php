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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Dashboard\DashboardWidgetInterface;

class SysNewsWidget implements DashboardWidgetInterface
{
    const IDENTIFIER = '1439446997';

    /**
     * Limit, If set, it will limit the results in the list.
     *
     * @var int
     */
    protected $limit = 0;

    /**
     * Widget settings
     *
     * @var array
     */
    protected $widget = null;

    /**
     * Renders content
     * @param \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSetting
     * @return string the rendered content
     */
    public function render($dashboardWidgetSetting = null)
    {
        $this->initialize($dashboardWidgetSetting);
        return $this->generateContent();
    }

    /**
     * Initializes settings from flexform
     * @param \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSetting
     * @return void
     */
    private function initialize($dashboardWidgetSetting = null)
    {
        $settings = $dashboardWidgetSetting->getSettings();
        $this->limit = (int)$settings['limit'];
        $this->widget = $settings;
    }

    /**
     * Generates the content
     * @return string
     */
    private function generateContent()
    {
        $widgetTemplateName = $this->widget['template'];
        $sysNewsView = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $template = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($widgetTemplateName);
        $sysNewsView->setTemplatePathAndFilename($template);
        $systemNews = $this->getSystemNews();
        $sysNewsView->assign('systemNews', $systemNews);
        return $sysNewsView->render();
    }

    /**
     * Gets news from sys_news and converts them into a format suitable for
     * showing them at the login screen.
     *
     * @return array An array of login news.
     */
    protected function getSystemNews()
    {
        $systemNewsTable = 'sys_news';
        $systemNews = [];
        $systemNewsRecords = $this->getDatabaseConnection()->exec_SELECTgetRows(
            'title, content, crdate',
            $systemNewsTable,
            '1=1' . BackendUtility::BEenableFields($systemNewsTable) . BackendUtility::deleteClause($systemNewsTable),
            '',
            'crdate DESC',
            $this->getLimit()
        );
        foreach ($systemNewsRecords as $systemNewsRecord) {
            $systemNews[] = [
                'date' => $systemNewsRecord['crdate'],
                'header' => $systemNewsRecord['title'],
                'content' => $systemNewsRecord['content'],
            ];
        }
        return $systemNews;
    }

    /**
     * Return DatabaseConnection
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Return limit for query
     *
     * @return string
     */
    protected function getLimit()
    {
        return (int)$this->limit > 0 ? (int)$this->limit : '';
    }
}
