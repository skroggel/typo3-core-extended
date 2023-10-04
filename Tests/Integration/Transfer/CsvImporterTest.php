<?php
namespace Madj2k\CoreExtended\Tests\Integration\Transfer;

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
 *
 */

use League\Csv\Reader;
use Madj2k\CoreExtended\Domain\Model\FrontendUser;
use Madj2k\CoreExtended\Transfer\CsvImporter;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * CsvImporterTest
 *
 * @author Steffen Krogel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CsvImporterTest extends FunctionalTestCase
{
    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/CsvImporterTest/Fixtures';


    /**
     * @var string[]
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/core_extended',
    ];


    /**
     * @var string[]
     */
    protected $coreExtensionsToLoad = [ ];


    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager|null
     */
    private ?ObjectManager $objectManager = null;


    /**
     * @var \Madj2k\CoreExtended\Transfer\CsvImporter|null
     */
    private ?CsvImporter $fixture = null;


    /**
     * Setup
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Global.xml');

        $this->setUpFrontendRootPage(
            1,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.typoscript',
                'EXT:core_extended/Configuration/TypoScript/constants.typoscript',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
            ],
            ['example.com' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

         // simulate a link in TCA
        $GLOBALS['TCA']['fe_users']['columns']['www']['config']['renderType'] = 'inputLink';
    }

    /**


*/


    #==============================================================================
    /**
     * @test
     * @throws \Exception
     */
    public function constructsThrowsExceptionIfTcaConfigMissing()
    {
        /**
         * Scenario:
         *
         * Given an existing CSV-file
         * Given a non-existing table without TCA-configuration
         * When the method is called with these as parameters
         * Then an exception-instance of \Madj2k\CoreExtended\Exception is thrown
         * Then the exception-code is 1695272455
         */
        self::expectException(\Madj2k\CoreExtended\Exception::class);
        self::expectExceptionCode(1695272455);

        $this->initFixtureFromFile(
            'non_existing_table'
        );
    }

    #==============================================================================


    /**
     * @test
     * @throws \Exception
     */
    public function readCsvLoadsDataFromFile()
    {
        /**
         * Scenario:
         *
         * Given an existing CSV-file
         * Given an existing table-name with TCA-configuration
         * Given the class is instantiated with the given table-name
         * When the method is called with the CSV-file
         * Then getHeader returns a non-empty array
         * Then getRecords returns a non-empty array
         */
        /** \Madj2k\CoreExtended\Transfer\CsvImporter $fixture */
        $this->fixture = $this->objectManager->get(
            CsvImporter::class,
            'fe_users'
        );

        $this->fixture->readCsv(self::FIXTURE_PATH . '/Files/TestData.csv');

        self::assertIsArray($this->fixture->getHeader());
        self::assertNotEmpty($this->fixture->getHeader());

        self::assertIsArray($this->fixture->getRecords());
        self::assertNotEmpty($this->fixture->getRecords());
    }


    /**
     * @test
     * @throws \Exception
     */
    public function readCsvLoadsReaderFromString()
    {
        /**
         * Scenario:
         *
         * Given an existing CSV-string
         * Given an existing table-name with TCA-configuration
         * Given the class is instantiated with the given table-name
         * When the method is called with the CSV-string
         * Then getHeader returns a non-empty array
         * Then getRecords returns a non-empty array
         */
        /** \Madj2k\CoreExtended\Transfer\CsvImporter $fixture */
        $this->fixture = $this->objectManager->get(
            CsvImporter::class,
            'fe_users'
        );

        $this->fixture->readCsv(file_get_contents(self::FIXTURE_PATH . '/Files/TestData.csv'));

        self::assertIsArray($this->fixture->getHeader());
        self::assertNotEmpty($this->fixture->getHeader());

        self::assertIsArray($this->fixture->getRecords());
        self::assertNotEmpty($this->fixture->getRecords());
    }


    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function getImportableColumnsReturnsAllowedColumnsOnly()
    {
        /**
         * Scenario:
         *
         * When the method is called
         * Then an array is returned
         * Then this array contains all importable columns
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check10.php');

        $result = $this->fixture->getImportableColumns('fe_users');

        self::assertEquals($expected, $result);
        self::assertIsArray($result);
        self::assertCount(count($expected), $result);
        self::assertEquals($expected, $result);

    }

    /**
     * @test
     * @throws \Exception
     */
    public function getImportableColumnsReturnsAllowedColumnsWithoutExcludeColumns()
    {
        /**
         * Scenario:
         *
         * Given an array of exclude fields which are part of the TCA
         * Given setExcludeFields has been called
         * When the method is called
         * Then an array is returned
         * Then this array contains all importable columns
         * Then the exclude-fields are not returned
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check240.php');
        $this->fixture->setExcludeColumns(
            [
                'fe_users' => [
                    'hidden', 'deleted', 'tstamp', 'crdate', 'tx_extbase_type', 'TSconfig', 'first_name', 'last_name'
                ]
            ],
        );

        $result = $this->fixture->getImportableColumns('fe_users');

        self::assertEquals($expected, $result);
        self::assertIsArray($result);
        self::assertCount(count($expected), $result);
        self::assertEquals($expected, $result);

    }

    /**
     * @test
     * @throws \Exception
     */
    public function getImportableColumnsReturnsAllowedColumnsWithIncludeColumns()
    {
        /**
         * Scenario:
         *
         * Given an array of include fields that are not part of the TCA
         * Given setIncludeFields has been called
         * When the method is called
         * Then an array is returned
         * Then this array contains all importable columns
         * Then the include-fields are included
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check250.php');
        $this->fixture->setIncludeColumns(
            [
                'fe_users' => [
                    'pid', 'fuck', 'off'
                ]
            ],
        );

        $result = $this->fixture->getImportableColumns('fe_users');

        self::assertEquals($expected, $result);
        self::assertIsArray($result);
        self::assertCount(count($expected), $result);
        self::assertEquals($expected, $result);

    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function getTableRelationsReturnsArrayOfColumnsWithTableRelations()
    {
        /**
         * Scenario:
         *
         * When the method is called
         * Then an array is returned
         * Then this array contains columns that have a relation to another table
         * Then this array has the column-names as key
         * Then this array-keys consist only of keys starting with "foreign_" and the "type"
         * Then this array has the corresponding TCA-configuration as value
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check20.php');

        $result = $this->fixture->getTableRelations('fe_users');

        self::assertIsArray($result);
        self::assertEquals(array_keys($expected), array_keys($result));
        self::assertEquals(array_values($expected), array_values($result));

    }


    /**
     * @test
     * @throws \Exception
     */
    public function getTableRelationsRespectsAllowedRelationTables()
    {
        /**
         * Scenario:
         *
         * Given a list with allowed relationTables
         * Given setAllowedRelationTables has been called
         * When the method is called
         * Then an array is returned
         * Then this array contains columns that have a relation to another table
         * Then only those tables that have been allowed via setAllowedRelationTables before are included
         * Then this array has the column-names as key
         * Then this array-keys consist only of keys starting with "foreign_" and the "type"
         * Then this array has the corresponding TCA-configuration as value
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check30.php');

        $this->fixture->setAllowedRelationTables(['fe_users' => ['fe_groups']]);
        $result = $this->fixture->getTableRelations('fe_users');

        self::assertIsArray($result);
        self::assertEquals(array_keys($expected), array_keys($result));
        self::assertEquals(array_values($expected), array_values($result));

    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function applyAdditionalDataWritesIntoData()
    {
        /**
         * Scenario:
         *
         * Given a defined array of key-value-pairs of the size 2
         * Given one key refers to a column that is already included in the data
         * Given one key refers to a column that is not included in the data
         * Given setAdditionalData is called with that array as parameter
         * Given applyAdditionalData is called
         * When the getDataRaw is called
         * Then an array is returned
         * Then this array contains three record-arrays
         * Then each record-array contains the columns it had before applyAdditionalData has been called
         * Then each record-array contains the column of the additionalData that was not included in the data
         * Then in each record-array the values of additionalData override existing values in the existing columns
         * Then the header is updated
         */
        $this->initFixtureFromFile();

        $additionalData = [
            'zip' => 'New Value!',
            'no_included' => 'New Field!',
            'usergroup.title' => 'Title Override!'
        ];

        $headerRawBefore = $this->fixture->getHeader();

        $this->fixture->setAdditionalData($additionalData);
        $this->fixture->applyAdditionalData();
        $result = $this->fixture->getRecords();
        $headerRawAfter = $this->fixture->getHeader();

        self::assertIsArray($result);
        self::assertCount(3, $result);

        foreach ($headerRawBefore as $rawColumn) {
            self::assertArrayHasKey($rawColumn, $result[0]);
            self::assertArrayHasKey($rawColumn, $result[1]);
            self::assertArrayHasKey($rawColumn, $result[2]);
        }

        foreach ($additionalData as $addColumn => $addValue) {
            self::assertArrayHasKey($addColumn, $result[0]);
            self::assertArrayHasKey($addColumn, $result[1]);
            self::assertArrayHasKey($addColumn, $result[2]);

            self::assertEquals($addValue, $result[0][$addColumn]);
            self::assertEquals($addValue, $result[1][$addColumn]);
            self::assertEquals($addValue, $result[2][$addColumn]);
        }

        self::assertEquals($headerRawAfter, array_keys($result[0]));

    }

    #==============================================================================


    /**
     * @test
     * @throws \Exception
     */
    public function applyDefaultValuesSetsValuesOfEmptyOrZeroColumns()
    {
        /**
         * Scenario:
         *
         * Given a defined array of key-value-pairs of the size 3
         * Given one key refers to a column that is already included in the data and non-empty
         * Given one key refers to a column that is already included in the data and empty
         * Given one key refers to a column that is not included in the data
         * Given setDefaultValues is called with that array as parameter
         * Given applyDefaultValues is called
         * When the getDataRaw is called
         * Then an array is returned
         * Then this array contains three record-arrays
         * Then each record-array contains the columns it had before applyDefaultValues has been called
         * Then each record-array contains the column of the defaultValues that was not included in the data
         * Then in each record-array only the empty values of data are overridden by the defaultValues
         * Then the header is updated
         */
        $this->initFixtureFromFile();

        $defaultValues = [
            'zip' => 'Non-Empty Value',
            'hidden' => 1, // empty value
            'no_included' => 'New Field!',
            'usergroup.title' => 'Default Value!'
        ];

        $headerRawBefore = $this->fixture->getHeader();

        $this->fixture->setDefaultValues($defaultValues);
        $this->fixture->applyDefaultValues();

        $result = $this->fixture->getRecords();
        $headerRawAfter = $this->fixture->getHeader();

        self::assertIsArray($result);
        self::assertCount(3, $result);

        foreach ($headerRawBefore as $rawColumn) {
            self::assertArrayHasKey($rawColumn, $result[0]);
            self::assertArrayHasKey($rawColumn, $result[1]);
            self::assertArrayHasKey($rawColumn, $result[2]);
        }

        self::assertArrayHasKey('no_included', $result[0]);
        self::assertArrayHasKey('no_included', $result[1]);
        self::assertArrayHasKey('no_included', $result[2]);

        self::assertEquals('1', $result[0]['hidden']);
        self::assertEquals('1', $result[1]['hidden']);
        self::assertEquals('1', $result[2]['hidden']);

        self::assertNotEquals('Non-Empty Value', $result[0]['zip']);
        self::assertNotEquals('Non-Empty Value', $result[1]['zip']);
        self::assertNotEquals('Non-Empty Value', $result[2]['zip']);

        self::assertNotEquals('NDefault Value!', $result[0]['usergroup.title']);
        self::assertEquals('Default Value!', $result[1]['usergroup.title']);
        self::assertNotEquals('Default Value!', $result[2]['usergroup.title']);

        self::assertEquals($headerRawAfter, array_keys($result[0]));

    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function getHeaderReturnsAllColumnNames()
    {
        /**
         * Scenario:
         *
         * When the method is called
         * Then an array is returned
         * Then this array contains all column-names from the CSV
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check40.php');

        $result = $this->fixture->getHeader();
        self::assertEquals($expected, $result);

        self::assertIsArray($result);
        self::assertCount(count($expected), $result);
        self::assertEquals($expected, $result);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function setHeaderSetsHeader()
    {
        /**
         * Scenario:
         *
         * Given a defined numeric array with the size of three
         * Given setHeader has been called with the array as param
         * When the getHeader is called
         * Then an array is returned
         * Then this array has the size of three
         * Then this array contains the given array
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check50.php');

        $this->fixture->setHeader($expected);
        $result = $this->fixture->getHeader();

        self::assertIsArray($result);
        self::assertCount(count($expected), $result);
        self::assertEquals($expected, $result);
    }


    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function getRecordsReturnsAllRecords()
    {
        /**
         * Scenario:
         *
         * When the method is called
         * Then an array is returned
         * Then this array contains all records
         * Then the header is not returned
         * Then each array-row contains all column-names of the CSV
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check60.php');

        $result = $this->fixture->getRecords();

        self::assertIsArray($result);
        self::assertCount(3, $result);

        self::assertCount(count($expected), $result[0]);
        self::assertEquals($expected, array_keys($result[0]));

        self::assertCount(count($expected), $result[1]);
        self::assertEquals($expected, array_keys($result[1]));

        self::assertCount(count($expected), $result[2]);
        self::assertEquals($expected, array_keys($result[2]));


    }


    /**
     * @test
     * @throws \Exception
     */
    public function getRecordsTransformsCoding()
    {
        /**
         * Scenario:
         *
         * When the method is called
         * Then an array is returned
         * Then this array contains all records
         * Then the german umlauts are coded correctly
         */
        $this->initFixtureFromFile();

        $result = $this->fixture->getRecords();

        self::assertIsArray($result);
        self::assertCount(3, $result);

        self::assertEquals('BÜNDNIS 90/DIE GRÜNEN', $result[1]['company']);

    }

    #==============================================================================


    /**
     * @test
     * @throws \Exception
     */
    public function setRecordsSetsRecordsRaw()
    {
        /**
         * Scenario:
         *
         * Given a defined recursive array with the size of two on level 1
         * Given that array has a size of three on level 2
         * Given setRecords has been called with the array as param
         * When the getRecords is called
         * Then an array is returned
         * Then this array is the given recursive array
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check70.php');

        $this->fixture->setRecords($expected);
        $result = $this->fixture->getRecords();

        self::assertIsArray($result);
        self::assertCount(count($expected), $result);
        self::assertEquals($expected, $result);
    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function filterRecordByRootColumnReturnsOnlyColumnsWithoutPrefix()
    {
        /**
         * Scenario:
         *
         * Given a record as associative array
         * Given that array contains keys with and without prefix
         * Given setRootColumns is not called
         * When the method is called without the prefix-parameter
         * Then an associative array is returned
         * Then this associative array contains only the columns that have no prefix
         * Then this associative array contains the relevant values
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check80.php');

        $recordsRaw = $this->fixture->getRecords();
        $result = $this->fixture->filterRecordByRootColumn($recordsRaw[0]);

        self::assertIsArray($result);
        self::assertEquals($expected, $result);

    }


    /**
     * @test
     * @throws \Exception
     */
    public function filterRecordByRootColumnReturnsOnlyColumnsWithPrefix()
    {
        /**
         * Scenario:
         *
         * Given a record as associative array
         * Given that array contains keys with and without prefix
         * Given setRootColumns is not called
         * When the method is called the prefix-parameter
         * Then an associative array is returned
         * Then this associative array contains only the columns that match the given filter-parameter
         * Then the given prefix is removed from the column-names of the returned associative array
         * Then this associative array contains the relevant values
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check90.php');

        $recordsRaw = $this->fixture->getRecords();
        $result = $this->fixture->filterRecordByRootColumn($recordsRaw[0], 'usergroup');

        self::assertIsArray($result);
        self::assertEquals($expected, $result);

    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function filterRecordByImportableColumnsIgnoresDefaultExcludedColumns()
    {
        /**
         * Scenario:
         *
         * Given a record as associative array
         * Given setExcludeColumns has not been called before
         * When the method is called
         * Then an associative array is returned
         * Then this associative array contains only the columns that match the exclude-filter
         * Then this associative array contains the relevant values
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check100.php');

        $recordsRaw = $this->fixture->getRecords();
        $result = $this->fixture->filterRecordByImportableColumns($recordsRaw[0]);

        self::assertIsArray($result);
        self::assertEquals($expected, $result);

    }


    /**
     * @test
     * @throws \Exception
     */
    public function filterRecordByImportableColumnsIgnoresDefinedExcludedColumns()
    {
        /**
         * Scenario:
         *
         * Given a record as associative array
         * Given an array with columns to exclude
         * Given the record contains columns that are to exclude
         * Given setExcludeColumns has been called before with that array of exclude-columns
         * When the method is called
         * Then an associative array is returned
         * Then this associative array contains only the columns that match the exclude-filter
         * Then this associative array contains the relevant values
         */
        $this->initFixtureFromFile();
        $expected = include(self::FIXTURE_PATH . '/Expected/Check110.php');

        $excludeColumns = [
            'fe_users' => [
                'hidden', 'deleted', 'tstamp', 'crdate', 'tx_extbase_type', 'TSconfig',
                'lastlogin', 'city', 'zip'
            ]
        ];

        $this->fixture->setExcludeColumns($excludeColumns);
        $recordsRaw = $this->fixture->getRecords();

        $result = $this->fixture->filterRecordByImportableColumns($recordsRaw[0]);

        self::assertIsArray($result);
        self::assertEquals($expected, $result);
    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function persistRecordDoesInsert()
    {
        /**
         * Scenario:
         *
         * Given a record as associative array
         * Given the uid of the record does not exist in the database
         * When the method is called with the array as parameter
         * Then the uid of the record is returned
         * Then the data is inserted the database
         * Then the data is sanitized
         */
        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestDataSingle.csv');
        $expected = include(self::FIXTURE_PATH . '/Expected/Check190.php');
        $recordsRaw = $this->fixture->getRecords();

        $result = $this->fixture->persistRecord($recordsRaw[0]);
        self::assertEquals(1, $result);

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');

        $statement = $queryBuilder->select('*')
            ->from('fe_users')
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expected[$cnt], $row);
            $cnt++;
        }
    }


    /**
     * @test
     * @throws \Exception
     */
    public function persistRecordDoesUpdate()
    {
        /**
         * Scenario:
         *
         * Given a record as associative array
         * Given the uid of the record does exist in the database
         * When the method is called with the array as parameter
         * Then the uid of the record is returned via referenced parameter
         * Then the data is updated in the database
         * Then the data is sanitized
         */
        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestDataSingle.csv');
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check200.xml');
        $expected = include(self::FIXTURE_PATH . '/Expected/Check200.php');
        $recordsRaw = $this->fixture->getRecords();

        $result = $this->fixture->persistRecord($recordsRaw[0]);
        self::assertEquals(100, $result);

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');

        $statement = $queryBuilder->select('*')
            ->from('fe_users')
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expected[$cnt], $row);
            $cnt++;
        }
    }


    /**
     * @test
     * @throws \Exception
     */
    public function persistRecordFindsMatchingAndDoesUpdate()
    {
        /**
         * Scenario:
         *
         * Given a record as associative array
         * Given the uid of the record does not exist in the database
         * Given setUniqueSelectColumns is called with two columns
         * Given for the given record there is a record in the database that matches when using the uniqueSelectColumns
         * When the method is called with the array as parameter
         * Then the uid of the record in the database that matches when using the uniqueSelectColumns is returned
         * Then the data is updated in the database
         */
        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestDataSingle.csv');
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check210.xml');
        $expected = include(self::FIXTURE_PATH . '/Expected/Check210.php');
        $recordsRaw = $this->fixture->getRecords();

        $this->fixture->setUniqueSelectColumns(['fe_users' => ['city', 'zip']]);

        $result = $this->fixture->persistRecord($recordsRaw[0]);
        self::assertEquals(3, $result);

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');

        $statement = $queryBuilder->select('*')
            ->from('fe_users')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter(3, \PDO::PARAM_INT)
                )
            )
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expected[$cnt], $row);
            $cnt++;
        }
    }

    /**
     * @test
     * @throws \Exception
     */
    public function persistRecordRespectsAllowedTables()
    {
        /**
         * Scenario:
         *
         * Given a record as associative array
         * Given the uid of the record does not exist in the database
         * Given the current table is not an allowed table
         * Given setAllowedTables has been called
         * When the method is called with the array as parameter
         * Then the value 0 is returned
         */
        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestDataSingle.csv');
        $this->fixture->setAllowedTables([]);
        $recordsRaw = $this->fixture->getRecords();

        $result = $this->fixture->persistRecord( $recordsRaw[0]);

        self::assertEquals(0, $result);

    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function persistTableRelationsSetsInlineRelationWithCommaList()
    {
        /**
         * Scenario:
         *
         * Given a persisted object A with a defined 1:n-relation in a column of table W
         * Given two objects 1 and 2 of table U are to be related to that column of table W
         * When the method is called
         * Then true is returned
         * Then the objects 1 and 2 are inserted in the column of table W as comma-separated list
         */
        $this->initFixtureFromFile();
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check150.xml');

        $result = $this->fixture->persistTableRelations(1, 'usergroup', [1,2]);
        self::assertTrue($result);

        $databaseResultParent = $this->getDatabaseConnection()->selectSingleRow('usergroup', 'fe_users','uid = 1');
        self::assertEquals('1,2', $databaseResultParent['usergroup']);

    }


    /**
     * @test
     * @throws \Exception
     */
    public function persistTableRelationsAddInlineRelationWithCommaList()
    {
        /**
         * Scenario:
         *
         * Given a persisted object A with a defined 1:n-relation in a column of table W
         * Given two objects 1 and 2 of table U are to be related to that column of table W
         * Given an existing relation to objects 2, 3 and 4 in column 1
         * When the method is called
         * Then true is returned
         * Then the object 1 is added to the column of table W as comma-separated list
         */
        $this->initFixtureFromFile();
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check170.xml');

        $result = $this->fixture->persistTableRelations(1, 'usergroup', [1,2]);
        self::assertTrue($result);

        $databaseResultParent = $this->getDatabaseConnection()->selectSingleRow('usergroup', 'fe_users','uid = 1');
        self::assertEquals('2,3,4,1', $databaseResultParent['usergroup']);

    }


    /**
     * @test
     * @throws \Exception
     */
    public function persistTableRelationsOverridesInlineRelationWithCommaList()
    {
        /**
         * Scenario:
         *
         * Given a persisted object A with a defined 1:n-relation in a column of table W
         * Given the object 1 table U ist to be related to that column of table W
         * Given an existing relation to object 2 in column 1
         * Given the column has maxitems=1 set in TCA
         * When the method is called
         * Then true is returned
         * Then the object 1 is set to the column of table W as comma-separated list
         */
        $this->initFixtureFromFile();
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check260.xml');

        // Fake TCA
        $GLOBALS['TCA']['fe_users']['columns']['usergroup']['config']['maxitems'] = 1;

        $result = $this->fixture->persistTableRelations(1, 'usergroup', [1]);
        self::assertTrue($result);

        $databaseResultParent = $this->getDatabaseConnection()->selectSingleRow('usergroup', 'fe_users','uid = 1');
        self::assertEquals('1', $databaseResultParent['usergroup']);

    }

    /**
     * @test
     * @throws \Exception
     */
    public function persistTableRelationsRespectsAllowedRelationTables()
    {
        /**
         * Scenario:
         *
         * Given table U is not an allowed table for relation-table W
         * Given setAllowedRelationTables has been called
         * Given a persisted object A with a defined 1:n-relation in a column of table W
         * Given two objects 1 and 2 of table U are to be related to that column of table W
         * When the method is called
         * Then false is returned
         */
        $this->initFixtureFromFile();
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check150.xml');

        $this->fixture->setAllowedRelationTables(['fe_users' => []]);
        $result = $this->fixture->persistTableRelations(1, 'usergroup', [1,2]);
        self::assertFalse($result);

    }

#==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function searchExistingRecordReturnsZeroIfNoUniqueSelectColumns()
    {
        /**
         * Scenario:
         *
         * Given a record as associative array
         * Given the uid of the record does not exist in the database
         * Given setUniqueSelectColumns is not called
         * Given for the given record there is a record in the database that matches when using the uniqueSelectColumns
         * When the method is called with the record as parameter
         * Then zero is returned
         */

        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestDataSingle.csv');
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check220.xml');
        $recordsRaw = $this->fixture->getRecords();

        $result = $this->fixture->searchExistingRecord($recordsRaw[0]);

        self::assertIsInt($result);
        self::assertEquals(0, $result);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function searchExistingRecordReturnsZeroIfNoMatch()
    {
        /**
         * Scenario:
         *
         * Given a record as associative array
         * Given the uid of the record does not exist in the database
         * Given setUniqueSelectColumns is called with two columns
         * Given for the given record there is no record in the database that matches when using the uniqueSelectColumns
         * When the method is called with the record as parameter
         * Then an integer is returned
         * Then the integer has the value zero
         */

        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestDataSingle.csv');
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check220.xml');
        $recordsRaw = $this->fixture->getRecords();

        $this->fixture->setUniqueSelectColumns(['fe_users' => ['address', 'email']]);
        $result = $this->fixture->searchExistingRecord($recordsRaw[0]);

        self::assertIsInt($result);
        self::assertEquals(0, $result);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function searchExistingRecordFindsMatching()
    {
        /**
         * Scenario:
         *
         * Given a record as associative array
         * Given the uid of the record does not exist in the database
         * Given setUniqueSelectColumns is called with two columns
         * Given for the given record there is a record in the database that matches when using the uniqueSelectColumns
         * When the method is called with the record as parameter
         * Then an integer is returned
         * Then the integer equals the uid of the record in the database that matches when using the uniqueSelectColumns
         */

        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestDataSingle.csv');
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check220.xml');
        $recordsRaw = $this->fixture->getRecords();

        $this->fixture->setUniqueSelectColumns(['fe_users' => ['city', 'zip']]);
        $result = $this->fixture->searchExistingRecord($recordsRaw[0]);

        self::assertIsInt($result);
        self::assertEquals(3, $result);

    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function doesRecordExistReturnsFalse()
    {
        /**
         * Scenario:
         *
         * Given a uid of a record that does not exist in the database
         * When the method is called with the uid of that record
         * Then false is returned
         */
        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestDataSingle.csv');

        $result = $this->fixture->doesRecordExist(101);
        self::assertFalse($result);

    }

    /**
     * @test
     * @throws \Exception
     */
    public function doesRecordExistReturnsTrue()
    {
        /**
         * Scenario:
         *
         * Given an existing record in the database
         * When the method is called with the uid of that record
         * Then true is returned
         */
        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestDataSingle.csv');

        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check240.xml');

        $result = $this->fixture->doesRecordExist(101);
        self::assertTrue($result);

    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function importInsertsDataToDatabase()
    {
        /**
         * Scenario:
         *
         * Given three rows of CSV data for table X without sub-references
         * When the method is called
         * Then an array is returned
         * Then the returned array contains three created uids of table X as keys
         * Then each key contains an empty array, because there are no sub-references
         * Then the data to import into table X is stored with the importable fields only
         */
        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestDataSingle.csv');
        $expected = include(self::FIXTURE_PATH . '/Expected/Check120.php');
        $expectedReturn = [
            1 => [],
            2 => [],
            3 => []
        ];

        $result = $this->fixture->import();

        self::assertIsArray($result);
        self::assertEquals($expectedReturn, $result);

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');

        $statement = $queryBuilder->select('*')
            ->from('fe_users')
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expected[$cnt], $row);
            $cnt++;
        }
    }


    /**
     * @test
     * @throws \Exception
     */
    public function importUpdatesDataInDatabase()
    {
        /**
         * Scenario:
         *
         * Given three rows of CSV data for table X without sub-references
         * Given there is a column uid set in the CSV data
         * Given two of the three uids already exist
         * When the method is called
         * Then an array is returned
         * Then the returned array contains one created uid and two existing uids of table X as keys
         * Then each key contains an empty array, because there are no sub-references
         * Then table X has been updated on two rows with the importable fields only
         * Then table X has an insert of one row with the importable fields only
         */
        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestDataSingle.csv');
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check130.xml');

        $expected = include(self::FIXTURE_PATH . '/Expected/Check130.php');
        $expectedReturn = [
            103 => [], // insert with auto-increment
            101 => [],
            102 => []
        ];

        $result = $this->fixture->import();

        self::assertIsArray($result);
        self::assertEquals($expectedReturn, $result);

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');

        $statement = $queryBuilder->select('*')
            ->from('fe_users')
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expected[$cnt], $row);
            $cnt++;
        }
    }


    /**
     * @test
     * @throws \Exception
     */
    public function importInsertsRecursivelyAndSetsRelations()
    {
        /**
         * Scenario:
         *
         * Given three rows of CSV-data for table X with sub-references of one field W to table Y1
         * Given that CSV data has sub-references (W.*) from table Y1 to table Y2
         * When the method is called
         * Then an array is returned
         * Then the returned array contains the created uids of table X as keys
         * Then each of these keys contains an array, with the sub-references and sub-subreferences respectively
         * Then the data to import into table X is stored with the importable fields only
         * Then the data to import into table Y1 is stored with the importable fields only
         * Then the data to import into table Y2 is stored with the importable fields only
         * Then the relations between the records in the tables are stored in the database
         */
        $this->initFixtureFromFile();
        $expectedFeUsers = include(self::FIXTURE_PATH . '/Expected/Check140.php');
        $expectedFeGroups = include(self::FIXTURE_PATH . '/Expected/Check141.php');

        $expectedReturn = [
            1 => [
                'usergroup' => [
                    1 => [
                        'subgroup' => [
                            2 => []
                        ]
                    ]
                ]
            ],
            2 => [
                'usergroup' => [
                    3 => [
                        'subgroup' => [
                             4 => []
                        ]
                    ]
                ]
            ],
            3 => [
                'usergroup' => [
                    5 => [
                        'subgroup' => [
                            6 => []
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->fixture->import();

        self::assertIsArray($result);
        self::assertEquals($expectedReturn, $result);

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');

        $statement = $queryBuilder->select('*')
            ->from('fe_users')
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expectedFeUsers[$cnt], $row);
            $cnt++;
        }

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_groups');

        $statement = $queryBuilder->select('*')
            ->from('fe_groups')
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expectedFeGroups[$cnt], $row);
            $cnt++;
        }
    }


    /**
     * @test
     * @throws \Exception
     */
    public function importUpdatesDataRecursivelyAndSetsRelations()
    {
        /**
         * Scenario:
         *
         * Given three rows of CSV-data for table X with sub-references of one field W to table Y1
         * Given that the sub-references are based on uids (W.uid)
         * Given first two of the three uids already exist
         * When the method is called
         * Then an array is returned
         * Then the returned array contains one newly created uid of table X as keys
         * Then the returned array contains two updated uids of table X as keys
         * Then each of these keys contains an array, with the sub-references and sub-subreferences respectively
         * Then the data to import into table X is stored with the importable fields only
         * Then the data to import into table Y1 is stored with the importable fields only
         * Then the relations between the records in the tables are stored in the database
         */
        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestData2.csv');
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check160.xml');

        $expectedFeUsers = include(self::FIXTURE_PATH . '/Expected/Check160.php');
        $expectedFeGroups = include(self::FIXTURE_PATH . '/Expected/Check161.php');
        $expectedReturn = [
            103 => [ // insert with auto-increment
                'usergroup' => [
                    50 => []
                ]
            ],
            101 => [
                'usergroup' => [
                    51 => []
                ]
            ],
            102 => [
                'usergroup' => [
                    52 => [] // insert with auto-increment
                ]
            ]
        ];

        $result = $this->fixture->import();

        self::assertIsArray($result);
        self::assertEquals($expectedReturn, $result);

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');

        $statement = $queryBuilder->select('*')
            ->from('fe_users')
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expectedFeUsers[$cnt], $row);
            $cnt++;
        }

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_groups');

        $statement = $queryBuilder->select('*')
            ->from('fe_groups')
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expectedFeGroups[$cnt], $row);
            $cnt++;
        }
    }

    /**
     * @test
     * @throws \Exception
     */
    public function importSetsRelationsOnly()
    {
        /**
         * Scenario:
         *
         * Given three rows of CSV-data for table X with sub-references of one field W to table Y1
         * Given that the sub-references are based on uids (W.uid)
         * Given first two of the three uids already exist
         * Given the table Y1 is not an allowed table
         * Given setAllowedTables has been called
         * When the method is called
         * Then an array is returned
         * Then the returned array contains one newly created uid of table X as keys
         * Then the returned array contains two updated uids of table X as keys
         * Then each key contains an empty array, because the sub-references where ignored
         * Then the data to import into table X is stored with the importable fields only
         * Then the data for table Y1 is not stored
         * Then the relations between the records in the tables are stored in the database
         * Then the relation to a non-existing record in table Y1 is not set
         */
        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestData2.csv');
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check230.xml');

        $this->fixture->setAllowedTables(['fe_users']);

        $expectedFeUsers = include(self::FIXTURE_PATH . '/Expected/Check230.php');
        $expectedFeGroups = include(self::FIXTURE_PATH . '/Expected/Check231.php');
        $expectedReturn = [
            103 => [ // insert with auto-increment
                'usergroup' => [
                    50 => []
                ]
            ],
            101 => [
                'usergroup' => [
                    51 => []
                ]
            ],
            102 => [] // no insert here
        ];

        $result = $this->fixture->import();

        self::assertIsArray($result);
        self::assertEquals($expectedReturn, $result);

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');

        $statement = $queryBuilder->select('*')
            ->from('fe_users')
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expectedFeUsers[$cnt], $row);
            $cnt++;
        }

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_groups');

        $statement = $queryBuilder->select('*')
            ->from('fe_groups')
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expectedFeGroups[$cnt], $row);
            $cnt++;
        }
    }


    /**
     * @test
     * @throws \Exception
     */
    public function importInsertsWithKeyword()
    {
        /**
         * Scenario:
         *
         * Given three rows of CSV-data for table X with sub-references of one field W to table Y1
         * Given that the sub-references are based on uids (W.uid)
         * Given all of the sub-references already exist as uids
         * Given the first rows of CSV-data contains a non-existing uid
         * Given the last to rows of CSV-data use "LAST" as keyword for the uid
         * Given the last to rows of CSV-data also contain additional data
         * When the method is called
         * Then an array is returned
         * Then the returned array contains one newly created uid of table X as key
         * Then the key contains an array, with the sub-references
         * Then the data to import into table X is stored with the importable fields only
         * Then the data to import into table Y1 is stored with the importable fields only
         * Then the relations between the records in the tables are stored in the database
         * Then all three sub-references are references by the first record in the CSV-data
         * Then the data of the CSV-rows using "LAST" as keyword for the uid is ignored
         */
        $this->initFixtureFromFile('fe_users', self::FIXTURE_PATH . '/Files/TestData3.csv');

        $expectedFeUsers = include(self::FIXTURE_PATH . '/Expected/Check180.php');
        $expectedFeGroups = include(self::FIXTURE_PATH . '/Expected/Check181.php');
        $expectedReturn = [
            1 => [
                'usergroup' => [
                    1 => [],
                    2 => [],
                    3 => []
                ]
            ]
        ];

        $result = $this->fixture->import();

        self::assertIsArray($result);
        self::assertEquals($expectedReturn, $result);

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');

        $statement = $queryBuilder->select('*')
            ->from('fe_users')
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expectedFeUsers[$cnt], $row);
            $cnt++;
        }

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_groups');

        $statement = $queryBuilder->select('*')
            ->from('fe_groups')
            ->execute();

        $cnt = 0;
        while ($row = $statement->fetch()) {
            self::assertEquals($expectedFeGroups[$cnt], $row);
            $cnt++;
        }
    }



    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function typeCastByRenderTypeAddsProtocolPrefixToExternalLinks()
    {
        /**
         * Scenario:
         *
         * Given a property with renderType inputLink
         * Given the value of that property has no protocol prefix
         * When the method is called
         * Then a protocol prefix is added
         */
        $this->initFixtureFromFile();

        $result = $this->fixture->typeCastByRenderType('www', 'google.de');
        self::assertEquals('https://google.de', $result);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function typeCastByRenderTypeAddsNoProtocolPrefixToExternalLinksWithProtocol()
    {
        /**
         * Scenario:
         *
         * Given a property with renderType inputLink
         * Given the value of that property has a protocol prefix
         * When the method is called
         * Then no protocol prefix is added
         */
        $this->initFixtureFromFile();

        $result = $this->fixture->typeCastByRenderType('www', 'https://google.de');
        self::assertEquals('https://google.de', $result);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function typeCastAddsByRenderTypeNoProtocolPrefixToInternalLinks()
    {
        /**
         * Scenario:
         *
         * Given a property with renderType inputLink
         * Given the value of that property has a internal protocol prefix
         * When the method is called
         * Then no protocol prefix is added
         */
        $this->initFixtureFromFile();

        $result = $this->fixture->typeCastByRenderType('www', 't3://page?uid=99');
        self::assertEquals('t3://page?uid=99', $result);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function typeCastAddsByRenderTypeNoProtocolPrefixToEmails()
    {
        /**
         * Scenario:
         *
         * Given a property with renderType inputLink
         * Given the value of that property is an email address
         * When the method is called
         * Then no protocol prefix is added
         */
        $this->initFixtureFromFile();

        $result = $this->fixture->typeCastByRenderType('www', 'test@testen.de');
        self::assertEquals('test@testen.de', $result);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function typeCastByRenderTypeAddsNoProtocolPrefixToFileLinks()
    {
        /**
         * Scenario:
         *
         * Given a property with renderType inputLink
         * Given the value of that property has a internal protocol prefix
         * When the method is called
         * Then no protocol prefix is added
         */
        $this->initFixtureFromFile();

        $result = $this->fixture->typeCastByRenderType('www', 'file://999');
        self::assertEquals('file://999', $result);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function typeCastByRenderTypeTransformsCheckBoxValue()
    {
        /**
         * Scenario:
         *
         * Given a property with renderType check
         * Given the value is greater than 1
         * When the method is called
         * Then the integer 1 is returned
         */
        $this->initFixtureFromFile();

        $result = $this->fixture->typeCastByRenderType('disable', '99');
        self::assertSame(1, $result);
    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function typeCastTransformsDateTimeToString()
    {
        /**
         * Scenario:
         *
         * Given a property with renderType inputDateTime
         * Given the value is in a german date-time format
         * When the method is called
         * Then a timestamp is returned
         * Then this timestamp is equivalent to the former date-time given
         */
        $this->initFixtureFromFile();

        $result = $this->fixture->typeCast('lastlogin', '20.09.2023 16:51:07');
        self::assertEquals('1695228667', $result);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function typeCastTransformsIntegerBasedOnEval()
    {
        /**
         * Scenario:
         *
         * Given a property with eval "int"
         * Given the value is a string with numbers and signs
         * When the method is called
         * Then an integer is returned
         */
        $this->initFixtureFromFile();

        // fake RTE
        $GLOBALS['TCA']['fe_users']['columns']['disable']['config']['eval'] = 'int';

        $result = $this->fixture->typeCast('disable', '9string9');
        self::assertSame(9, $result);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function typeCastTransformsIntegerBasedOnType()
    {
        /**
         * Scenario:
         *
         * Given a property with type "number"
         * Given the value is a string with numbers and signs
         * When the method is called
         * Then an integer is returned
         */
        $this->initFixtureFromFile();

        // fake RTE
        $GLOBALS['TCA']['fe_users']['columns']['description']['config']['type'] = 'number';

        $result = $this->fixture->typeCast('description', '9string9');
        self::assertSame(9, $result);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function typeCastTransformsFloatBasedOnEval()
    {
        /**
         * Scenario:
         *
         * Given a property with eval "double2"
         * Given the value is a string with commas
         * When the method is called
         * Then an integer is returned
         */
        $this->initFixtureFromFile();

        // fake RTE
        $GLOBALS['TCA']['fe_users']['columns']['description']['config']['eval'] = 'double2';

        $result = $this->fixture->typeCast('description', '9,56');
        self::assertSame(9.56, $result);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function typeCastTransformsFloatBasedOnType()
    {
        /**
         * Scenario:
         *
         * Given a property with type "number"
         * Given the value is a string with numbers and signs
         * When the method is called
         * Then an integer is returned
         */
        $this->initFixtureFromFile();

        // fake RTE
        $GLOBALS['TCA']['fe_users']['columns']['description']['config']['type'] = 'number';
        $GLOBALS['TCA']['fe_users']['columns']['description']['config']['format'] = 'decimal';

        $result = $this->fixture->typeCast('description', '9,56');
        self::assertSame(9.56, $result);
    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function sanitizeDoesNotAffectNonRteColumns()
    {
        /**
         * Scenario:
         *
         * Given a property with no enableRichtext
         * When the method is called
         * Then the value is returned unchanged
         */
        $this->initFixtureFromFile();

        $expected = 'Dies
                   ist
                   ein
                   Test';

        $result = $this->fixture->sanitize('description', $expected);
        self::assertEquals($expected, $result);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function sanitizeOptimizesHtmlForRteColumns()
    {
        /**
         * Scenario:
         *
         * Given a property with enableRichtext = true
         * When the method is called
         * Then the value is returned as optimized HTML
         */
        $this->initFixtureFromFile();

        // fake RTE
        $GLOBALS['TCA']['fe_users']['columns']['description']['config']['enableRichtext'] = true;

        $fixture = 'Dies
                   ist
                   ein
                   Test';

        $expected = '<p>Dies<br /> ist<br /> ein<br /> Test</p>';

        $result = $this->fixture->sanitize('description', $fixture);
        self::assertEquals($expected, $result);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function sanitizeDoesNotOptimizeHtmlForRteColumnsIfValueEmpty()
    {
        /**
         * Scenario:
         *
         * Given a property with enableRichtext = true
         * Given the value is empty
         * When the method is called
         * Then the value is returned unchanged
         */
        $this->initFixtureFromFile();

        // fake RTE
        $GLOBALS['TCA']['fe_users']['columns']['description']['config']['enableRichtext'] = true;

        $fixture = '';
        $expected = '';

        $result = $this->fixture->sanitize('description', $fixture);
        self::assertEquals($expected, $result);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sanitizeCleanesHtmlForRteColumns()
    {
        /**
         * Scenario:
         *
         * Given a property with enableRichtext = true
         * When the method is called
         * Then the value is returned as sanitized HTML
         */
        $this->initFixtureFromFile();
        $fixture = file_get_contents(self::FIXTURE_PATH . '/Files/HtmlCode.html');
        $expected = file_get_contents(self::FIXTURE_PATH . '/Expected/Check220.html');

        // fake RTE
        $GLOBALS['TCA']['fe_users']['columns']['description']['config']['enableRichtext'] = true;

        $result = $this->fixture->sanitize('description', $fixture);
        self::assertEquals($expected, $result);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function sanitizeDoesNotCleaneHtmlForRteColumnsIfValueEmpty()
    {
        /**
         * Scenario:
         *
         * Given a property with enableRichtext = true
         * Given the value is empty
         * When the method is called
         * Then the value is returned unchanged
         */
        $this->initFixtureFromFile();
        $fixture = '';
        $expected = '';

        // fake RTE
        $GLOBALS['TCA']['fe_users']['columns']['description']['config']['enableRichtext'] = true;

        $result = $this->fixture->sanitize('description', $fixture);
        self::assertEquals($expected, $result);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sanitizeTrimsValues()
    {
        /**
         * Scenario:
         *
         * Given a property with a value with leading and trailing spaces
         * When the method is called
         * Then the value is returned without leading and trailing spaces
         */
        $this->initFixtureFromFile();

        $fixture = ' This is the core value. ';
        $expected = 'This is the core value.';

        $result = $this->fixture->sanitize('description', $fixture);
        self::assertEquals($expected, $result);
    }


    #==============================================================================
    /**
     * TearDown
     */
    protected function teardown(): void
    {
        parent::tearDown();
    }


    /**
     * Inits reader from file
     *
     * @param string $table
     * @param string $file
     * @return void
     * @throws \League\Csv\Exception
     * @throws \League\Csv\InvalidArgument
     * @throws \League\Csv\UnavailableFeature
     */
    protected function initFixtureFromFile (
        string $table = 'fe_users',
        string $file = self::FIXTURE_PATH . '/Files/TestData.csv'
    ): void {

        /** \Madj2k\CoreExtended\Transfer\CsvImporter $fixture */
        $this->fixture = $this->objectManager->get(
            CsvImporter::class,
            $table
        );

        $this->fixture->readCsv($file);
        $this->fixture->setAllowedTables(['fe_users', 'fe_groups']);
        $this->fixture->setAllowedRelationTables(
            [
                'fe_users' => ['fe_groups', 'sys_file_reference'],
                'fe_groups' => ['fe_groups']
            ]
        );
        $this->fixture->setExcludeColumns(
            [
                'fe_users' => [
                    'hidden', 'deleted', 'tstamp', 'crdate', 'tx_extbase_type', 'TSconfig'
                ],
                'fe_groups' => [
                    'hidden', 'deleted', 'tstamp', 'crdate'
                ]
            ],
        );

    }


    /**
     * Inits reader from string
     *
     * @param string $table
     * @param string $file
     * @return void
     * @throws \League\Csv\Exception
     * @throws \League\Csv\InvalidArgument
     * @throws \League\Csv\UnavailableFeature
     */
    protected function initFixtureFromString (
        string $table = 'fe_users',
        string $file = self::FIXTURE_PATH . '/Files/TestData.csv'
    ): void {

        /** \Madj2k\CoreExtended\Transfer\CsvImporter $fixture */
        $this->fixture = $this->objectManager->get(
            CsvImporter::class,
            $table
        );

        $this->fixture->readCsv(file_get_contents($file));
        $this->fixture->setAllowedTables(['fe_users', 'fe_groups']);
        $this->fixture->setAllowedRelationTables(
            [
                'fe_users' => ['fe_groups', 'sys_file_reference'],
                'fe_groups' => ['fe_groups']
            ]
        );
        $this->fixture->setExcludeColumns(
            [
                'fe_users' => [
                    'hidden', 'deleted', 'tstamp', 'crdate', 'tx_extbase_type', 'TSconfig'
                ],
                'fe_groups' => [
                    'hidden', 'deleted', 'tstamp', 'crdate'
                ]
            ],
        );

    }
}
