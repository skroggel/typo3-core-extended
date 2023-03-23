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

/**
 * MetaTagGeneratorTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MetaTagGeneratorTest extends FunctionalTestCase
{
    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/MetaTagGeneratorTest/Fixtures';


    /**
     * @var string[]
     */
    protected $coreExtensionsToLoad = [
        'seo'
    ];


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
    }

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function itInheritsDescription ()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has a description set
         * Given a subpage of this page
         * Given this subpage has no description set
         * When the method is called on that subpage
         * Then the description of the parent page is set
         */
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check10.xml');

        $this->setUpFrontendRootPage(
            3,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.txt',
                'EXT:core_extended/Configuration/TypoScript/constants.txt',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
            ],
            ['example.local' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        FrontendSimulatorUtility::simulateFrontendEnvironment(3);

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(3);

        self::assertStringContainsString('<meta name="description" content="Parent Description" />', $response->getContent());

        FrontendSimulatorUtility::resetFrontendEnvironment();
    }


    /**
     * @test
     * @throws \Exception
     */
    public function itInheritsKeywords ()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has keywords set
         * Given a subpage of this page
         * Given this subpage has no keywords set
         * When the method is called on that subpage
         * Then the keywords of the parent page are set
         * Then "nofollow" is set as robots-tag
         */
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check20.xml');

        $this->setUpFrontendRootPage(
            3,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.txt',
                'EXT:core_extended/Configuration/TypoScript/constants.txt',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
            ],
            ['example.local' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        FrontendSimulatorUtility::simulateFrontendEnvironment(3);

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(3);

        self::assertStringContainsString('<meta name="keywords" content="Parent, Keywords" />', $response->getContent());

        FrontendSimulatorUtility::resetFrontendEnvironment();
    }


    /**
     * @test
     * @throws \Exception
     */
    public function itInheritsDescriptionAndKeywords ()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has a description set
         * Given this page has keywords set
         * Given a subpage of this page
         * Given this subpage has no description set
         * Given this subpage has no keywords set
         * When the method is called on that subpage
         * Then the description of the parent page is set
         * Then the keywords of the parent page are set
         */
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check30.xml');

        $this->setUpFrontendRootPage(
            3,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.txt',
                'EXT:core_extended/Configuration/TypoScript/constants.txt',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
            ],
            ['example.local' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        FrontendSimulatorUtility::simulateFrontendEnvironment(3);

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(3);

        self::assertStringContainsString('<meta name="description" content="Parent Description" />', $response->getContent());
        self::assertStringContainsString('<meta name="keywords" content="Parent, Keywords" />', $response->getContent());

        FrontendSimulatorUtility::resetFrontendEnvironment();
    }


    /**
     * @test
     * @throws \Exception
     */
    public function itInheritsDescriptionAndKeywordsFromFirstParent ()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has a description set
         * Given this page has keywords set
         * Given a subpage of this page
         * Given this subpage has a description set
         * Given this subpage has keywords set
         * Given a sub-subpage of the page
         * Given this sub-subpage has no description set
         * Given this sub-subpage has no keywords set
         * When the method is called on that subpage
         * Then the description of the subpage is set
         * Then the keywords of the subpage are set
         */
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check70.xml');

        $this->setUpFrontendRootPage(
            4,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.txt',
                'EXT:core_extended/Configuration/TypoScript/constants.txt',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
            ],
            ['example.local' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        FrontendSimulatorUtility::simulateFrontendEnvironment(4);

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(4);

        self::assertStringContainsString('<meta name="description" content="Subpage Description" />', $response->getContent());
        self::assertStringContainsString('<meta name="keywords" content="Subpage, Keywords" />', $response->getContent());

        FrontendSimulatorUtility::resetFrontendEnvironment();
    }


    /**
     * @test
     * @throws \Exception
     */
    public function itSetsDescription ()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has a description set
         * Given a subpage of this page
         * Given this subpage has a description set
         * When the method is called on that page
         * Then the description of the subpage is set
         */
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check40.xml');

        $this->setUpFrontendRootPage(
            3,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.txt',
                'EXT:core_extended/Configuration/TypoScript/constants.txt',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
            ],
            ['example.local' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        FrontendSimulatorUtility::simulateFrontendEnvironment(3);

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(3);

        self::assertStringContainsString('<meta name="description" content="Subpage Description" />', $response->getContent());

        FrontendSimulatorUtility::resetFrontendEnvironment();
    }


    /**
     * @test
     * @throws \Exception
     */
    public function itSetsKeywords ()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has keywords set
         * Given a subpage of this page
         * Given this subpage has keywords set
         * When the method is called on that page
         * Then the keywords of the subpage are set
         */
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check50.xml');

        $this->setUpFrontendRootPage(
            3,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.txt',
                'EXT:core_extended/Configuration/TypoScript/constants.txt',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
            ],
            ['example.local' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        FrontendSimulatorUtility::simulateFrontendEnvironment(3);

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(3);

        self::assertStringContainsString('<meta name="keywords" content="Subpage, Keywords" />', $response->getContent());

        FrontendSimulatorUtility::resetFrontendEnvironment();

    }


    /**
     * @test
     * @throws \Exception
     */
    public function itSetsDescriptionAndKeywords()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has a description set
         * Given this page has keywords set
         * Given a subpage of this page
         * Given this subpage has a description set
         * Given this subpage has keywords set
         * When the method is called on that subpage
         * Then the description of the subpage is set
         * Then the keywords of the subpage are set
         */
        $this->importDataSet(self::FIXTURE_PATH . '/Database/Check60.xml');

        $this->setUpFrontendRootPage(
            3,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.txt',
                'EXT:core_extended/Configuration/TypoScript/constants.txt',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
            ],
            ['example.local' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        FrontendSimulatorUtility::simulateFrontendEnvironment(3);

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(3);

        self::assertStringContainsString('<meta name="description" content="Subpage Description" />', $response->getContent());
        self::assertStringContainsString('<meta name="keywords" content="Subpage, Keywords" />', $response->getContent());

        FrontendSimulatorUtility::resetFrontendEnvironment();

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
