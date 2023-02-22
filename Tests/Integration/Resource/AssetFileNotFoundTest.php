<?php
namespace Madj2k\CoreExtended\Tests\Integration\Resource;

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

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Madj2k\CoreExtended\Resource\AssetFileNotFound;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * AssetFileNotFoundTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AssetFileNotFoundTest extends FunctionalTestCase
{

    const BASE_PATH = __DIR__ . '/AssetFileNotFoundTest';


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
     * @var \Madj2k\CoreExtended\Resource\AssetFileNotFound|null
     */
    private ?AssetFileNotFound $subject = null;


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

        $this->importDataSet(self::BASE_PATH . '/Fixtures/Database/Global.xml');

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->subject = $this->objectManager->get(AssetFileNotFound::class);


        // create folder for files
        if (file_exists(\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp')) {

            if (! file_exists(\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets')) {
                mkdir (\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets');
            }

            if (! file_exists(\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets/images')) {
                mkdir (\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets/images');
            }

            foreach (range(1, 4) as $fileCount) {
                if (! file_exists(\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets/images/csm_2020-10-08-Unternehmensstandort_e47fb575c0_' . $fileCount . '.jpg')) {
                    touch (\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets/images/csm_2020-10-08-Unternehmensstandort_e47fb575c0_' . $fileCount . '.jpg');
                }
            }
        }
    }


    //=============================================

    /**
     * @test
     */
    public function searchFileIgnoresNonCsmPrefix()
    {

        /**
         * Scenario:
         *
         * Given the url does refer to an image file
         * Given the the image file has the right path
         * Given the image file-name has no csm-prefix
         * When searchFile is called
         * Then false is returned
         */
        $url = 'https://www.beispiel.de/typo3temp/assets/images/csd_2020-10-08-Unternehmensstandort_e47fb575c0_logo_b44a445e64.jpg';
        self::assertFalse( $this->subject->searchFile($url));
    }
    /**
     * @test
     */
    public function searchFileIgnoresTheGivenPath()
    {

        /**
         * Scenario:
         *
         * Given the url does refer to an image file
         * Given the image file is not referring to the asset-image path
         * When searchFile is called
         * Then an array is returned
         */
        $url = 'https://www.beispiel.de/typo3temp/assets/babalala/csm_2020-10-08-Unternehmensstandort_e47fb575c0_logo_b44a445e64.jpg';
        self::assertIsArray( $this->subject->searchFile($url));
    }


    /**
     * @test
     */
    public function searchFileIgnoresNonMatchingExtensions()
    {

        /**
         * Scenario:
         *
         * Given the url does refer to an image file
         * Given the the image file has the right path
         * Given the image file-name has the csm-prefix
         * Given the name of the image file is identical with an existing file
         * Given the existing file has another file extension as the given file
         * When searchFile is called
         * Then false is returned
         */
        $url = 'https://www.beispiel.de/typo3temp/assets/images/csd_2020-10-08-Unternehmensstandort_e47fb575c0_logo_b44a445e64.png';
        self::assertFalse( $this->subject->searchFile($url));
    }

    /**
     * @test
     */
    public function searchFileReturnsExistingFile()
    {

        /**
         * Scenario:
         *
         * Given the url does refer to an image file
         * Given the the image file has the right path
         * Given the image file-name has the csm-prefix
         * Given the name of the image file is identical with an existing file
         * Given the existing file has the same file-extension as the given file
         * When searchFile is called
         * Then an array is returned
         * Then the absolute path to the existing file is returned
         * Then the relative path to the existing file is returned
         * Then the path to the existing file is returned
         * Then the file extension of the given image file is returned
         * Then the file size of the the existing file is returned
         */
        $url = 'https://www.beispiel.de/typo3temp/assets/images/csm_2020-10-08-Unternehmensstandort_e47fb575c0_3.jpg';
        $result = $this->subject->searchFile($url);

        self::assertIsArray( $result);
        self::assertStringEndsWith('typo3temp/assets/images/csm_2020-10-08-Unternehmensstandort_e47fb575c0_3.jpg', $result['absolutePath']);
        self::assertEquals('typo3temp/assets/images/csm_2020-10-08-Unternehmensstandort_e47fb575c0_3.jpg', $result['relativePath']);
        self::assertEquals('csm_2020-10-08-Unternehmensstandort_e47fb575c0_3.jpg', $result['file']);
        self::assertEquals('jpg', $result['extension']);
        self::assertEquals(0, $result['size']);
        self::assertFileExists($result['absolutePath']);
    }

    /**
     * @test
     *
     */
    public function searchFileReturnsMatchingImage()
    {


        /**
         * Scenario:
         *
         * Given the url does refer to an image file
         * Given the image file has the right path
         * Given the image file has the csm-prefix
         * Given the name of the image file is not identical with an existing file
         * Given the name of the image file begins with the name of an existing file
         * Given the existing file has the same file-extension as the given file
         * When searchFile is called
         * Then an array is returned
         * Then the absolute path to the first matching file is returned
         * Then the relative path to the first matching file is returned
         * Then the file extension of the given image file is returned
         * Then the file size of the first matching file is returned
         * Then a symlink from the missing to the existing file is generated
         */
        $url = 'https://www.beispiel.de/typo3temp/assets/images/csm_2020-10-08-Unternehmensstandort_e47fb575c0_logo_b44a445e64.jpg';
        $result = $this->subject->searchFile($url);

        self::assertIsArray( $result);
        self::assertStringEndsWith('typo3temp/assets/images/csm_2020-10-08-Unternehmensstandort_e47fb575c0_logo_b44a445e64.jpg', $result['absolutePath']);
        self::assertEquals('typo3temp/assets/images/csm_2020-10-08-Unternehmensstandort_e47fb575c0_logo_b44a445e64.jpg', $result['relativePath']);
        self::assertEquals('csm_2020-10-08-Unternehmensstandort_e47fb575c0_logo_b44a445e64.jpg', $result['file']);
        self::assertEquals('jpg', $result['extension']);
        self::assertEquals(0, $result['size']);
        self::assertFileExists($result['absolutePath']);
    }


    /**
     * TearDown
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // remove folders and files
        if (file_exists(\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets/images')) {

            foreach (range(1, 4) as $fileCount) {
                if (file_exists(\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets/images/csm_2020-10-08-Unternehmensstandort_e47fb575c0_' . $fileCount . '.jpg')) {
                    unlink (\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets/images/csm_2020-10-08-Unternehmensstandort_e47fb575c0_' . $fileCount . '.jpg');
                }
            }

            if (file_exists(\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets/images')) {
                rmdir (\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets/images');
            }

            if (file_exists(\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets')) {
                rmdir (\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3temp/assets');
            }
        }
    }


}
