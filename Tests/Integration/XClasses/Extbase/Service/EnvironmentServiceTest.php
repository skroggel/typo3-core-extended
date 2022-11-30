<?php
namespace Madj2k\CoreExtended\Tests\Integration\XClasses\Extbase\Service;

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
use Madj2k\CoreExtended\Domain\Repository\PagesRepository;
use Madj2k\CoreExtended\Utility\FrontendSimulatorUtility;
use RKW\RkwMailer\Domain\Model\QueueMail;
use RKW\RkwMailer\Domain\Model\QueueRecipient;
use RKW\RkwMailer\View\MailStandaloneView;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\EnvironmentService;


/**
 * EnvironmentServiceTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwMailer
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EnvironmentServiceTest extends FunctionalTestCase
{

    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/EnvironmentServiceTest/Fixtures';


    /**
     * @var string[]
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/core_extended',
    ];

    /**
     * @var string[]
     */
    protected $coreExtensionsToLoad = [];


    /**
     * @var \Madj2k\CoreExtended\XClasses\Extbase\Service\EnvironmentService
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

        $this->importDataSet(self::FIXTURE_PATH . '/Database/Global.xml');
        $this->setUpFrontendRootPage(
            1,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.typoscript',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
            ]
        );

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $this->objectManager */
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->subject = $this->objectManager->get(\Madj2k\CoreExtended\XClasses\Extbase\Service\EnvironmentService::class);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function isEnvironmentInFrontendModeReturnsFalse ()
    {

        /**
        * Scenario:
        *
        * Given we are in BE-context
        * When the method is called
        * Then false is returned
        */

        self::assertFalse($this->subject->isEnvironmentInFrontendMode());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function isEnvironmentInFrontendModeReturnsTrueIfSimulatedFrontend ()
    {

        /**
         * Scenario:
         *
         * Given we are in BE-context
         * Given we simulate a frontend-context
         * When the method is called
         * Then true is returned
         */

        FrontendSimulatorUtility::simulateFrontendEnvironment(1);
        self::assertTrue($this->subject->isEnvironmentInFrontendMode());
    }

    //=============================================


    /**
     * @test
     * @throws \Exception
     */
    public function isEnvironmentInBackendModeReturnsTrue ()
    {

        /**
         * Scenario:
         *
         * Given we are in BE-context
         * When the method is called
         * Then true is returned
         */

        self::assertTrue($this->subject->isEnvironmentInBackendMode());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function isEnvironmentInBackendModeReturnsFalseIfSimulatedFrontend ()
    {

        /**
         * Scenario:
         *
         * Given we are in BE-context
         * Given we simulate a frontend-context
         * When the method is called
         * Then false is returned
         */

        FrontendSimulatorUtility::simulateFrontendEnvironment(1);
        self::assertFalse($this->subject->isEnvironmentInBackendMode());
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
