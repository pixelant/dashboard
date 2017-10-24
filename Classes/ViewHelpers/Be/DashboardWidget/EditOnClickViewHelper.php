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
/**
 * View helper which renders a record list as known from the TYPO3 list module
 * Note: This feature is experimental!
 *
 * = Examples =
 *
 * <code title="Minimal">
 * <f:be.tableList tableName="fe_users" />
 * </code>
 * <output>
 * List of all "Website user" records stored in the configured storage PID.
 * Records will be editable, if the current BE user has got edit rights for the table "fe_users".
 * Only the title column (username) will be shown.
 * Context menu is active.
 * </output>
 *
 * <code title="Full">
 * <f:be.tableList tableName="fe_users" fieldList="{0: 'name', 1: 'email'}" storagePid="1" levels="2" filter='foo' recordsPerPage="10" sortField="name" sortDescending="true" readOnly="true" enableClickMenu="false" clickTitleMode="info" />
 * </code>
 * <output>
 * List of "Website user" records with a text property of "foo" stored on PID 1 and two levels down.
 * Clicking on a username will open the TYPO3 info popup for the respective record
 * </output>
 */
class EditOnClickViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper
{
    /**
     * Renders a record list as known from the TYPO3 list module
     * Note: This feature is experimental!
     *
     * @param \Pixelant\Dashboard\Domain\Model\Widget $widget
     * @return string the rendered content
     * @see \TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList
     */
    public function render(\Pixelant\Dashboard\Domain\Model\Widget $widget)
    {
        $editOnClick = \TYPO3\CMS\Backend\Utility\BackendUtility::editOnClick('&edit[tx_dashboard_domain_model_widget][' . $widget->getUid() . ']=edit');
        return $editOnClick;
    }
}
