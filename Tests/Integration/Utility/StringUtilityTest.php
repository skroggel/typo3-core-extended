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

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Madj2k\CoreExtended\Utility\StringUtility;

/**
 * StringUtilityTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class StringUtilityTest extends FunctionalTestCase
{
    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/StringUtilityTest/Fixtures';


    /**
     * @var string[]
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/core_extended',
    ];


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

    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function getUniqueRandomStringGeneratesRandomStrings ()
    {
        /**
         * Scenario:
         *
         * When the method is called multiple times
         * Then each time a string is returned
         * Then every string is unique
         */

        $arrayOfStrings = [];
        for ($i = 1; $i <= 10000; $i++) {

            $string = StringUtility::getUniqueRandomString();
            self::assertNotContains($string, $arrayOfStrings);
            $arrayOfStrings[] = $string;
        }
    }


    /**
     * @test
     * @throws \Exception
     */
    public function getUniqueRandomStringReturnsStringOfDefinedLength ()
    {
        /**
         * Scenario:
         *
         * When the method is called multiple times
         * Then a string is returned
         * Then the string has the length of StringUtility::RANDOM_STRING_LENGTH
         */

        $string = StringUtility::getUniqueRandomString();
        self::assertEquals(StringUtility::RANDOM_STRING_LENGTH, strlen($string));

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
