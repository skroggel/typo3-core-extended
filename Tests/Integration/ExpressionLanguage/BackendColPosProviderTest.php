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

use Nimut\TestingFramework\Http\Response;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use PHPUnit\Util\PHP\DefaultPhpProcess;
use SebastianBergmann\Template\Template;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Frontend\Page\CacheHashCalculator;

/**
 * BackendColPosProviderTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendColPosProviderTest extends FunctionalTestCase
{

    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/BackendColPosProviderTest/Fixtures';


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
     * @var string
     */
    protected string $frontendResponseParams = '';


    /**
     * Setup
     * @throws \Exception
     */
    protected function setUp(): void
    {

        parent::setUp();

        // do not validate cHash because we need to add some parameters for testing
        $GLOBALS['TYPO3_CONF_VARS']['FE']['disableNoCacheParameter'] = true;
        $GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFoundOnCHashError'] = -1;

        $this->importDataSet(self::FIXTURE_PATH . '/Database/Global.xml');


    }

    //=============================================

    /**
     * @test
     * @throws \Exception
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     */
    public function itReturnsColPosFromGetParamsIfNoEditParam ()
    {

        /**
         * Scenario:
         *
         * Given the TypoScript-condition is used
         * Given _GET-parameter "colPos" is set
         * Given no _GET-parameter "edit" is set
         * When the condition is evaluated
         * Then the value of _GET[colPos] "111" is returned
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

        $this->frontendResponseParams ='colPos=111';

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(1);

        self::assertStringContainsString('111', $response->getContent());
    }


    /**
     * @test
     * @throws \Exception
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     */
    public function itReturnsColPosFromTtContentElementIfEditParam ()
    {

        /**
         * Scenario:
         *
         * Given the TypoScript-condition is used
         * Given no _GET-parameter "colPos" is set
         * Given the _GET-parameter "edit" is set as array [edit][tt_content][1]
         * When the condition is evaluated
         * Then the colPos of the tt_content-element "222" is returned
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

        $this->frontendResponseParams = 'edit[tt_content][1]=edit';

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(1);

        self::assertStringContainsString('222', $response->getContent());
    }


    /**
     * @test
     * @throws \Exception
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     */
    public function itReturnsEmptyIfNoColPosAndNoEditParam ()
    {

        /**
         * Scenario:
         *
         * Given the TypoScript-condition is used
         * Given no _GET-parameter "colPos" is set
         * Given no _GET-parameter "edit" is set
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

        /** @var \Nimut\TestingFramework\Http\Response $response */
        $response = $this->getFrontendResponse(1);

        self::assertStringContainsString('empty', $response->getContent());


    }

    //=============================================

    /**
     * TearDown
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    //=============================================

    /**
     * @param int $pageId
     * @param int $languageId
     * @param int $backendUserId
     * @param int $workspaceId
     * @param bool $failOnFailure
     * @param int $frontendUserId
     * @return Response
     */
    protected function getFrontendResponse($pageId, $languageId = 0, $backendUserId = 0, $workspaceId = 0, $failOnFailure = true, $frontendUserId = 0)
    {
        $pageId = (int)$pageId;
        $languageId = (int)$languageId;

        $additionalParameter = '';

        if (!empty($frontendUserId)) {
            $additionalParameter .= '&frontendUserId=' . (int)$frontendUserId;
        }
        if (!empty($backendUserId)) {
            $additionalParameter .= '&backendUserId=' . (int)$backendUserId;
        }
        if (!empty($workspaceId)) {
            $additionalParameter .= '&workspaceId=' . (int)$workspaceId;
        }

        if ($this->frontendResponseParams) {
            $additionalParameter .= '&' . $this->frontendResponseParams;
        }

        $queryString = 'id=' . $pageId . '&L=' . $languageId . $additionalParameter;

        /** Since TYPO3 10 we have to add a valid cHash, so we need to generate it */
        /** @var \TYPO3\CMS\Frontend\Page\CacheHashCalculator $cacheHashCalculator */
        $cacheHashCalculator = GeneralUtility::makeInstance(CacheHashCalculator::class);
        $relevantParameters = $cacheHashCalculator->getRelevantParameters($queryString);
        $calculatedCacheHash = $cacheHashCalculator->calculateCacheHash($relevantParameters);

        $arguments = [
            'documentRoot' => $this->getInstancePath(),
            'requestUrl' => 'http://localhost/?' . $queryString . '&cHash=' . $calculatedCacheHash,
        ];

        $textTemplateClass = class_exists(Template::class) ? Template::class : \Text_Template::class;
        $template = new $textTemplateClass('ntf://Frontend/Request.tpl');
        $template->setVar(
            [
                'arguments' => var_export($arguments, true),
                'originalRoot' => ORIGINAL_ROOT,
                'ntfRoot' => ORIGINAL_ROOT . '/../vendor/nimut/testing-framework/'
            ]
        );

        $php = DefaultPhpProcess::factory();
        $response = $php->runJob($template->render());
        $result = json_decode($response['stdout'], true);

        if ($result === null) {
            $this->fail('Frontend Response is empty.' . LF . 'Error: ' . LF . $response['stderr']);
        }

        if ($failOnFailure && $result['status'] === Response::STATUS_Failure) {
            $this->fail('Frontend Response has failure:' . LF . $result['error']);
        }

        $response = new Response($result['status'], $result['content'], $result['error']);

        return $response;
    }







}
