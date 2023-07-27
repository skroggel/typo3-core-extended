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

use Madj2k\CoreExtended\Domain\Repository\PagesRepository;
use Madj2k\CoreExtended\Utility\CsvUtility;
use Madj2k\CoreExtended\Utility\GeneralUtility;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * CsvUtilityTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CsvUtilityTest extends FunctionalTestCase
{
    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/CsvUtilityTest/Fixtures';


    /**
     * @var string[]
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/core_extended',
    ];


    /**
     * @var \Madj2k\CoreExtended\Domain\Repository\PagesRepository|null
     */
    private ?PagesRepository $pagesRepository = null;


    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager|null
     */
    private ?ObjectManager $objectManager = null;


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
                'EXT:core_extended/Configuration/TypoScript/setup.txt',
                'EXT:core_extended/Configuration/TypoScript/constants.txt',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
            ],
            ['example.com' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \Madj2k\CoreExtended\Domain\Repository\PagesRepository pagesRepository */
        $this->pagesRepository = $this->objectManager->get(PagesRepository::class);

    }

    #==============================================================================

    /**
     * @test
     */
    public function createCsvBuildsEmptyFileIfNoDataGiven ()
    {
        /**
         * Scenario:
         *
         * Given an empty objectStorage
         * When the method is called
         * Then an empty CSV-file is generated
         */

        /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage $objectStorage*/
        $objectStorage = GeneralUtility::makeInstance(ObjectStorage::class);
        $fileName = Environment::getPublicPath() .'/typo3temp/test.csv';

        CsvUtility::createCsv($objectStorage, $fileName);
        $fileContent = file_get_contents($fileName);

        self::assertEquals('', $fileContent);
    }


    /**
     * @test
     */
    public function createCsvBuildsFileWithData ()
    {
        /**
         * Scenario:
         *
         * Given an objectStorage
         * Given that objectStorage contains two objects
         * When the method is called
         * Then a CSV-file with the content of the two objects is generated
         */

        /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage $objectStorage*/
        $objectStorage = GeneralUtility::makeInstance(ObjectStorage::class);
        $fileName = Environment::getPublicPath() .'/typo3temp/test.csv';

        $pages = $this->pagesRepository->findAll();

        /** @var \Madj2k\CoreExtended\Domain\Model\Pages $page */
        foreach ($pages as $page) {
            $objectStorage->attach($page);
        }

        CsvUtility::createCsv($objectStorage, $fileName);
        $fileContent = file_get_contents($fileName);

        $expected = 'uid;pid;crdate;tstamp;hidden;deleted;sysLanguageUid;sorting;doktype;title;subtitle;abstract;description;noSearch;lastUpdated;txCoreextendedAlternativeTitle;txCoreextendedFeLayoutNextLevel;txCoreextendedPreviewImage;txCoreextendedOgImage;txCoreextendedFile;txCoreextendedCover' . "\n" .
            '1;0;0;0;;0;0;0;1;Rootpage;;;;;0;;0;;;;' . "\n" .
            '2;1;0;0;;0;0;0;1;"Sub Page";;;;;0;;0;;;;' . "\n";

        self::assertEquals($expected, $fileContent);
    }

    /**
     * @test
     */
    public function createCsvBuildsFileWithDataAndExcludesSomeProperties ()
    {
        /**
         * Scenario:
         *
         * Given an objectStorage
         * Given that objectStorage contains two objects
         * Given two properties to exclude
         * When the method is called
         * Then a CSV-file with the content of the two objects is generated
         * Then the two properties to exclude are not part of the csv
         */

        /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage $objectStorage*/
        $objectStorage = GeneralUtility::makeInstance(ObjectStorage::class);
        $fileName = Environment::getPublicPath() .'/typo3temp/test.csv';

        $pages = $this->pagesRepository->findAll();

        /** @var \Madj2k\CoreExtended\Domain\Model\Pages $page */
        foreach ($pages as $page) {
            $objectStorage->attach($page);
        }

        CsvUtility::createCsv($objectStorage, $fileName, ';', ['tstamp', 'txCoreextendedAlternativeTitle']);
        $fileContent = file_get_contents($fileName);

        $expected = 'uid;pid;crdate;hidden;deleted;sysLanguageUid;sorting;doktype;title;subtitle;abstract;description;noSearch;lastUpdated;txCoreextendedFeLayoutNextLevel;txCoreextendedPreviewImage;txCoreextendedOgImage;txCoreextendedFile;txCoreextendedCover' . "\n" .
            '1;0;0;;0;0;0;1;Rootpage;;;;;0;0;;;;' . "\n" .
            '2;1;0;;0;0;0;1;"Sub Page";;;;;0;0;;;;' . "\n";

        self::assertEquals($expected, $fileContent);
    }


    #==============================================================================

    /**
     * TearDown
     */
    protected function teardown(): void
    {
        parent::tearDown();
    }

}
