<?php
declare(strict_types=1);
namespace Pixelant\Dashboard\Domain\Repository;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Pixelant\Dashboard\Domain\Model\Widget;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for Dashboards
 */
class WidgetRepository extends Repository
{
    const TABLE_NAME = 'tx_dashboard_domain_model_widget';

    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    /**
     * Constructs a new Repository
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager, ConnectionPool $connectionPool)
    {
        parent::__construct($objectManager);
        $this->connectionPool = $connectionPool;
    }

    /**
     * @param int $uid
     * @return Widget
     */
    public function findByUid($uid)
    {
        return parent::findByUid($uid);
    }

    /**
     * @param int $dashBoardId
     * @return int
     */
    public function findNextAvailableVerticalPositionOnDashboard(int $dashBoardId): int
    {
        $availablePosition = 0;

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $result = $queryBuilder
            ->select('y', 'height')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'dashboard',
                    $queryBuilder->createNamedParameter($dashBoardId, \PDO::PARAM_INT)
                )
            )
            ->orderBy('y', 'DESC')
            ->addOrderBy('height', 'DESC')
            ->setMaxResults(1)
            ->execute()
            ->fetch();

        if ($result) {
            $availablePosition = $result['y'] + $result['height'];
        }
        return $availablePosition;
    }
}
