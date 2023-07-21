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
use Madj2k\CoreExtended\Utility\FrontendSimulatorUtility;
use Madj2k\CoreExtended\Utility\ClientUtility;

/**
 * ClientUtilityTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ClientUtilityTest extends FunctionalTestCase
{
    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/ClientUtilityTest/Fixtures';


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

        FrontendSimulatorUtility::simulateFrontendEnvironment(1);

    }

    #==============================================================================

    /**
     * @test
     */
    public function getIpReturnsLocalHost ()
    {
        /**
         * Scenario:
         *
         * Given a request without a proxy
         * Given no remote-address is set
         * When the method is called
         * Then localhost is returned
         */

        self::assertEquals('127.0.0.1', ClientUtility::getIp());
    }


    /**
     * @test
     */
    public function getIpReturnsClientIp ()
    {
        /**
         * Scenario:
         *
         * Given a request without a proxy
         * Given $_SERVER['REMOTE_ADDR'] is set
         * When the method is called
         * Then the remote-address is returned
         */

        $_SERVER['REMOTE_ADDR'] = '1.1.2.1';
        self::assertEquals('1.1.2.1', ClientUtility::getIp());
    }


    /**
     * @test
     */
    public function getIpReturnsClientIpWithProxy ()
    {
        /**
         * Scenario:
         *
         * Given a request with a proxy
         * Given $_SERVER['HTTP_X_FORWARDED_FOR'] is set
         * When the method is called
         * Then the first IP in $_SERVER['HTTP_X_FORWARDED_FOR']  is returned
         */

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.1.2.1, 2.2.1.2, 3.3.2.3';
        self::assertEquals('1.1.2.1', ClientUtility::getIp());
    }

    #==============================================================================

    /**
     * @test
     */
    public function getClientHashReturnsMd5 ()
    {
        /**
         * Scenario:
         *
         * Given REMOTE_ADDR is set in globalVar $_SERVER
         * Given HTTP_USER_AGENT is set in globalVar $_SERVER
         * When the method is called
         * Then a md5-value is returned that consists of both globalVar-settings
         */

        $_SERVER['REMOTE_ADDR'] = '1.1.2.1';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7) Gecko/20040803 Firefox/0.9.3';

        $expected = md5($_SERVER['REMOTE_ADDR'] . '-' . $_SERVER['HTTP_USER_AGENT']);

        self::assertEquals($expected, ClientUtility::getClientHash());
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
