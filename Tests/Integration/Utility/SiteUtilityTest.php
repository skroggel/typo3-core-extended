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

use Madj2k\CoreExtended\Utility\FrontendSimulatorUtility;
use Madj2k\CoreExtended\Utility\SiteUtility;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Madj2k\CoreExtended\Utility\StringUtility;

/**
 * SiteUtilityTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SiteUtilityTest extends FunctionalTestCase
{
    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/SiteUtilityTest/Fixtures';


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
            ['example.local' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        FrontendSimulatorUtility::simulateFrontendEnvironment(2,2);

    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function getTypo3LanguageByLanguageUidReturnsStringForDefault ()
    {
        /**
         * Scenario:
         *
         * Given a sub-page with uid = 2 is loaded in frontendEnd
         * Given that page is loaded with languageUid = 2
         * Given the parameter languageUid is set to the value 0
         * Given that value is part of the site configuration
         * When the method is called
         * Then a string is returned
         * Then the string has the value "de" as configured for the current page and language
         */

        $result = SiteUtility::getTypo3LanguageByLanguageUid(0);
        self::assertIsString($result);
        self::assertEquals('de', $result);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function getTypo3LanguageByLanguageUidReturnsStringForForeignLanguage ()
    {
        /**
         * Scenario:
         *
         * Given a sub-page with uid = 2 is loaded in frontendEnd
         * Given that page is loaded with languageUid = 2
         * Given the parameter languageUid is set to the value 2
         * Given that value is part of the site configuration
         * When the method is called
         * Then a string is returned
         * Then the string has the value "ru" as configured for the current page and language
         */

        $result = SiteUtility::getTypo3LanguageByLanguageUid(2);
        self::assertIsString($result);
        self::assertEquals('ru', $result);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function getTypo3LanguageByLanguageUidReturnsFallback ()
    {
        /**
         * Scenario:
         *
         * Given a sub-page with uid = 2 is loaded in frontendEnd
         * Given that page is loaded with languageUid = 2
         * Given the parameter languageUid is set to the value 2
         * Given that value is not part of the site configuration
         * When the method is called
         * Then a string is returned
         * Then the string has the value "default" as configured for the current page and language
         */

        $result = SiteUtility::getTypo3LanguageByLanguageUid(1);
        self::assertIsString($result);
        self::assertEquals('default', $result);
    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function  getLanguageUidByTypo3LanguageReturnsZero ()
    {
        /**
         * Scenario:
         *
         * Given a sub-page with uid = 2 is loaded in frontendEnd
         * Given that page is loaded with languageUid = 2
         * Given the parameter typo3Language is set to the value 'de'
         * Given that value is part of the site configuration
         * When the method is called
         * Then an integer is returned
         * Then the integer has the value "0" as configured for the current page and language
         */

        $result = SiteUtility::getLanguageUidByTypo3Language('de');
        self::assertIsInt($result);
        self::assertEquals(0, $result);

    }


    /**
     * @test
     * @throws \Exception
     */
    public function  getLanguageUidByTypo3LanguageReturnsOne ()
    {
        /**
         * Scenario:
         *
         * Given a sub-page with uid = 2 is loaded in frontendEnd
         * Given that page is loaded with languageUid = 2
         * Given the parameter typo3Language is set to the value 'ru'
         * Given that value is part of the site configuration
         * When the method is called
         * Then an integer is returned
         * Then the integer has the value "2" as configured for the current page and language
         */

        $result = SiteUtility::getLanguageUidByTypo3Language('ru');
        self::assertIsInt($result);
        self::assertEquals(2, $result);

    }


    /**
     * @test
     * @throws \Exception
     */
    public function  getLanguageUidByTypo3LanguageReturnsFallback ()
    {
        /**
         * Scenario:
         *
         * Given a sub-page with uid = 2 is loaded in frontendEnd
         * Given that page is loaded with languageUid = 2
         * Given the parameter typo3Language is set to the value 'it'
         * Given that value is not part of the site configuration
         * When the method is called
         * Then an integer is returned
         * Then the integer has the value "0" as configured for the current page and language
         */

        $result = SiteUtility::getLanguageUidByTypo3Language('it');
        self::assertIsInt($result);
        self::assertEquals(0, $result);

    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function getCurrentTypo3LanguageReturnsStringForCurrentLanguage ()
    {
        /**
         * Scenario:
         *
         * Given a sub-page with uid = 2 is loaded in frontendEnd
         * Given that page is loaded with languageUid = 2
         * When the method is called
         * Then a string is returned
         * Then the string has the value "ru" as configured for the current page and language
         */

        $result = SiteUtility:: getCurrentTypo3Language();
        self::assertIsString($result);
        self::assertEquals('ru', $result);
    }

    #==============================================================================

    /**
     * TearDown
     */
    protected function teardown(): void
    {
        parent::tearDown();
        FrontendSimulatorUtility::resetFrontendEnvironment();
    }

}
