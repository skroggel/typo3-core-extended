<?php

namespace Madj2k\CoreExtended\Helper;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * Class QueryTypo3
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class QueryTypo3
{

    /**
     * get the WHERE clause for the enabled fields of this TCA table
     * depending on the context
     *
     * @param string $table table name
     * @return string the additional where clause, something like " AND deleted=0 AND hidden=0"
     * @see \TYPO3\CMS\Core\Resource\AbstractRepository
     */
    static public function getWhereClauseForEnableFields(string $table): string
    {
        $whereClause = \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields($table);
        $whereClause .= self::getWhereClauseForDeleteFields($table);

        return $whereClause;
    }


    /**
     * get the WHERE clause for the enabled fields of this TCA table
     * depending on the context
     *
     * @param string $table table name
     * @return string the additional where clause, something like " AND deleted=0 AND hidden=0"
     * @see \TYPO3\CMS\Core\Resource\AbstractRepository
     */
    static public function getWhereClauseForDeleteFields(string $table): string
    {

        if (empty($GLOBALS['TCA'][$table]['ctrl']['delete'])) {
            return '';
        }
        $expressionBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table)
            ->expr();

        return ' AND ' . $expressionBuilder->eq(
                $table . '.' . $GLOBALS['TCA'][$table]['ctrl']['delete'],
                0
            );
    }

    /**
     * get the WHERE clause for the language
     * depending on the context
     *
     * @param string $table
     * @param integer $languageUid
     * @return string the additional where clause, something like " AND sys_language_uid = X"
     */
    static public function getWhereClauseForLanguageFields(string $table, int $languageUid = 0): string
    {

        if (empty ($GLOBALS['TCA'][$table]['ctrl']['languageField'])) {
            return '';
        }

        $expressionBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table)
            ->expr();

        return ' AND ' . $expressionBuilder->eq(
                $table . '.' . $GLOBALS['TCA'][$table]['ctrl']['languageField'],
                intval($languageUid)
            );

    }

    /**
     * get the WHERE clause for the versioning
     * depending on the context
     *
     * @param string $table
     * @return string the additional where clause, something like " AND deleted=0 AND hidden=0"
     * @throws \TYPO3\CMS\Core\Type\Exception\InvalidEnumerationValueException
     *@see \TYPO3\CMS\Core\Resource\AbstractRepository
     */
    static public function getWhereClauseForVersioning(string $table): string
    {
        if (empty ($GLOBALS['TCA'][$table]['ctrl']['versioningWS'])) {
            return '';
        }

        $expressionBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table)
            ->expr();

        return ' AND ' . $expressionBuilder->eq(
                $table . '.' . $GLOBALS['TCA'][$table]['ctrl']['versioningWS'],
                new \TYPO3\CMS\Core\Versioning\VersionState(\TYPO3\CMS\Core\Versioning\VersionState::DEFAULT_STATE)
            );
    }


}
