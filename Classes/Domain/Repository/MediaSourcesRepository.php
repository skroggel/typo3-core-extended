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

use Madj2k\CoreExtended\Utility\QueryUtility;
use Madj2k\CoreExtended\Utility\GeneralUtility as Common;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class MediaSourcesRepository
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MediaSourcesRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * Finds all used images on active pages that are from image databases and returns their source meta-data
     *
     * @param array $pagesUidList
     * @param bool $respectFields
     * @return array
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function findAllWithPublisher(array $pagesUidList = [], bool $respectFields = true): array
    {

        $fileTable = 'sys_file';
        $fileMetaTable = 'sys_file_metadata';
        $fileReferenceTable = 'sys_file_reference';
        $mediaSourcesTable = 'tx_coreextended_domain_model_mediasources';

        // Build where-query
        $whereQuery = array();
        if (is_array($pagesUidList)) {

            if ($respectFields) {
                foreach ($pagesUidList as $pid => $fieldList) {
                    foreach ($fieldList as $fieldName) {

                        if (strpos($fieldName, 'tt_content.') === 0) {
                            $whereQuery[] = '(tt_content.pid = ' . intval($pid) . '
                                AND ' . $fileReferenceTable . '.uid_foreign = tt_content.uid
                                AND ' . $fileReferenceTable . '.fieldname = "' . Common::camelize(addslashes(str_replace('tt_content.', '', $fieldName))) . '"
                                AND ' . $fileReferenceTable . '.tablenames = "tt_content"
                            )';

                        } else {
                            if (strpos($fieldName, 'pages.') === 0) {
                                $whereQuery[] = '(' . $fileReferenceTable . '.uid_foreign = ' . intval($pid) . '
                                AND ' . $fileReferenceTable . '.fieldname = "' . Common::camelize(addslashes(str_replace('pages.', '', $fieldName))) . '"
                                AND ' . $fileReferenceTable . '.tablenames = "pages"
                            )';
                            }
                        }

                    }
                }

            } else {
                $whereQuery[] = '(
                    (
                        ' . $fileReferenceTable . '.uid_foreign IN (' . implode(',', $pagesUidList) . ')
                         AND ' . $fileReferenceTable . '.tablenames = "pages"
                    )
                    OR (
                        tt_content.pid IN (' . implode(',', $pagesUidList) . ')
                        AND ' . $fileReferenceTable . '.uid_foreign = tt_content.uid
                        AND ' . $fileReferenceTable . '.tablenames = "tt_content"
                    )
                )';

            }

        }

        $statement = '
            SELECT DISTINCT
                ' . $mediaSourcesTable . '.name AS distributerName,
                ' . $mediaSourcesTable . '.url AS distributerUrl,
                ' . $fileMetaTable . '.tx_coreextended_publisher AS publisher,
                ' . $fileMetaTable . '.title as imageTitle,
                ' . $fileMetaTable . '.description as imageDescription,
                ' . $fileTable . '.name as fileName,
                GROUP_CONCAT(DISTINCT pages.uid, "###", pages.title SEPARATOR "|") AS pagesList

            FROM ' . $fileReferenceTable . '
            RIGHT JOIN (' . $fileMetaTable . ')

                ON (
                    ' . $fileMetaTable . '.file = ' . $fileReferenceTable . '.uid_local
                    ' . $this->getWhereClauseForEnabledFields($fileMetaTable) . '
                )

            RIGHT JOIN (' . $mediaSourcesTable . ')
                ON (
                    ' . $mediaSourcesTable . '.uid = ' . $fileMetaTable . '.tx_coreextended_source
                    ' . $this->getWhereClauseForEnabledFields($mediaSourcesTable) . '
                )
            RIGHT JOIN (' . $fileTable . ')

                ON (
                    ' . $fileTable . '.uid = ' . $fileMetaTable . '.file
                    ' . $this->getWhereClauseForEnabledFields($fileTable) . '
                )
            LEFT JOIN (tt_content)
                ON (
                    ' . $fileReferenceTable . '.tablenames = "tt_content"
                    AND ' . $fileReferenceTable . '.uid_foreign = tt_content.uid
                    ' . $this->getWhereClauseForEnabledFields("tt_content") . '

                )

            LEFT JOIN (pages)
                ON (
                    (
                        ' . $fileReferenceTable . '.tablenames = "pages"
                        AND  ' . $fileReferenceTable . '.uid_foreign = pages.uid
                    ) OR
                    (
                        ' . $fileReferenceTable . '.tablenames = "tt_content"
                        AND  tt_content.pid = pages.uid
                        ' . $this->getWhereClauseForEnabledFields("tt_content") . '
                    )
                    ' . $this->getWhereClauseForEnabledFields("pages") . '
                )

            WHERE
                ' . $mediaSourcesTable . '.internal != 1
                ' . $this->getWhereClauseForEnabledFields($fileReferenceTable) .
            ((is_array($whereQuery) && count($whereQuery) > 0) ? ' AND (' . implode(' OR ', $whereQuery) . ')' : '') . '

            GROUP BY ' . $fileReferenceTable . '.uid_local

            ORDER BY
                ' . $mediaSourcesTable . '.name ' . \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING . ',
                ' . $fileMetaTable . '.tx_coreextended_publisher ' . \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING . '
        ';

        $query = $this->createQuery();
        $query->statement($statement);

        return $query->execute(true);
    }


    /**
     * Finds source meta-data for given file uid
     *
     * @param int $uid
     * @return array
     */
    public function findByFileUid(int $uid): array
    {

        $fileMetaTable = 'sys_file_metadata';
        $mediaSourcesTable = 'tx_coreextended_domain_model_mediasources';

        $query = $this->createQuery();
        $query->statement('
            SELECT
                ' . $mediaSourcesTable . '.name AS distributerName,
                ' . $mediaSourcesTable . '.url AS distributerUrl,
                ' . $fileMetaTable . '.tx_coreextended_publisher AS publisher
            FROM
                ' . $fileMetaTable . ',
                ' . $mediaSourcesTable . '
            WHERE
                ' . $fileMetaTable . '.file = ' . intval($uid) . '
                AND ' . $mediaSourcesTable . '.internal != 1
                AND ' . $mediaSourcesTable . '.uid = ' . $fileMetaTable . '.tx_coreextended_source
        ');

        return $query->execute(true);
    }


    /**
     * get the WHERE clause for the enabled fields of this TCA table
     * depending on the context
     *
     * @param string $table
     * @return string the additional where clause, something like " AND deleted=0 AND hidden=0"
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     * @see \TYPO3\CMS\Core\Resource\AbstractRepository
     */
    protected function getWhereClauseForEnabledFields(string $table): string
    {
        $whereClause = QueryUtility::getWhereClauseEnabled($table);
        $whereClause .= QueryUtility::getWhereClauseDeleted($table);

        return $whereClause;
    }

    /**
     * Function to return the current TYPO3_MODE.
     * This function can be mocked in unit tests to be able to test frontend behaviour.
     *
     * @return string
     * @see \TYPO3\CMS\Core\Resource\AbstractRepository
     */
    protected function getEnvironmentMode(): string
    {
        return TYPO3_MODE;
    }
}
