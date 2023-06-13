<?php
namespace Madj2k\CoreExtended\Tests\Integration\Utility;

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
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Context\VisibilityAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Versioning\VersionState;

/**
 * QueryUtilityTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class QueryUtilityTest extends FunctionalTestCase
{

    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/QueryUtilityTest/Fixtures';


    /**
     * @const
     */
    const TEST_TABLE = 'test';


    /**
     * @var string[]
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/core_extended',
    ];

    /**
     * @var string[]
     */
    protected $coreExtensionsToLoad = [
        'seo'
    ];


    /**
     * Setup
     */
    protected function setUp(): void
    {
        parent::setUp();

    }

    //=============================================

    /**
     * @test
     */
    public function getSelectColumnsReturnsEmptyStringIfNoConfig()
    {

        /**
         * Scenario:
         *
         * Given a TCA-configuration for a table
         * Given in this configuration no columns are set
         * When the method is called
         * Then an array is returned
         * Then this array is empty
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['columns'] = [

        ];

        $result = QueryUtility::getSelectColumns(self::TEST_TABLE);

        self::assertIsArray($result);
        self::assertEmpty($result);

    }


    /**
     * @test
     */
    public function etSelectColumnsReturnsAllFieldsExceptTypeNone()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration there are five columns defined
         * Given in this configuration one of the columns is of type "none"
         * When the method is called
         * Then an array is returned
         * Then the array has the size of four
         * Then column of type "none" is not included
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['columns'] = [
            'test1' => [
                'config' => [
                    'type' => 'select',
                ],
            ],
            'test2' => [
                'config' => [
                    'type' => 'inline',
                ],
            ],
            'test3' => [
                'config' => [
                    'type' => 'text',
                ],
            ],
            'test4' => [
                'config' => [
                    'type' => 'none',
                ],
            ],
            'test5' => [
                'config' => [
                    'type' => 'check',
                ],
            ],
        ];

        $result = QueryUtility::getSelectColumns(self::TEST_TABLE);

        self::assertIsArray($result);
        self::assertCount(4, $result);
        self::assertNotContains('test4', $result);

    }

    //=============================================

    /**
     * @test
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getWhereClauseEnabledReturnsEmptyStringIfNoConfig()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration is no delete-field defined
         * Given in this configuration is no disable-field defined
         * Given in this configuration is no starttime-field defined
         * Given in this configuration is no endtime-field defined
         * When the method is called
         * Then a string is returned
         * Then this string is empty
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [

        ];

        $result = QueryUtility::getWhereClauseEnabled(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertEmpty($result);

    }


    /**
     * @todo currently commented because of issues with rkw_newsletter
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getWhereClauseEnabledReturnsEmptyStringIfAspectIncludeHidden()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration a delete-field is defined
         * Given in this configuration a disable-field is defined
         * Given in this configuration a starttime-field is defined
         * Given in this configuration a endtime-field is defined
         * Given the visibility-aspect has showHidden set
         * When the method is called
         * Then a string is returned
         * Then this string is empty
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [
            'delete' => 'deleted',
            'enablecolumns' => [
                'disabled' => 'hidden',
                'starttime' => 'starttime',
                'endtime' => 'endtime',
            ],
        ];

        /** @var \TYPO3\CMS\Core\Context\Context $context */
        $context = GeneralUtility::makeInstance(Context::class);
        $context->setAspect('visibility', GeneralUtility::makeInstance(VisibilityAspect::class, false, true, false));

        $result = QueryUtility::getWhereClauseEnabled(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertEmpty($result);

    }


    /**
     * @test
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getWhereClauseEnabledReturnsDeleteFieldInQuery()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration a delete-field is defined
         * When the method is called
         * Then this string starts with " AND"
         * Then a string is returned
         * Then this string is a where-clause which checks for this field
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [
            'delete' => 'deleted'
        ];

        $result = QueryUtility::getWhereClauseEnabled(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertStringStartsWith(' AND', $result);
        self::assertStringContainsString('`deleted` = 0', $result);

    }


    /**
     * @test
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getWhereClauseEnabledReturnsDisableFieldInQuery()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration a disabled-field is defined
         * When the method is called
         * Then a string is returned
         * Then this string starts with " AND"
         * Then this string is a where-clause which checks for this field
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [
            'enablecolumns' => [
                'disabled' => 'hidden',
            ],
        ];

        $result = QueryUtility::getWhereClauseEnabled(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertStringStartsWith(' AND', $result);
        self::assertStringContainsString('`hidden` = 0', $result);
    }


    /**
     * @test
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getWhereClauseEnabledReturnsStartTimeFieldInQuery()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration a starttime-field is defined
         * When the method is called
         * Then a string is returned
         * Then this string starts with " AND"
         * Then this string is a where-clause which checks for this field
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [
            'enablecolumns' => [
                'starttime' => 'starttime',
            ],
        ];

        $result = QueryUtility::getWhereClauseEnabled(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertStringStartsWith(' AND', $result);
        self::assertStringContainsString('`starttime` <= ', $result);

    }


    /**
     * @test
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getWhereClauseEnabledReturnsEndTimeFieldInQuery()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration a endtime-field is defined
         * When the method is called
         * Then a string is returned
         * Then this string starts with " AND"
         * Then this string is a where-clause which checks for this field
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [
            'enablecolumns' => [
                'endtime' => 'endtime',
            ],
        ];

        $result = QueryUtility::getWhereClauseEnabled(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertStringStartsWith(' AND', $result);
        self::assertStringContainsString('`endtime` = 0', $result);
        self::assertStringContainsString('`endtime` > ', $result);

    }


    /**
     * @test
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getWhereClauseEnabledReturnsAllEnableFieldsInQuery()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration a delete-field is defined
         * Given in this configuration a disable-field is defined
         * Given in this configuration a starttime-field is defined
         * Given in this configuration a endtime-field is defined
         * When the method is called
         * Then a string is returned
         * Then this string starts with " AND"
         * Then this string is a where-clause which checks for all defined fields
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [
            'delete' => 'deleted',
            'enablecolumns' => [
                'disabled' => 'hidden',
                'starttime' => 'starttime',
                'endtime' => 'endtime',
            ],
        ];

        $result = QueryUtility::getWhereClauseEnabled(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertStringStartsWith(' AND', $result);
        self::assertStringContainsString('`deleted` = 0', $result);
        self::assertStringContainsString('`hidden` = 0', $result);
        self::assertStringContainsString('`starttime` <= ', $result);
        self::assertStringContainsString('`endtime` = 0', $result);
        self::assertStringContainsString('`endtime` > ', $result);

    }

    //=============================================

    /**
     * @test
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getWhereClauseDeletedReturnsEmptyStringIfNoConfig()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration is no delete-field defined
         * When the method is called
         * Then a string is returned
         * Then this string is empty
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [

        ];

        $result = QueryUtility::getWhereClauseDeleted(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertEmpty($result);

    }


    /**
     * @todo currently commented because of issues with rkw_newsletter
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getWhereClauseDeletedReturnsEmptyStringIfAspectIncludeHidden()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration a delete-field is defined
         * When the method is called
         * Then a string is returned
         * Then this string is empty
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [
            'delete' => 'deleted',
        ];

        /** @var \TYPO3\CMS\Core\Context\Context $context */
        $context = GeneralUtility::makeInstance(Context::class);
        $context->setAspect('visibility', GeneralUtility::makeInstance(VisibilityAspect::class, false, true, false));

        $result = QueryUtility::getWhereClauseDeleted(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertEmpty($result);

    }


    /**
     * @test
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getWhereClauseDeletedReturnsDeleteFieldInQuery()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration a delete-field is defined
         * When the method is called
         * Then a string is returned
         * Then this string starts with " AND"
         * Then this string is a where-clause which checks for this field
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [
            'delete' => 'deleted'
        ];

        $result = QueryUtility::getWhereClauseDeleted(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertStringStartsWith(' AND', $result);
        self::assertStringContainsString('`deleted` = 0', $result);

    }

    //=============================================

    /**
     * @test
     */
    public function getWhereClauseLanguageReturnsEmptyStringIfNoConfig()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration is no language-field defined
         * When the method is called
         * Then a string is returned
         * Then this string is empty
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [

        ];

        $result = QueryUtility::getWhereClauseLanguage(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertEmpty($result);

    }


    /**
     * @test
     */
    public function getWhereClauseLanguageReturnsLanguageFieldInQuery()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration a delete-field is defined
         * When the method is called
         * Then a string is returned
         * Then this string starts with " AND"
         * Then this string is a where-clause which checks for this field with the given uid
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [
            'languageField' => 'sys_language_uid',
        ];

        $result = QueryUtility::getWhereClauseLanguage(self::TEST_TABLE, 1111);

        self::assertIsString($result);
        self::assertStringStartsWith(' AND', $result);
        self::assertStringContainsString('`sys_language_uid` = 1111', $result);

    }

    //=============================================

    /**
     * @test
     */
    public function getWhereClauseVersioningReturnsEmptyStringIfNoConfig()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration the versioning is not activated
         * When the method is called
         * Then a string is returned
         * Then this string is empty
         */

        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [

        ];

        $result = QueryUtility::getWhereClauseVersioning(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertEmpty($result);

    }


    /**
     * @test
     */
    public function getWhereClauseVersioningReturnsVersioningFieldInQuery()
    {

        /**
         * Scenario:
         *
         * Given TCA-configuration for a table
         * Given in this configuration the versioning is activated
         * When the method is called
         * Then a string is returned
         * Then this string starts with " AND"
         * Then this string is a where-clause which checks for the default version-state
         */
        $GLOBALS['TCA'][self::TEST_TABLE]['ctrl'] = [
            'versioningWS' => true
        ];

        $result = QueryUtility::getWhereClauseVersioning(self::TEST_TABLE);

        self::assertIsString($result);
        self::assertStringStartsWith(' AND', $result);
        self::assertStringContainsString('`t3ver_state` <= ' . VersionState::DEFAULT_STATE, $result);

    }

    //=============================================

    /**
     * @test
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function getTreeListReturnsListOfPages()
    {
        /**
         * Scenario:
         *
         * Given a complex list of pages
         * Given the treeList is not cached
         * When the method is called
         * Then a string is returned
         * Then this string is a comma-separated list of these pages
         */

        $numberOfPages = 30;
        $expectedList = $this->createRecursivePageStructure($numberOfPages);

        GeneralUtility::makeInstance(CacheManager::class)
            ->getCache('tx_coreextended_treelist')
            ->flush();

        self::assertEquals(
            $expectedList,
            QueryUtility::getTreeList(1)
        );

        $this->deleteRecursivePageStructure($numberOfPages);

    }


    /**
     * @test
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function getTreeListReturnsListOfPagesIsFasterWhenCached()
    {
        /**
         * Scenario:
         *
         * Given a complex list of pages
         * Given the treeList is not cached
         * Given the method has been called before
         * Given the treeList has been cached in that call
         * When the method is called again with identical params
         * Then a string is returned
         * Then this string is a comma-separated list of these pages
         * Then the strings of both calls are identical
         * Then the processing time of the second call is less than the processing time of the second call
         */
        $numberOfPages = 3000;
        $expectedList = $this->createRecursivePageStructure($numberOfPages);

        GeneralUtility::makeInstance(CacheManager::class)
            ->getCache('tx_coreextended_treelist')
            ->flush();

        $startime = microtime(true);
        $resultFirst = QueryUtility::getTreeList(1,9999, 0);
        $endtime = microtime(true);
        $processingTimeFirst = $endtime - $startime;

        self::assertEquals(
            $expectedList,
            $resultFirst
        );

        $startime = microtime(true);
        $resultSecond = QueryUtility::getTreeList(1,9999, 0);
        $endtime = microtime(true);
        $processingTimeSecond = $endtime - $startime;

        self::assertEquals(
            $resultFirst,
            $resultSecond
        );

        self::assertLessThan($processingTimeFirst, $processingTimeSecond);

        $this->deleteRecursivePageStructure($numberOfPages);

    }

    /**
     * @test
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function getTreeListReturnsUpdatedListOfPagesAfterChangeInPageTree()
    {
        /**
         * Scenario:
         *
         * Given a complex list of pages
         * Given the treeList is not cached
         * Given the method has been called before
         * Given the treeList has been cached in that call
         * Given a new page has been created in the pageTree in the meantime
         * When the method is called again with identical params
         * Then a string is returned
         * Then the strings of both calls are not identical
         * Then the string of the second call is a comma-separated list of these pages, including the new one
         */
        $numberOfPages = 30;
        $expectedList = $this->createRecursivePageStructure($numberOfPages);

        GeneralUtility::makeInstance(CacheManager::class)
            ->getCache('tx_coreextended_treelist')
            ->flush();

        $resultFirst = QueryUtility::getTreeList(1,9999, 0);
        self::assertEquals(
            $expectedList,
            $resultFirst
        );

        sleep(2);

        $this->createRecursivePageStructure(1, $numberOfPages);
        $resultSecond = QueryUtility::getTreeList(1,9999, 0);
        self::assertNotEquals(
            $resultFirst,
            $resultSecond
        );

        self::assertEquals(
            $expectedList . ',31',
            $resultSecond
        );

        $this->deleteRecursivePageStructure($numberOfPages);

    }

    /**
     * @test
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function getTreeListReturnsDifferentListOfPagesBasedOnParams()
    {
        /**
         * Scenario:
         *
         * Given a complex list of pages
         * Given the treeList is not cached
         * Given the method has been called before
         * When the method is called again with another begin-parameter
         * Then a string is returned
         * Then this string is a comma-separated list of these pages
         * Then the strings of both calls are not identical
         * Then the string of the second call begins at one level down
         */
        $numberOfPages = 30;
        $expectedList = $this->createRecursivePageStructure($numberOfPages);

        GeneralUtility::makeInstance(CacheManager::class)
            ->getCache('tx_coreextended_treelist')
            ->flush();

        $resultFirst = QueryUtility::getTreeList(1,9999, 0);
        self::assertEquals(
            $expectedList,
            $resultFirst
        );

        $resultSecond = QueryUtility::getTreeList(1,9999, 1);
        self::assertNotEquals(
            $resultFirst,
            $resultSecond
        );

        self::assertEquals(
            substr($expectedList, 2),
            $resultSecond
        );

        $this->deleteRecursivePageStructure($numberOfPages);
    }

    //=============================================

    /**
     * @param int $numberOfPages
     * @param int $offset
     * @return string
     */
    protected function createRecursivePageStructure (int $numberOfPages, int $offset = 0): string
    {
        if ($offset < 0) {
            $offset = 0;
        }

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');


        $expectedList = '';
        if ($offset == 0) {

            $expectedList = '1';
            $queryBuilder
                ->insert('pages')
                ->values([
                    'title' => 'title',
                    'uid' => $offset + 1,
                    'pid' => '0',
                    'is_siteroot' => '1',
                    'tstamp' => time(),
                    'crdate' => time(),
                ])
                ->execute();

            $offset++;
            $numberOfPages--;

            if ($numberOfPages <= 1) {
                return $expectedList;
            }
        }

        foreach (range(($offset + 1), ($offset + $numberOfPages)) as $uid) {

            $queryBuilder
                ->insert('pages')
                ->values([
                    'title' => 'title',
                    'uid' => $uid,
                    'pid' => $uid - 1,
                    'is_siteroot' => '0',
                    'tstamp' => time(),
                    'crdate' => time(),
                ])
                ->execute();

            $expectedList .= ','. $uid;
        }

        return trim($expectedList,',');
    }

    /**
     * @param int $numberOfPages
     * @return void
     */
    protected function deleteRecursivePageStructure(int $numberOfPages): void
    {

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');

        $queryBuilder->delete('pages')
            ->where('uid <= ' . $numberOfPages);
    }

    /**
     * TearDown
     */
    protected function tearDown(): void
    {
        parent::tearDown();


    }


}
