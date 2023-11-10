<?php
namespace Madj2k\CoreExtended\Transfer;

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

use League\Csv\Reader;
use Madj2k\CoreExtended\Exception;
use Madj2k\CoreExtended\Utility\GeneralUtility;
use Madj2k\CoreExtended\Utility\QueryUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Html\HtmlParser;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Class CsvImporter
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CsvImporter
{

    /**
     * Constants for typecasting
     */
    const TYPE_NUMBER = 'number';
    const FORMAT_DECIMAL = 'decimal';
    const EVAL_FLOAT2 = 'double2';
    const EVAL_DATETIME = 'datetime';
    const EVAL_INT = 'int';
    const RENDERTYPE_LINK = 'inputLink';
    const RENDERTYPE_CHECKBOX = 'checkboxToggle';


    /**
     * Constants for signalSlots
     */
    const SIGNAL_BEFORE_PERSIST = 'beforePersist';
    const SIGNAL_AFTER_UPDATE = 'afterUpdate';
    const SIGNAL_AFTER_INSERT = 'afterInsert';
    const SIGNAL_AFTER_RELATIONS = 'afterRelations';


    /**
     * @var array
     */
    protected array $allowedTables = [];


    /**
     * @var string
     */
    protected string $tableName = '';


    /**
     * @var array
     */
    protected array $excludeColumns = [];


    /**
     * @var array
     */
    protected array $includeColumns = [];


    /**
     * @var array
     */
    protected array $uniqueSelectColumns = [];


    /**
     * @var array
     */
    protected array $allowedRelationTables = [];


    /**
     * @var array
     */
    protected array $tableRelations = [];


    /**
     * @var array
     */
    protected array $header = [];


    /**
     * @var array
     */
    protected array $records = [];


    /**
     * @var array
     */
    protected array $additionalData = [];


    /**
     * @var array
     */
    protected array $defaultValues = [];


    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager|null
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?ObjectManager $objectManager = null;


    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected Dispatcher $signalSlotDispatcher;


    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;


    /**
     * @param ObjectManager $objectManager
     * @return void

    public function injectObjectManager (ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }
     */

    /**
     * @param string $pathOrString
     * @param bool $isExcel Important: this parameter also overrides $delimiter if true
     * @param string $delimiter
     * @return void
     * @throws \League\Csv\Exception
     * @throws \League\Csv\InvalidArgument
     * @throws \League\Csv\UnavailableFeature
     */
    public function readCsv (string $pathOrString, bool $isExcel = true, string $delimiter = ',')
    {
        // load the CSV document from a file path or from string
        if (file_exists($pathOrString)) {
            $csvReader = Reader::createFromPath($pathOrString, 'r');
        } else {
            $csvReader = Reader::createFromString($pathOrString);
        }

        // special settings for f***ing MS Excel
        $csvReader->setOutputBOM(Reader::BOM_UTF8);
        $csvReader->setDelimiter($delimiter);

        if ($isExcel) {
            $csvReader->addStreamFilter('convert.iconv.ISO-8859-15/UTF-8');
            $csvReader->setDelimiter(';');
        }

        $csvReader->setHeaderOffset(0);
        $this->setHeader($csvReader->getHeader());
        $this->records = iterator_to_array($csvReader->getRecords($this->header));
    }


    /**
     * Gets the tableName
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }


    /**
     * Sets the tableName
     *
     * @param string $tableName
     * @return void
     * @throws Exception
     */
    public function setTableName(string $tableName): void
    {
        // if there is no configuration for the given table, kill it here
        if (! $GLOBALS['TCA'][$tableName]) {
            throw new Exception(
                'No TCA-configuration available for the given table.',
                1695272455
            );
        }

        $this->tableName = $tableName;
    }


    /**
     * Gets the allowedTables
     *
     * @return array
     */
    public function getAllowedTables(): array
    {
        return $this->allowedTables;
    }


    /**
     * Sets the allowedTables
     *
     * @param array $allowedTables
     * @return void
     */
    public function setAllowedTables(array $allowedTables): void
    {
        $this->allowedTables = $allowedTables;
    }


    /**
     * Gets the excludeColumns
     *
     * @param string $tableName
     * @return array
     */
    public function getExcludeColumns(string $tableName = ''): array
    {
        return $tableName
            ? ($this->excludeColumns[$tableName] ?: [])
            : $this->excludeColumns;
    }


    /**
     * Sets the excludeColumns
     *
     * @param array $excludeColumns
     * @return void
     */
    public function setExcludeColumns(array $excludeColumns): void
    {
        $this->excludeColumns = $excludeColumns;
    }


    /**
     * Gets the includeColumns
     *
     * @param string $tableName
     * @return array
     */
    public function getIncludeColumns(string $tableName = ''): array
    {
        return $tableName
            ? ($this->includeColumns[$tableName] ?: [])
            : $this->includeColumns;
    }


    /**
     * Sets the includeColumns
     *
     * @param array $includeColumns
     * @return void
     */
    public function setIncludeColumns(array $includeColumns): void
    {
        $this->includeColumns = $includeColumns;
    }


    /**
     * Gets the uniqueSelectColumns
     *
     * @param string $tableName
     * @return array
     */
    public function getUniqueSelectColumns(string $tableName = ''): array
    {
        return $tableName
            ? ($this->uniqueSelectColumns[$tableName] ?: [])
            : $this->uniqueSelectColumns;
    }


    /**
     * Sets the uniqueSelectColumns
     *
     * @param array $uniqueSelectColumns
     * @return void
     */
    public function setUniqueSelectColumns(array $uniqueSelectColumns): void
    {
        $this->uniqueSelectColumns = $uniqueSelectColumns;
    }


    /**
     * Gets the allowedRelationTables
     *
     * @param string $tableName
     * @return array
     */
    public function getAllowedRelationTables(string $tableName = ''): array
    {
        return $tableName
            ? ($this->allowedRelationTables[$tableName] ?: [])
            : $this->allowedRelationTables;
    }


    /**
     * Sets the allowedRelationTables
     *
     * @param array $allowedRelationTables
     * @return void
     */
    public function setAllowedRelationTables(array $allowedRelationTables): void
    {
        $this->allowedRelationTables = $allowedRelationTables;
    }


    /**
     * Get the importable columns
     *
     * @param string $tableName
     * @return array
     */
    public function getImportableColumns(string $tableName): array
    {
        $columns = array_keys($GLOBALS['TCA'][$tableName]['columns']);
        $excludeColumns = $this->getExcludeColumns($tableName);
        $includeColumns = $this->getIncludeColumns($tableName);

        return array_merge(
            array_values(
                array_diff(
                    $columns,
                    $excludeColumns
                )
            ),
            $includeColumns
        );
    }


    /**
     * Get columns with relations to another tables
     *
     * @param string $tableName
     * @return array
     */
    public function getTableRelations(string $tableName): array
    {

        if ($this->tableRelations[$tableName]) {
            return $this->tableRelations[$tableName];
        }

        $result = [];
        foreach ($GLOBALS['TCA'][$tableName]['columns'] as $column => $config) {
            if (
                ($foreignTable = $config['config']['foreign_table'])
                && (in_array($foreignTable, $this->getAllowedRelationTables($tableName)))
                && ($config['config']['type'])
                && (! $config['config']['mm'])
            ) {
                // add type and number of items
                $result[$column]['type'] = $config['config']['type'];
                $result[$column]['minitems'] = $config['config']['minitems'];
                $result[$column]['maxitems'] = $config['config']['maxitems'];

                // and all fields beginning with "foreign_"
                foreach ($config['config'] as $key => $value) {
                    if (strpos($key, 'foreign_') === 0) {
                        $result[$column][$key] = $config['config'][$key];
                    }
                }
            }
        }

        return $result;
    }


    /**
     * Gets data that overrides each column of the imported data
     *
     * @return array
     */
    public function getAdditionalData (): array
    {
        return $this->additionalData;
    }


    /**
     * Sets data that overrides each column of the imported data
     *
     * @param array $additionalData
     * @return void
     */
    public function setAdditionalData (array $additionalData): void
    {
        $this->additionalData = $additionalData;
    }


    /**
     * Inserts additionalData to raw data
     *
     * @return void
     */
    public function applyAdditionalData (): void
    {
        // merge additional array into record, override existing values
        foreach ($this->records as &$record) {
            $record = array_merge($record, $this->getAdditionalData());
        }

        // update header accordingly
        if (
            ($currentRecord = current($this->records))
            && (is_array($currentRecord))
        ) {
            $this->setHeader(array_keys($currentRecord));
        }
    }


    /**
     * Gets array that defines default values
     *
     * @return array
     */
    public function getDefaultValues (): array
    {
        return $this->defaultValues;
    }


    /**
     * Sets array that defines default values
     *
     * @param array $defaultValues
     * @return void
     */
    public function setDefaultValues (array $defaultValues): void
    {
        $this->defaultValues = $defaultValues;
    }


    /**
     * Inserts default values to raw data
     *
     * @return void
     */
    public function applyDefaultValues (): void
    {
        // merge default into record, but only if there are no existing values
        foreach ($this->records as &$record) {
            foreach ($this->getDefaultValues() as $key => $value) {
                if (! $record[$key]) {
                    $record[$key] = $value;
                }
            }
        }

        // update header accordingly
        if (
            ($currentRecord = current($this->records))
            && (is_array($currentRecord))
        ) {
            $this->setHeader(array_keys($currentRecord));
        }
    }


    /**
     * Get the raw header from CSV
     *
     * @return array
     */
    public function getHeader(): array
    {
        // simply return the unfiltered header with numeric keys
        return $this->header;
    }


    /**
     * Sets the raw header from CSV
     *
     * @param array $header
     * @return void
     */
    public function setHeader(array $header): void
    {
        $this->header = $header;
    }


    /**
     * Get raw records of the CSV
     *
     * @return array
     */
    public function getRecords(): array
    {
        // simply return all records unfiltered and with column-names as key
        // but reset the numeric index
        return array_values($this->records);
    }


    /**
     * Set raw records from CSV
     *
     * @param array $records
     * @return void
     */
    public function setRecords(array $records): void
    {
        $this->records = $records;
    }


    /**
     * Filter columns of the given record by rootColumn
     *
     * @param array $record
     * @param string $rootColumn
     * @return array
     */
    public function filterRecordByRootColumn(array $record, string $rootColumn = ''): array
    {
        $result = [];
        $needle = $rootColumn . '.';
        $check = $rootColumn ? 0 : false;

        foreach ($record as $column => $value) {

            // filter columns by rootColumn as prefix if a rootColumn is set
            // OR return only columns WITHOUT prefix if no rootColumn is set
            if (strpos($column, $needle) === $check) {

                // remove prefix
                if ($rootColumn) {
                    $column = substr($column, strlen($rootColumn)+1);
                }
                $result[$column] = $value;
            }
        }

        return $result;
    }


    /**
     * Filter columns of the given record by importable columns
     *
     * @param array $record
     * @return array
     */
    public function filterRecordByImportableColumns(array $record): array
    {
        $filteredHeader = array_values(array_intersect($this->getHeader(), $this->getImportableColumns($this->getTableName())));
        return array_intersect_key($record, array_flip($filteredHeader));
    }


    /**
     * Search for existing records based on uniqueSelectColumns
     *
     * @param array $record
     * @return int
     */
    public function searchExistingRecord (array $record): int
    {
        //  check for existing data based on defined keys and try to get an uid
        if ($selectColumns = $this->getUniqueSelectColumns($this->getTableName())) {

            /** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

            /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
            $queryBuilder = $connectionPool->getQueryBuilderForTable($this->getTableName());
            $queryBuilder->getRestrictions()->removeAll();

            $conditions = [];
            foreach ($selectColumns as $column) {
                if ($record[$column]) {
                    $conditions[] = $queryBuilder->expr()->eq(
                        $column,
                        $queryBuilder->createNamedParameter($record[$column])
                    );
                }
            }

            if ($conditions) {
                // get existing values in relation-field
                $statement = $queryBuilder->select('uid')
                    ->from($this->getTableName())
                    ->where(...$conditions)
                    ->execute();

                $this->logSqlStatement($queryBuilder);
                return $statement->fetchColumn();
            }
        }

        return 0;
    }


    /**
     * Check for an existing record based on uid
     *
     * @param int $uid
     * @return bool
     */
    public function doesRecordExist (int $uid): bool
    {
        /** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = $connectionPool->getQueryBuilderForTable($this->getTableName());
        $queryBuilder->getRestrictions()->removeAll();

        $statement = $queryBuilder->select('uid')
            ->from($this->getTableName())
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                ))
            ->execute();

        $this->logSqlStatement($queryBuilder);
        return (bool) $statement->fetchColumn();
    }

    /**
     * Update or insert record
     *
     * @param array $recordRaw
     * @return int
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \Exception
     */
    public function persistRecord (array $recordRaw): int
    {

        // check if table is in allowed tables
        if (in_array($this->getTableName(), $this->getAllowedTables())) {

            // do some basic filtering
            $record = $this->filterRecordByImportableColumns($recordRaw);
            if (count($record) > 0) {

                // do typecast and parsing if needed
                foreach ($record as $property => &$value) {
                    $value = $this->typeCastByRenderType($property, $value);
                    $value = $this->typeCast($property, $value);
                    $value = $this->sanitize($property, $value);
                }

                $this->signalSlotDispatcher->dispatch(
                    __CLASS__,
                    self::SIGNAL_BEFORE_PERSIST,
                    [$record]
                );

                /** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
                $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

                // if there is no uid or a non-existing one...
                if (
                    (! $uid = intval($recordRaw['uid']))
                    || (! $this->doesRecordExist($uid))
                ) {

                    // if not, we check via uniqueSelectColumns if there is a matching record
                    $uid = $this->searchExistingRecord($record);
                }


                // if there is an uid given that exists, we try to update
                if (
                    ($uid)
                    && ($this->doesRecordExist($uid))
                ){

                    /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
                    $queryBuilder = $connectionPool->getQueryBuilderForTable($this->getTableName());
                    $queryBuilder->getRestrictions()->removeAll();

                    $queryBuilder
                        ->update($this->getTableName())
                        ->where(
                            $queryBuilder->expr()->eq(
                                'uid',
                                $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                            )
                        );

                    foreach ($record as $column => $updateValue) {
                        $queryBuilder->set($column, $updateValue);
                    }

                    if ($queryBuilder->execute()) {

                        $this->signalSlotDispatcher->dispatch(
                            __CLASS__,
                            self::SIGNAL_AFTER_UPDATE,
                            [$uid, $record]
                        );
                    }

                    return $uid;
                }

                // if no update took place and no existing record can be found, do insert!
                // never insert an uid!
                unset($record['uid']);

                /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
                $queryBuilder = $connectionPool->getQueryBuilderForTable($this->getTableName());
                $queryBuilder->getRestrictions()->removeAll();

                $queryBuilder
                    ->insert($this->getTableName())
                    ->values($record)
                    ->execute();

                $uid = $queryBuilder->getConnection()->lastInsertId();

                $this->signalSlotDispatcher->dispatch(
                    __CLASS__,
                    self::SIGNAL_AFTER_INSERT,
                    [$uid, $record]
                );

                $this->logSqlStatement($queryBuilder);

                return $uid;
            }
        }

        return 0;
    }


    /**
     * Get columns with relations to another tables
     *
     * @param int $uid Uid of the current record that has been stored in the database
     * @param string $column The relevant column
     * @param array $subUidList The uids of the elements to set a relation to the current record
     * @return bool
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function persistTableRelations(int $uid, string $column, array $subUidList): bool
    {
        $tableRelations = $this->getTableRelations($this->getTableName());
        $columnConfiguration = $tableRelations[$column];

        if (
            ($columnConfiguration)
            && ($foreignTable = $columnConfiguration['foreign_table'] )
        ){

            /** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

            /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
            $queryBuilder = $connectionPool->getQueryBuilderForTable($this->getTableName());
            $queryBuilder->getRestrictions()->removeAll();

            /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilderForeign */
            $queryBuilderForeign = $connectionPool->getQueryBuilderForTable($foreignTable);
            $queryBuilderForeign->getRestrictions()->removeAll();

            // set current uid into all subUidList-records in the foreign field
            // and set the number of records to the column of the current record
            if ($foreignField = $columnConfiguration['foreign_field']) {

                /** todo: write tests for this condition */
                $queryBuilderForeign
                    ->update($foreignTable)
                    ->where(
                        $queryBuilderForeign->expr()->in(
                            'uid',
                            $queryBuilderForeign->createNamedParameter(
                                $subUidList,
                                Connection::PARAM_INT_ARRAY
                            )
                        )
                    )
                    ->set($foreignField, $uid);

                // check if the foreign_table_field is set
                if ($foreignTableField = $columnConfiguration['foreign_table_field']) {
                    $queryBuilderForeign->set($foreignTableField, $this->getTableName());
                }

                // check if the foreign_match_fields is set
                if ($foreignMatchFields = $columnConfiguration['foreign_match_fields']) {
                    foreach($foreignMatchFields as $field => $value) {
                        $queryBuilderForeign->set($field, $value);
                    }
                }

                $queryBuilderForeign->execute();
                $this->logSqlStatement($queryBuilderForeign);

                $queryBuilder
                    ->update($this->getTableName())
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                        )
                    )
                    ->set($column, count($subUidList))
                    ->execute();

                $this->logSqlStatement($queryBuilder);

            // set subUidList-records as comma-separated list into column of current record
            } else {

                $currentRelations = [];
                if ($columnConfiguration['maxitems'] != 1) {
                    // get existing values in relation-field
                    $statement = $queryBuilder->select($column)
                        ->from($this->getTableName())
                        ->where(
                            $queryBuilder->expr()->eq(
                                'uid',
                                $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                            )
                        )
                        ->execute();

                    $currentRelations = GeneralUtility::trimExplode(',', $statement->fetchColumn(), true);
                    $this->logSqlStatement($queryBuilder);
                }

                $queryBuilder
                    ->update($this->getTableName())
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                        )
                    )
                    ->set($column, implode(',', array_unique(array_merge($currentRelations, $subUidList))))
                    ->execute();

                $this->logSqlStatement($queryBuilder);
            }

            $this->signalSlotDispatcher->dispatch(
                __CLASS__,
                self::SIGNAL_AFTER_RELATIONS,
                [$uid, $subUidList, $column, $columnConfiguration]
            );

            return true;
        }

        return false;
    }


    /**
     * Does the import to the database
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \Exception
     */
    public function import(): array
    {
        $result = [];
        $recordsRaw = $this->getRecords();
        $uid = 0;
        foreach ($recordsRaw as $recordRaw) {

            // keyword used? Do not do anything but setting relations
            if ($recordRaw['uid'] == 'LAST') {
                $uid = array_key_last($result);

            // else do normal update / insert-stuff
            } else {
                $uid = intval($recordRaw['uid']);

                // update uid accordingly
                if ($tempUid = $this->persistRecord($recordRaw)) {
                    $uid = $tempUid;
                }
            }

            // call recursively if successful
            if ($this->doesRecordExist($uid)) {

                if (! $result[$uid]) {
                    $result[$uid] = [];
                }

                // check for relations. If there are some, call import recursively with matching table and rootColumn
                // this can also be done if only an uid is given!
                $tempResult = [];
                if ($columnRelation = $this->getTableRelations($this->getTableName())) {
                    foreach ($columnRelation as $column => $config) {

                        if ($foreignTable = $config['foreign_table']) {
                            if ($subResult = $this->importRecursive($uid, $column, $foreignTable, $recordRaw)) {
                                $tempResult[$column] = $subResult;
                            }
                        }
                    }
                }
                $result[$uid] = array_merge_recursive($result[$uid], $tempResult);
            }
        }

        return $result;
    }


    /**
     * Do import recursively
     *
     * @param int $uid
     * @param string $column
     * @param string $foreignTable
     * @param array $recordRaw
     * @return array
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    protected function importRecursive (int $uid, string $column, string $foreignTable, array $recordRaw): array
    {
        $result = [];

        // check if there is data to import on the next level
        if ($subRecordRaw = $this->filterRecordByRootColumn($recordRaw, $column)) {

            // init importer with relevant setup
            $csvImporter = $this->objectManager->get(self::class, $foreignTable);
            $csvImporter->setAllowedTables($this->getAllowedTables());
            $csvImporter->setExcludeColumns($this->getExcludeColumns());
            $csvImporter->setIncludeColumns($this->getIncludeColumns());
            $csvImporter->setUniqueSelectColumns($this->getUniqueSelectColumns());
            $csvImporter->setAllowedRelationTables($this->getAllowedRelationTables());

            // init data
            $csvImporter->setHeader(array_keys($subRecordRaw));
            $csvImporter->setRecords([$subRecordRaw]);

            // do import and set relations accordingly
            if ($result = $csvImporter->import()) {
                $this->persistTableRelations($uid, $column, array_keys($result));
            }
        }

        return $result;
    }


    /**
     * Do typecast based on eval and type in TCA
     *
     * @param string $property
     * @param mixed $value
     * @return mixed
     * @throws \Exception
     */
    public function typeCast(string $property, $value)
    {

        $evalArray = GeneralUtility::trimExplode(',', $GLOBALS['TCA'][$this->getTableName()]['columns'][$property]['config']['eval'], true);
        $type = $GLOBALS['TCA'][$this->getTableName()]['columns'][$property]['config']['type'] ?: '';
        $format = $GLOBALS['TCA'][$this->getTableName()]['columns'][$property]['config']['format'] ?: '';

        // datetime
        if (in_array(self::EVAL_DATETIME, $evalArray)) {
            $value = strtotime($value. ' GMT', 0);

            /**
             * @todo check if REALLY needed for daylight saving time
             */
            /*
            $date = new \DateTime(strftime("%Y-%m-%d %H:%M:%S", $value));
            if ($date->format('I')) {
                $value -= 60 * 60;
            }*/

        // int
        } else if (
            in_array(self::EVAL_INT, $evalArray) // deprecated since v12.0
            || (
                ($type == self::TYPE_NUMBER)
                && ($format != self::FORMAT_DECIMAL)
            )
        ) {
            $value = intval($value);

        // float
        } else if (
            (in_array(self::EVAL_FLOAT2, $evalArray))
            || (
                ($type == self::TYPE_NUMBER)
                && ($format == self::FORMAT_DECIMAL)
            )
        ){
            $value = floatval(str_replace(',', '.', str_replace('.', '', $value)));
        }

        return $value;
    }


    /**
     * Do typecast based on renderType in TCA
     *
     * @param string $property
     * @param $value
     * @return mixed
     */
    public function typeCastByRenderType (string $property, $value)
    {
        $renderType = $GLOBALS['TCA'][$this->getTableName()]['columns'][$property]['config']['renderType'] ?: '';
        switch ($renderType) {
            // links
            case self::RENDERTYPE_LINK:
                if (
                    ($value)
                    && (! GeneralUtility::validEmail($value))
                    && strpos($value, 'http://') === false
                    && strpos($value, 'https://') === false
                    && strpos($value, 't3://') === false
                    && strpos($value, 'file://') === false
                ) {
                    $value = 'https://' . $value;
                }
                break;

            // checkbox
            case self::RENDERTYPE_CHECKBOX:
                $value = intval(boolval($value));
                break;

            default:
                break;
        }

        return $value;
    }


    /**
     * Parse HTML
     *
     * @param string $property
     * @param mixed $value
     * @return mixed
     */
    public function sanitize(string $property, $value)
    {
        $parseHtml = $GLOBALS['TCA'][$this->getTableName()]['columns'][$property]['config']['enableRichtext'];
        if (
            ($value)
            && ($parseHtml)
        ){

            // configuration for HTML-Cleanup
            $tagCfg = [
                'br' => [
                    'allowedAttribs' => 0
                ],
                'a'  => [
                    'allowedAttribs' => 'href, target'
                ],
                'p'  => [
                    'allowedAttribs' => 0
                ],
                'ul' => [
                    'nesting' => 'global',
                    'allowedAttribs' => 0
                ],
                'ol' => [
                    'nesting' => 'global',
                    'allowedAttribs' => 0
                ],
                'li' => [
                    'nesting' => 'global',
                    'allowedAttribs' => 0
                ]
            ];
            $additionalConfig = array(
                'stripEmptyTags' => 1,
            );

            $value = str_replace('\n', '', trim($value));

            /** @var \TYPO3\CMS\Core\Html\HtmlParser $parseObj */
            $parseObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(HtmlParser::class);
            $value = $parseObj->HTMLcleaner(trim($value), $tagCfg, 0, 0, $additionalConfig);

            // add p-tag-wrap and brs if there is no HTML
            if (strip_tags($value) == $value){
                $value = '<p>' . nl2br($value) . '</p>';
            }

            // cleanup linebreaks and spaces
            $value = preg_replace('#(\n|\r|\t)#', '', $value);
            $value = preg_replace('#[ ]{2,}#', ' ', $value);

            // cleanup bad HTML
            $value = preg_replace('#<p>[ ]*<p>#', '<p>', trim($value));
            $value = preg_replace('#</p>[ ]*</p>#', '</p>', trim($value));
            $value = str_replace('&shy;', '', trim($value));
        }

        return trim($value);

    }


    /**
     * Write SQL-statement to logger
     *
     * @param \TYPO3\CMS\Core\Database\Query\QueryBuilder  $queryBuilder
     * @return void
     */
    protected function logSqlStatement(QueryBuilder $queryBuilder): void
    {
        // var_dump(QueryUtility::getFullSql($queryBuilder));
        $this->getLogger()->log(
            LogLevel::DEBUG,
            sprintf(
                'SQL-Query: %s',
                QueryUtility::getFullSql($queryBuilder)
            )
        );
    }

    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger(): Logger
    {
        if (!$this->logger instanceof Logger) {
            $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }
}
