<?php

namespace Madj2k\CoreExtended\Utility;

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

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Versioning\VersionState;

/**
 * Class QueryUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class QueryUtility
{

    /**
     * get the WHERE clause for the enabled fields of this TCA table
     * depending on the context
     *
     * @param string $table table name
     * @return string the additional where clause, something like " AND deleted=0 AND hidden=0"
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     * @see \TYPO3\CMS\Frontend\Page::enableFields()
     */
    static public function getWhereClauseEnabled(string $table): string
    {

        /** @var  \TYPO3\CMS\Core\Context\Context $context */
        $context = GeneralUtility::makeInstance(Context::class);

        /**
         * @todo Problem with newsletter: hidden elements are included
        $showHidden = (bool)$context->getPropertyFromAspect('visibility',
            $table === 'pages' ? 'includeHiddenPages' : 'includeHiddenContent',
            false
        );

        if ($showHidden) {
            return '';
        }*/

        $ctrl = $GLOBALS['TCA'][$table]['ctrl'] ?? null;
        if (empty($ctrl) || !is_array($ctrl)) {
            return '';
        }

        $expressionBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table)
            ->expr();
        $constraints = [];

        // delete-field
        if ($ctrl['delete']) {
            $constraints[] = $expressionBuilder->eq($table . '.' . $ctrl['delete'], 0);
        }

        // enable-fields
        if (is_array($ctrl['enablecolumns'])) {
            // In case of versioning-preview, enableFields are ignored (checked in
            // versionOL())
            if (($ctrl['enablecolumns']['disabled'] ?? false) && !($ignore_array['disabled'] ?? false)) {
                $field = $table . '.' . $ctrl['enablecolumns']['disabled'];
                $constraints[] = $expressionBuilder->eq($field, 0);
            }
            if (($ctrl['enablecolumns']['starttime'] ?? false) && !($ignore_array['starttime'] ?? false)) {
                $field = $table . '.' . $ctrl['enablecolumns']['starttime'];
                $constraints[] = $expressionBuilder->lte(
                    $field,
                    $context->getPropertyFromAspect('date', 'accessTime', 0)
                );
            }
            if (($ctrl['enablecolumns']['endtime'] ?? false) && !($ignore_array['endtime'] ?? false)) {
                $field = $table . '.' . $ctrl['enablecolumns']['endtime'];
                $constraints[] = $expressionBuilder->orX(
                    $expressionBuilder->eq($field, 0),
                    $expressionBuilder->gt(
                        $field,
                        $context->getPropertyFromAspect('date', 'accessTime', 0)
                    )
                );
            }
            // if (($ctrl['enablecolumns']['fe_group'] ?? false) && !($ignore_array['fe_group'] ?? false)) {
                // currently we do not check for the groups here
            // }
        }

        return empty($constraints) ? '' : ' AND ' . $expressionBuilder->andX(...$constraints);
    }


    /**
     * get the WHERE clause for the enabled fields of this TCA table
     * depending on the context
     *
     * @param string $table table name
     * @return string the additional where clause, something like " AND deleted=0 AND hidden=0"
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     * @see \TYPO3\CMS\Core\Resource\AbstractRepository
     */
    static public function getWhereClauseDeleted(string $table): string
    {

        /** @var  \TYPO3\CMS\Core\Context\Context $context */
        $context = GeneralUtility::makeInstance(Context::class);

        /**
         * @todo Problem with newsletter: hidden elements are included
        $showHidden = (bool)$context->getPropertyFromAspect('visibility',
            $table === 'pages' ? 'includeHiddenPages' : 'includeHiddenContent',
            false
        );
        if ($showHidden) {
            return '';
        }*/

        if (empty($GLOBALS['TCA'][$table]['ctrl']['delete'])) {
            return '';
        }

        $constraints = [];
        $expressionBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table)
            ->expr();

        $constraints[] = $expressionBuilder->eq(
            $table . '.' . $GLOBALS['TCA'][$table]['ctrl']['delete'],
            0
        );

        return empty($constraints) ? '' : ' AND ' . $expressionBuilder->andX(...$constraints);
    }


    /**
     * get the WHERE clause for the language
     * depending on the context
     *
     * @param string $table
     * @param int $languageUid
     * @return string the additional where clause, something like " AND sys_language_uid = X"
     */
    static public function getWhereClauseLanguage(string $table, int $languageUid = 0): string
    {
        if (empty ($GLOBALS['TCA'][$table]['ctrl']['languageField'])) {
            return '';
        }

        $constraints = [];
        $expressionBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table)
            ->expr();

        $constraints[] = $expressionBuilder->eq(
            $table . '.' . $GLOBALS['TCA'][$table]['ctrl']['languageField'],
            intval($languageUid)
        );

        return empty($constraints) ? '' : ' AND ' . $expressionBuilder->andX(...$constraints);
    }


    /**
     * get the WHERE clause for the versioning
     * depending on the context
     *
     * @param string $table
     * @return string the additional where clause, something like " AND deleted=0 AND hidden=0"
     * @see \TYPO3\CMS\Frontend\Page::enableFields()
     */
    static public function getWhereClauseVersioning(string $table): string
    {
        if (empty ($GLOBALS['TCA'][$table]['ctrl']['versioningWS'])) {
            return '';
        }

        $constraints = [];
        $expressionBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table)
            ->expr();

        // Filter out placeholder records (new/moved/deleted items)
        // in case we are NOT in a versioning preview (that means we are online!)
        $constraints[] = $expressionBuilder->lte(
            $table . '.t3ver_state',
            new VersionState(VersionState::DEFAULT_STATE)
        );

        return empty($constraints) ? '' : ' AND ' . $expressionBuilder->andX(...$constraints);
    }


}
