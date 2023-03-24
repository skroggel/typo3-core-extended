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
 * RobotsTagGeneratorTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RobotsTagGeneratorTest extends FunctionalTestCase
{
    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/RobotsTagGeneratorTest/Fixtures';


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
    public function itInheritsNoIndex ()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has no_index = 1 set
         * Given a subpage of this page
         * When the method is called on that subpage
         * Then "noindex" is set as robots-tag
         * Then "follow" is set as robots-tag
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

        self::assertStringContainsString('<meta name="robots" content="noindex,follow,noodp,noydir" />', $response->getContent());

        FrontendSimulatorUtility::resetFrontendEnvironment();

    }


    /**
     * @test
     * @throws \Exception
     */
    public function itInheritsNoFollow ()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has no_follow = 1 set
         * Given a subpage of this page
         * When the method is called on that subpage
         * Then "index" is set as robots-tag
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

        self::assertStringContainsString('<meta name="robots" content="index,nofollow,noodp,noydir" />', $response->getContent());

        FrontendSimulatorUtility::resetFrontendEnvironment();
    }


    /**
     * @test
     * @throws \Exception
     */
    public function itInheritsNoIndexNoFollow ()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has no_index = 1 set
         * Given this page has no_follow = 1 set
         * Given a subpage of this page
         * When the method is called on that subpage
         * Then "noindex" is set as robots-tag
         * Then "nofollow" is set as robots-tag
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

        self::assertStringContainsString('<meta name="robots" content="noindex,nofollow,noodp,noydir" />', $response->getContent());

        FrontendSimulatorUtility::resetFrontendEnvironment();
    }


    /**
     * @test
     * @throws \Exception
     */
    public function itSetsNoIndex ()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has no_index = 1 set
         * When the method is called on that page
         * Then "noindex" is set as robots-tag
         * Then "follow" is set as robots-tag
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

        self::assertStringContainsString('<meta name="robots" content="noindex,follow,noodp,noydir" />', $response->getContent());

        FrontendSimulatorUtility::resetFrontendEnvironment();
    }


    /**
     * @test
     * @throws \Exception
     */
    public function itSetNoFollow ()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has no_follow = 1 set
         * When the method is called on that page
         * Then "index" is set as robots-tag
         * Then "nofollow" is set as robots-tag
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

        self::assertStringContainsString('<meta name="robots" content="index,nofollow,noodp,noydir" />', $response->getContent());

        FrontendSimulatorUtility::resetFrontendEnvironment();
    }


    /**
     * @test
     * @throws \Exception
     */
    public function itSetsNoIndexNoFollow ()
    {
        /**
         * Scenario:
         *
         * Given a page
         * Given this page has no_index = 1 set
         * Given this page has no_follow = 1 set
         * When the method is called on that page
         * Then "noindex" is set as robots-tag
         * Then "nofollow" is set as robots-tag
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

        self::assertStringContainsString('<meta name="robots" content="noindex,nofollow,noodp,noydir" />', $response->getContent());

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
