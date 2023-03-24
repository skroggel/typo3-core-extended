<?php
namespace Madj2k\CoreExtended\Tests\Integration\ExpressionLanguage;

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

/**
 * BackendLayoutProviderTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendLayoutProviderTest extends FunctionalTestCase
{

    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/BackendLayoutProviderTest/Fixtures';


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
     * Setup
     * @throws \Exception
     */
    protected function setUp(): void
    {

        parent::setUp();

        $this->importDataSet(self::FIXTURE_PATH . '/Database/Global.xml');


    }

    //=============================================

    /**
     * @test
     * @throws \Exception
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     */
    public function itReturnsEmptyIfColPosBelowZero ()
    {

        /**
         * Scenario:
         *
         * Given the TypoScript-condition is used
         * Given the current page has a backend_layout-value "pagets__homePages" set
         * Given colPos is set to -1
         * When the condition is evaluated
         * Then "empty" is returned
         */
        $this->setUpFrontendRootPage(
            1,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.typoscript',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
                self::FIXTURE_PATH . '/Frontend/Configuration/Check10.typoscript',
            ],
            ['example.com' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        $_GET['colPos'] = -1;

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(1);

        self::assertStringNotContainsString('empty', $response->getContent());
    }

    /**
     * @test
     * @throws \Exception
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     */
    public function itReturnsBackendLayoutOfCurrentPageIfBackendLayoutNextLevel ()
    {

        /**
         * Scenario:
         *
         * Given the TypoScript-condition is used
         * Given the current page has a backend_layout-value "pagets__homePages" set
         * Given the current has with the backend_layout_next_level-value "pagets__subPage" set
         * When the condition is evaluated
         * Then the "pagets__homePages" is returned
         */
        $this->setUpFrontendRootPage(
            1,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.typoscript',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
                self::FIXTURE_PATH . '/Frontend/Configuration/Check10.typoscript',
            ],
            ['example.com' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(1);

        self::assertStringContainsString('pagets__homePages', $response->getContent());
    }



    /**
     * @test
     * @throws \Exception
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     */
    public function itReturnsBackendLayoutOfCurrentPageIfNoBackendLayoutNextLevel ()
    {

        /**
        * Scenario:
        *
        * Given the TypoScript-condition is used
        * Given the current page has the backend_layout-value "pagets__specialPage" set
        * When the condition is evaluated
        * Then  "pagets__specialPage" is returned
        */
        $this->setUpFrontendRootPage(
            2,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.typoscript',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
                self::FIXTURE_PATH . '/Frontend/Configuration/Check10.typoscript',
            ],
            ['example.com' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(2);

        self::assertStringContainsString('pagets__specialPage', $response->getContent());
    }



    /**
     * @test
     * @throws \Exception
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     */
    public function itReturnsInheritedBackendLayoutNextLevel ()
    {

        /**
         * Scenario:
         *
         * Given the TypoScript-condition is used
         * Given the current page has no backend_layout set
         * Given the parent page has a backend_layout set
         * Given the rootpage has with the backend_layout_next_level-value "pagets__subPage" set
         * When the condition is evaluated
         * Then the "pagets__subPage" is returned
         */
        $this->setUpFrontendRootPage(
            3,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.typoscript',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
                self::FIXTURE_PATH . '/Frontend/Configuration/Check10.typoscript',
            ],
            ['example.com' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
        );

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(3);

        self::assertStringContainsString('pagets__subPage', $response->getContent());
    }




    //=============================================

    /**
     * TearDown
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }








}
