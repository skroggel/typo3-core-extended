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

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Versioning\VersionState;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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
     * get active columns for a select query
     *
     * @param string $table table name
     * @return array the active columns
     * @see \TYPO3\CMS\Frontend\Page::enableFields()
     */
    static public function getSelectColumns(string $table): array
    {
        $columns = $GLOBALS['TCA'][$table]['columns'] ?? null;
        if (empty($columns) || !is_array($columns)) {
            return [];
        }

        $select = [];
        foreach ($columns as $column => $config) {
            if (
                ($config['config']['type'])
                && ($config['config']['type'] != 'none')
            ){
                $select[] = $column;
            }
        }

        return $select;
    }


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


    /**
     * Recursively fetch all descendants of a given page - slightly modified version of core-method with applied caching
     *
     * @param int $id uid of the page
     * @param int $depth
     * @param int $begin
     * @param string $permClause
     * @param bool $excludeNoIndex
     * @param bool $isSubCall
     * @return string comma separated list of descendant pages
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @see \TYPO3\CMS\Core\Database\QueryGenerator::getTreeList()
     */
    static public function getTreeList(
        int $id,
        int $depth = 99999,
        int $begin = 0,
        string $permClause = '',
        bool $excludeNoIndex = false,
        bool $isSubCall = false
    ): string {

        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('core_extended_treelist');
        $cacheEntryIdentifier = 'Treelist_' . $id . '_' . md5($depth . '_' . $begin . '_' . $permClause);
        $queryBuilder = \Madj2k\CoreExtended\Utility\GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');

        if (
            !$isSubCall
            && ($cache->has($cacheEntryIdentifier))
            && ($cacheResult = $cache->get($cacheEntryIdentifier))
            && is_array($cacheResult)
            && (! empty($cacheResult['pageUids']))
            && (! empty($cacheResult['cacheTstamp']))
        ) {

            // check for new pages in existing page tree, based on timestamp of cache
            $count = $queryBuilder->count('uid')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->in(
                        'pid',
                        GeneralUtility::trimExplode(
                            ',',
                            preg_replace('/[^0-9,]+,/', '', $cacheResult['pageUids']),
                            true
                        )
                    ),
                    $queryBuilder->expr()->gt(
                        'tstamp',
                        $queryBuilder->createNamedParameter(
                            $cacheResult['cacheTstamp'],
                            \PDO::PARAM_INT
                        )
                    ),
                    QueryHelper::stripLogicalOperatorPrefix($permClause)
                )
                ->execute()
                ->fetchOne();

            if (! $count) {
                return $cacheResult['pageUids'];
            }
        }

        if ($id < 0) {
            $id = abs($id);
        }

        if ($begin === 0) {
            $theList = $id;
        } else {
            $theList = '';
        }

        if ($id && $depth > 0) {
            $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

            $statement = $queryBuilder->select('uid', 'no_index')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('sys_language_uid', 0)
                )
                ->orderBy('uid');

            if ($permClause !== '') {
                $queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($permClause));
            }

            $statement = $queryBuilder->execute();
            while ($row = $statement->fetch()) {

                if (($row['no_index']) && $excludeNoIndex) {
                    continue;
                }

                if ($begin <= 0) {
                    $theList .= ',' . $row['uid'];
                }

                if ($depth > 1) {
                    $theSubList = self::getTreeList($row['uid'], $depth - 1, $begin - 1, $permClause, $excludeNoIndex, true);
                    if (!empty($theList) && !empty($theSubList) && ($theSubList[0] !== ',')) {
                        $theList .= ',';
                    }
                    $theList .= $theSubList;
                }
            }
        }

        if (!$isSubCall) {
            $cache->set(
                $cacheEntryIdentifier,
                [
                    'pageUids' => $theList,
                    'cacheTstamp' => time(),
                ],
                array(
                    'core_extended_treelist'
                ),
                0 // forever
            );
        }

        return $theList;
    }


    /**
     * Get full SQL query for debugging
     *
     * @param \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder
     * @return string
     * @todo write a test
     */
    public static function getFullSql (QueryBuilder $queryBuilder): string
    {
        $sql = $queryBuilder->getSQL();
        $parameters = $queryBuilder->getParameters();

        $search = array();
        $replace = array();
        krsort($parameters);

        foreach ($parameters as $k => $v) {
            $search[] = ':' . $k;
            $replace[] = '\'' . $v . '\'';
        }
        return str_replace($search, $replace, $sql);
    }
}
