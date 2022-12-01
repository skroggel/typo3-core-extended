<?php

namespace Madj2k\CoreExtended\Domain\Repository;
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FileReferenceRepository
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class FileReferenceRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * Workaround for add-method because the foreign-field has to be updated manually
     *
     * @see https://docs.typo3.org/m/typo3/reference-fal/8.7/en-us/UsingFal/Examples/FileFolder/Index.html
     * @todo remove this work-around when it isn't needed any more
     * @param \Madj2k\CoreExtended\Domain\Model\FileReference $object
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function add($object)
    {

        // save object
        parent::add($object);
        $this->persistenceManager->persistAll();

        // now get number of current references to the target-object and update foreign-field
        /** @var  \TYPO3\CMS\Core\Database\Connection $connectionPages */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_file_reference');

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()
            ->removeByType(StartTimeRestriction::class)
            ->removeByType(EndTimeRestriction::class)
            ->removeByType(HiddenRestriction::class)
            ->removeByType(DeletedRestriction::class);

        $count = $queryBuilder->count('uid')
            ->from('sys_file_reference')
            ->where(
                $queryBuilder->expr()->eq(
                    'tablenames',
                    $queryBuilder->createNamedParameter($object->getTablenames(), \PDO::PARAM_STR)
                ),
                $queryBuilder->expr()->eq(
                    'fieldname',
                    $queryBuilder->createNamedParameter($object->getFieldname(), \PDO::PARAM_STR)
                )
            )
            ->execute()
            ->fetchColumn(0);

        // now set new count in foreign table
        $updateQueryBuilder = $connection->createQueryBuilder();
        $updateQueryBuilder->update($object->getTablenames())
            ->set($object->getFieldname(), $count)
            ->set('tstamp', time())
            ->where(
                $updateQueryBuilder->expr()->eq(
                    'uid',
                    $updateQueryBuilder->createNamedParameter($object->getUidForeign()), \PDO::PARAM_INT
                )
            );

        $updateQueryBuilder->execute();

    }
}
