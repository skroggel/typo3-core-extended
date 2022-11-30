<?php
namespace Madj2k\CoreExtended\Tests\Integration\ContentProcessing;

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
use Madj2k\CoreExtended\ContentProcessing\PseudoCdn;
use Madj2k\CoreExtended\ContentProcessing\ReplaceExtensionPaths;
use Madj2k\CoreExtended\Utility\FrontendSimulatorUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * ReplaceExtensionPathsTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ReplaceExtensionPathsTest extends FunctionalTestCase
{

    const BASE_PATH = __DIR__ . '/ReplaceExtensionPathsTest';


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
     * @var \Madj2k\CoreExtended\ContentProcessing\ReplaceExtensionPaths
     */
    private $subject;


    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    private $objectManager;



    /**
     * Setup
     * @throws \Exception
     */
    protected function setUp(): void
    {

        parent::setUp();

        $this->importDataSet(self::BASE_PATH . '/Fixtures/Database/Global.xml');
        $this->setUpFrontendRootPage(
            1,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.txt',
                'EXT:core_extended/Configuration/TypoScript/constants.txt',
                self::BASE_PATH . '/Fixtures/Frontend/Configuration/Rootpage.typoscript',
                self::BASE_PATH . '/Fixtures/Frontend/Configuration/Check10.typoscript',
            ]
        );


        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->subject = $this->objectManager->get(ReplaceExtensionPaths::class);
    }



    //=============================================

    /**
     * @test
     */
    public function processReplacesPathsOfLoadedExtensions()
    {

        /**
         * Scenario:
         *
         * Given the HTML contains paths to loaded extensions
         * When process is called
         * Then the paths to the loaded extensions are replaced by the real paths
         */

        $html = file_get_contents(self::BASE_PATH . '/Fixtures/Frontend/Templates/Check10.html');
        $expected = file_get_contents(self::BASE_PATH . '/Fixtures/Expected/Check10.html');

        self::assertEquals($expected, $this->subject->process($html));
    }

    /**
     * @test
     */
    public function processIgnoredPathsOfNonLoadedExtensions()
    {

        /**
         * Scenario:
         *
         * Given the HTML contains paths to non-loaded extensions
         * When process is called
         * Then the paths to the non-loaded extensions are not replaced by the real paths
         */

        $html = file_get_contents(self::BASE_PATH . '/Fixtures/Frontend/Templates/Check20.html');
        $expected = file_get_contents(self::BASE_PATH . '/Fixtures/Expected/Check20.html');

        self::assertEquals($expected, $this->subject->process($html));
    }



    /**
     * TearDown
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }


}
