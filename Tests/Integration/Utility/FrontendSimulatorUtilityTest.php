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
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * FrontendSimulatorUtilityTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FrontendSimulatorUtilityTest extends FunctionalTestCase
{

    const BASE_PATH = __DIR__ . '/FrontendSimulatorUtilityTest';
    const REL_BASE_PATH = 'EXT:core_extended/Tests/Integration/Utility/FrontendSimulatorUtilityTest';

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

        $this->importDataSet(self::BASE_PATH . '/Fixtures/Database/Global.xml');
        $this->setUpFrontendRootPage(
            1,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.txt',
                self::REL_BASE_PATH  .'/Fixtures/Frontend/Configuration/Rootpage.typoscript',
            ],
            [1 => self::REL_BASE_PATH  .'/Fixtures/Sites/config.yaml']
        );
        $this->setUpFrontendRootPage(
            11,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.txt',
                self::REL_BASE_PATH  .'/Fixtures/Frontend/Configuration/Rootpage.typoscript',
            ],
            [11 => self::REL_BASE_PATH  .'/Fixtures/Sites/config10.yaml']
        );
        $this->setUpFrontendRootPage(
            21,
            [
                'EXT:core_extended/Configuration/TypoScript/setup.txt',
                self::REL_BASE_PATH  .'/Fixtures/Frontend/Configuration/Rootpage.typoscript',
            ],
            [11 => self::REL_BASE_PATH  .'/Fixtures/Sites/config.yaml']
        );

    }



    //=============================================

    /**
     * @test
     */
    public function simulateFrontendEnvironmentRemovesPageNotFoundHandler()
    {

        /**
         * Scenario:
         *
         * Given a sub-page in the rootline
         * When the method is called
         * Then the method returns the value 1
         * Then the page-not-found-handler is deleted
         */

        $GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling'] = 'Test';
        self::assertEquals(1, FrontendSimulatorUtility::simulateFrontendEnvironment(3));
        self::assertEmpty($GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling']);
    }


    /**
     * @test
     */
    public function simulateFrontendEnvironmentSetsHostVariableToDomainOfRootpage()
    {

        /**
         * Scenario:
         *
         * Given a sub-page in the rootline
         * When the method is called
         * Then the method returns the value 1
         * Then the host-environment-variable is set to the domain of the rootpage
         * Then the environment-caches are flushed
         */

        // set variable an fill caches
        $_SERVER['HTTP_HOST'] = 'example.com';
        GeneralUtility::getIndpEnv('HTTP_HOST');

        self::assertEquals(1, FrontendSimulatorUtility::simulateFrontendEnvironment(3));
        self::assertEquals('www.rkw-kompetenzzentrum.rkw.local', $_SERVER['HTTP_HOST']);
        self::assertEquals('www.rkw-kompetenzzentrum.rkw.local',  GeneralUtility::getIndpEnv('HTTP_HOST'));

    }


    /**
     * @test
     */
    public function simulateFrontendEnvironmentSetsGetAndPostId()
    {

        /**
         * Scenario:
         *
         * Given a sub-page in the rootline
         * When the method is called
         * Then the method returns the value 1
         * Then the key 'id' of _GET and _POST is set to the id of the given sub-page
         */

        // set variables
        $_GET['id'] = $_POST['id'] = 99;

        self::assertEquals(1, FrontendSimulatorUtility::simulateFrontendEnvironment(3));
        self::assertEquals(3, $_GET['id']);
        self::assertEquals(3, $_POST['id']);

    }

    /**
     * @test
     */
    public function simulateFrontendEnvironmentSetsBaseUrlInConfiguration()
    {

        /**
         * Scenario:
         *
         * Given a sub-page in the rootline
         * When the method is called
         * Then the method returns the value 1
         * Then a TyposcriptFrontendController-Object is generated
         * Then this TyposcriptFrontendController-Object contains a config-key 'absRefPrefix' which is set to '/'
         * Then this TyposcriptFrontendController-Object contains a config-key 'baseURL' which is set to the domain of the given sub-page
         * Then this TyposcriptFrontendController-Object contains a property 'absRefPrefix' which is set to '/'
         */
        self::assertEquals(1, FrontendSimulatorUtility::simulateFrontendEnvironment(3));
        self::assertInstanceOf(\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class, $GLOBALS['TSFE']);

        self::assertEquals('/', $GLOBALS['TSFE']->config['config']['absRefPrefix']);
        self::assertEquals('www.rkw-kompetenzzentrum.rkw.local', $GLOBALS['TSFE']->config['config']['baseURL']);
        self::assertEquals('/', $GLOBALS['TSFE']->absRefPrefix);

    }


    /**
     * @test
     */
    public function simulateFrontendEnvironmentGeneratesCompleteFrontendObject()
    {

        /**
         * Scenario:
         *
         * Given a sub-page in the rootline
         * When the method is called
         * Then the method returns the value 1
         * Then a TyposcriptFrontendController-Object is generated
         * Then this TyposcriptFrontendController-Object has the property 'id' set to the given sub-page-id
         * Then this TyposcriptFrontendController-Object has the property 'rootline' set to an array
         * Then this array has three items
         * Then this three items are the root-page, the parent-page and the given sub-page in that order
         * Then this TyposcriptFrontendController-Object has the property 'page' set to an array
         * Then this array has the key 'id' with the id of the given sub-page
         * Then this array has the key 'title' with the title of the given sub-page
         * Then this TyposcriptFrontendController-Object has the property 'domainStartPage' set to root-page id (=1)
         * Then this TyposcriptFrontendController-Object has the property 'sys_language_uid' / language-aspect set to zero
         * Then this TyposcriptFrontendController-Object has the property 'pageNotFound' set to zero
         * Then this TyposcriptFrontendController-Object has the property 'sys_page' set to a TYPO3\CMS\Frontend\Page\PageRepository-object
         * Then this TyposcriptFrontendController-Object has the property 'fe_user' set to a TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication-object
         * Then this TyposcriptFrontendController-Object has the property 'tmpl' set to a TYPO3\CMS\Core\TypoScript\TemplateService-object
         */

        self::assertEquals(1, FrontendSimulatorUtility::simulateFrontendEnvironment(3));
        self::assertInstanceOf(\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class, $GLOBALS['TSFE']);
        self::assertEquals(3, $GLOBALS['TSFE']->id);
        self::assertIsArray($GLOBALS['TSFE']->rootLine);

        $rootline = $GLOBALS['TSFE']->rootLine;
        self::assertCount(3, $rootline);
        self::assertEquals(1, $rootline[0]['uid']);
        self::assertEquals(2, $rootline[1]['uid']);
        self::assertEquals(3, $rootline[2]['uid']);

        self::assertEquals(3, $GLOBALS['TSFE']->page['uid']);
        self::assertEquals('Test-Sub-Page', $GLOBALS['TSFE']->page['title']);

        //  self::assertEquals(1, $GLOBALS['TSFE']->domainStartPage);

        /** @deprecated since TYPO3 9.x - instead we use the Aspect-Version below */
        self::assertEquals(0,$GLOBALS['TSFE']->sys_language_uid);

        self::assertEquals(0, $GLOBALS['TSFE']->pageNotFound);

        self::assertInstanceOf(\TYPO3\CMS\Frontend\Page\PageRepository::class, $GLOBALS['TSFE']->sys_page);
        self::assertInstanceOf(\TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication::class, $GLOBALS['TSFE']->fe_user);
        self::assertInstanceOf(\TYPO3\CMS\Core\TypoScript\TemplateService::class, $GLOBALS['TSFE']->tmpl);

    }

    /**
     * @test
     */
    public function simulateFrontendEnvironmentForAPageWithUsergroupGeneratesCompleteFrontendObject()
    {

        /**
         * Scenario:
         *
         * Given a sub-page in the rootline
         * Given that subpage is only accessable for a usergroup
         * Given that usergroup is persisted
         * When the method is called
         * Then the method returns the value 1
         * Then a TyposcriptFrontendController-Object is generated
         * Then this TyposcriptFrontendController-Object has the property 'id' set to the given sub-page-id
         * Then this TyposcriptFrontendController-Object has the property 'rootline' set to an array
         * Then this array has three items
         * Then this three items are the root-page, the parent-page and the given sub-page in that order
         * Then this TyposcriptFrontendController-Object has the property 'page' set to an array
         * Then this array has the key 'id' with the id of the given sub-page
         * Then this array has the key 'title' with the title of the given sub-page
         * Then this TyposcriptFrontendController-Object has the property 'domainStartPage' set to root-page id (=1)
         * Then this TyposcriptFrontendController-Object has the property 'sys_language_uid' / language-aspect set to zero
         * Then this TyposcriptFrontendController-Object has the property 'pageNotFound' set to zero
         * Then this TyposcriptFrontendController-Object has the property 'sys_page' set to a TYPO3\CMS\Frontend\Page\PageRepository-object
         * Then this TyposcriptFrontendController-Object has the property 'fe_user' set to a TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication-object
         * Then this TyposcriptFrontendController-Object has the property 'tmpl' set to a TYPO3\CMS\Core\TypoScript\TemplateService-object
         */

        self::assertEquals(1, FrontendSimulatorUtility::simulateFrontendEnvironment(21));
        self::assertInstanceOf(\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class, $GLOBALS['TSFE']);
        self::assertEquals(21, $GLOBALS['TSFE']->id);
        self::assertIsArray($GLOBALS['TSFE']->rootLine);

        $rootline = $GLOBALS['TSFE']->rootLine;
        self::assertCount(2, $rootline);
        self::assertEquals(20, $rootline[0]['uid']);
        self::assertEquals(21, $rootline[1]['uid']);

        self::assertEquals(21, $GLOBALS['TSFE']->page['uid']);
        self::assertEquals('Test-Sub-Page', $GLOBALS['TSFE']->page['title']);

        //  self::assertEquals(1, $GLOBALS['TSFE']->domainStartPage);

        /** @deprecated since TYPO3 9.x - instead we use the Aspect-Version below */
        self::assertEquals(0,$GLOBALS['TSFE']->sys_language_uid);

        self::assertEquals(0, $GLOBALS['TSFE']->pageNotFound);

        self::assertInstanceOf(\TYPO3\CMS\Frontend\Page\PageRepository::class, $GLOBALS['TSFE']->sys_page);
        self::assertInstanceOf(\TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication::class, $GLOBALS['TSFE']->fe_user);
        self::assertInstanceOf(\TYPO3\CMS\Core\TypoScript\TemplateService::class, $GLOBALS['TSFE']->tmpl);

    }


    /**
     * @test
     */
    public function simulateFrontendEnvironmentGeneratesAspectObjects()
    {

        /**
         * Scenario:
         *
         * Given a sub-page in the rootline
         * When the method is called
         * Then the method returns the value 1
         * Then a new dateTimeAspect is generated that is not identical to the original one
         * Then this object has the current time set
         * Then a new visibilityAspect is generated that is not identical to the original one
         * Then a new languageAspect is generated that is not identical to the original one
         * Then this object has the language uid zero
         * Then a new UserAspect for the frontendUser is generated that is not identical to the original one
         * Then this object has the uid zero
         * Then a new UserAspect for the backendUser is generated that is not identical to the original one
         * Then this object has the uid zero
         * Then a new workspaceAspect is generated that is not identical to the original one
         */

        $beforeDateTimeAspect = GeneralUtility::makeInstance(Context::class)->getAspect('date');
        $beforeVisibilityAspect = GeneralUtility::makeInstance(Context::class)->getAspect('visibility');
        $beforeLanguageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $beforeFrontendUserAspect = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user');
        $beforeBackendUserAspect = GeneralUtility::makeInstance(Context::class)->getAspect('backend.user');
        $beforeWorkspaceAspect = GeneralUtility::makeInstance(Context::class)->getAspect('workspace');

        self::assertEquals(1, FrontendSimulatorUtility::simulateFrontendEnvironment(3));

        /** @var \TYPO3\CMS\Core\Context\DateTimeAspect $dateTimeAspect */
        $dateTimeAspect = GeneralUtility::makeInstance(Context::class)->getAspect('date');
        self::assertNotSame($beforeDateTimeAspect, $dateTimeAspect);
        self::assertGreaterThanOrEqual(time()-5, $dateTimeAspect->get('timestamp'));

        self::assertNotSame($beforeVisibilityAspect, GeneralUtility::makeInstance(Context::class)->getAspect('visibility'));

        /** @var \TYPO3\CMS\Core\Context\LanguageAspect $languageAspect */
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        self::assertNotSame($beforeLanguageAspect, $languageAspect);
        self::assertEquals(0, $languageAspect->get('id'));

        /** @var \TYPO3\CMS\Core\Context\UserAspect $frontendUserAspect */
        $frontendUserAspect = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user');
        self::assertNotSame($beforeFrontendUserAspect, $frontendUserAspect);
        self::assertEquals(0, $frontendUserAspect->get('id'));

        /** @var \TYPO3\CMS\Core\Context\UserAspect $backendUserAspect */
        $backendUserAspect = GeneralUtility::makeInstance(Context::class)->getAspect('backend.user');
        self::assertNotSame($beforeBackendUserAspect, $backendUserAspect);
        self::assertEquals(0, $backendUserAspect->get('id'));

        self::assertNotSame($beforeWorkspaceAspect, GeneralUtility::makeInstance(Context::class)->getAspect('workspace'));
    }


    /**
     * @test
     */
    public function simulateFrontendEnvironmentLoadsDataFromCache()
    {

        /**
         * Scenario:
         *
         * Given the method was called for sub-page A in the rootline
         * Given the method was then called for sub-page B in the rootline
         * Given the method has set a new dateTimeAspect that is not the same as the original one
         * Given the method has set a new visibilityAspect that is not the same as the original one
         * Given the method has set a new languageAspect that is not the same as the original one
         * Given the method has set a new userAspect for the frontendUser that is not the same as the original one
         * Given the method has set a new userAspect for the backendUser that is not the same as the original one
         * Given the method has set a new workspaceAspect that is not the same as the original one
         * When the method is called for sub-page A again
         * Then the method returns the value 2
         * Then a TyposcriptFrontendController-Object is identical with the one generated the first time
         * Then the _TYPO3_CONF_VARS is identical with the one generated the first time
         * Then the _SERVER-superglobal is identical with the one generated the first time
         * Then the _POST-superglobal is identical with the one generated the first time
         * Then the _GET-superglobal is identical with the one generated the first time
         * Then the environment-cache is flushed and thus identical with the one generated the first time
         * Then the dateAspect is identical with the one generated the first time
         * Then the visibilityAspect is identical with the one generated the first time
         * Then the languageAspect is identical with the one generated the first time
         * Then the UserAspect for the frontendUser is identical with the one generated the first time
         * Then the UserAspect for the backendUser is identical with the one generated the first time
         * Then the WorkspaceAspect is identical with the one generated the first time
         */

        FrontendSimulatorUtility::simulateFrontendEnvironment(3);

        $beforeTSFE = $GLOBALS['TSFE'];
        $beforeConfVars = $GLOBALS['TYPO3_CONF_VARS'];
        $beforeGET = $_GET;
        $beforePOST = $_POST;
        $beforeSERVER = $_SERVER;
        $beforeEnvironmentCache = GeneralUtility::getIndpEnv('HTTP_HOST');
        $beforeDateTimeAspect = GeneralUtility::makeInstance(Context::class)->getAspect('date');
        $beforeVisibilityAspect = GeneralUtility::makeInstance(Context::class)->getAspect('visibility');
        $beforeLanguageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $beforeFrontendUserAspect = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user');
        $beforeBackendUserAspect = GeneralUtility::makeInstance(Context::class)->getAspect('backend.user');
        $beforeWorkspaceAspect = GeneralUtility::makeInstance(Context::class)->getAspect('workspace');


        FrontendSimulatorUtility::simulateFrontendEnvironment(11);

        self::assertNotEquals($beforeTSFE, $GLOBALS['TSFE']);
        self::assertNotEquals($beforeGET, $_GET);
        self::assertNotEquals($beforePOST, $_POST);
        self::assertNotEquals($beforeEnvironmentCache, GeneralUtility::getIndpEnv('HTTP_HOST'));
        self::assertNotSame($beforeDateTimeAspect, GeneralUtility::makeInstance(Context::class)->getAspect('date'));
        self::assertNotSame($beforeVisibilityAspect, GeneralUtility::makeInstance(Context::class)->getAspect('visibility'));
        self::assertNotSame($beforeLanguageAspect, GeneralUtility::makeInstance(Context::class)->getAspect('language'));
        self::assertNotSame($beforeFrontendUserAspect, GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user'));
        self::assertNotSame($beforeBackendUserAspect, GeneralUtility::makeInstance(Context::class)->getAspect('backend.user'));
        self::assertNotSame($beforeWorkspaceAspect, GeneralUtility::makeInstance(Context::class)->getAspect('workspace'));

        self::assertEquals(2, FrontendSimulatorUtility::simulateFrontendEnvironment(3));

        self::assertEquals($beforeTSFE, $GLOBALS['TSFE']);
        self::assertEquals($beforeConfVars, $GLOBALS['TYPO3_CONF_VARS']);
        self::assertEquals($beforeGET, $_GET);
        self::assertEquals($beforePOST, $_POST);
        self::assertEquals($beforeSERVER, $_SERVER);
        self::assertEquals($beforeEnvironmentCache,  GeneralUtility::getIndpEnv('HTTP_HOST'));
        self::assertSame($beforeDateTimeAspect, GeneralUtility::makeInstance(Context::class)->getAspect('date'));
        self::assertSame($beforeVisibilityAspect, GeneralUtility::makeInstance(Context::class)->getAspect('visibility'));
        self::assertSame($beforeLanguageAspect, GeneralUtility::makeInstance(Context::class)->getAspect('language'));
        self::assertSame($beforeFrontendUserAspect, GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user'));
        self::assertSame($beforeBackendUserAspect, GeneralUtility::makeInstance(Context::class)->getAspect('backend.user'));
        self::assertSame($beforeWorkspaceAspect, GeneralUtility::makeInstance(Context::class)->getAspect('workspace'));

    }

    /**
     * @test
     */
    public function simulateFrontendEnvironmentSetsFrontendConfigurationManager()
    {

        /**
         * Scenario:
         *
         * Given a sub-page in the rootline
         * When the method is called
         * Then the method returns the value 1
         * Then the Typoscript-configuration for frontend is available via configurationManager
         */

        self::assertEquals(1, FrontendSimulatorUtility::simulateFrontendEnvironment(3));

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);

        $settings = $configurationManager->getConfiguration($configurationManager::CONFIGURATION_TYPE_SETTINGS, 'rkwBasics');
        self::assertEquals(1, $settings['frontendContext']);
    }

    /**
     * @test
     */
    public function simulateFrontendEnvironmentSetsContentObjectRenderer ()
    {

        /**
         * Scenario:
         *
         * Given a sub-page in the rootline
         * Given the configurationManager has no contentObjectRenderer-object
         * When the method is called
         * Then the method returns the value 1
         * Then the configurationManager has a contentObjectRenderer-object
         */

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);

        self::assertEmpty($configurationManager->getContentObject());
        self::assertEquals(1, FrontendSimulatorUtility::simulateFrontendEnvironment(3));

        self::assertNotEmpty($configurationManager->getContentObject());

    }


    //=============================================

    /**
     * @test
     */
    public function resetFrontendEnvironmentRestoresPageNotFoundHandler()
    {

        /**
         * Scenario:
         *
         * Given we were in FE-Mode
         * Given a sub-page in the rootline
         * Given simulateFrontendEnvironment was called before
         * When the method is called
         * Then the method returns true
         * Then the page-not-found-handler is restored
         */

        $GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling'] = 'Test';

        FrontendSimulatorUtility::simulateFrontendEnvironment(3);
        self::assertTrue(FrontendSimulatorUtility::resetFrontendEnvironment());
        self::assertEquals('Test', $GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling']);
    }


    /**
     * @test
     */
    public function resetFrontendEnvironmentRestoresHostVariable()
    {

        /**
         * Scenario:
         *
         * Given we were in FE-Mode
         * Given a sub-page in the rootline
         * Given simulateFrontendEnvironment was called before
         * When the method is called
         * Then the method returns true
         * Then the host-environment-variable is restored
         * Then the environment-caches are flushed
         */

        // set variable an fill caches
        $_SERVER['HTTP_HOST'] = 'example.com';
        GeneralUtility::getIndpEnv('HTTP_HOST');

        FrontendSimulatorUtility::simulateFrontendEnvironment(3);
        self::assertTrue(FrontendSimulatorUtility::resetFrontendEnvironment());
        self::assertEquals('example.com', $_SERVER['HTTP_HOST']);
        self::assertEquals('example.com',  GeneralUtility::getIndpEnv('HTTP_HOST'));

    }



    /**
     * @test
     */
    public function resetFrontendEnvironmentRestoresGetAndPostId()
    {

        /**
         * Scenario:
         *
         * Given we were in FE-Mode
         * Given a sub-page in the rootline
         * Given simulateFrontendEnvironment was called before
         * When the method is called
         * Then the method returns true
         * Then the key 'id' of _GET and _POST is restored
         */

        // set variable an fill caches
        $_GET['id'] = $_POST['id'] = 99;

        FrontendSimulatorUtility::simulateFrontendEnvironment(3);
        self::assertTrue(FrontendSimulatorUtility::resetFrontendEnvironment());
        self::assertEquals(99, $_GET['id']);
        self::assertEquals(99, $_POST['id']);

    }


    /**
     * @test
     */
    public function resetFrontendEnvironmentRestoresFrontendObject()
    {

        /**
         * Scenario:
         *
         * Given we were in FE-Mode
         * Given a sub-page in the rootline
         * Given simulateFrontendEnvironment was called before
         * When the method is called
         * Then the method returns true
         * Then the $GLOBALS['TSFE']-object is restored
         */

        /** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $GLOBALS ['TSFE'] */
        $before = $GLOBALS['TSFE'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class,
            $GLOBALS['TYPO3_CONF_VARS'],
            11,
            0
        );

        FrontendSimulatorUtility::simulateFrontendEnvironment(3);
        self::assertNotSame($before, $GLOBALS['TSFE']);
        self::assertTrue(FrontendSimulatorUtility::resetFrontendEnvironment());
        self::assertSame($before, $GLOBALS['TSFE']);

    }


    /**
     * @test
     */
    public function resetFrontendEnvironmentRestoresAspectObjects()
    {

        /**
         * Scenario:
         *
         * Given we were in FE-Mode
         * Given a sub-page in the rootline
         * Given a dateTimeAspect
         * Given a languageAspect
         * Given a userAspect for the frontendUser
         * Given simulateFrontendEnvironment was then called
         * Given simulateFrontendEnvironment has set a new dateTimeAspect that is not the same as the original one
         * Given simulateFrontendEnvironment has set a new visibilityAspect that is not the same as the original one
         * Given simulateFrontendEnvironment has set a new languageAspect that is not the same as the original one
         * Given simulateFrontendEnvironment has set a new userAspect for the frontendUser that is not the same as the original one
         * Given simulateFrontendEnvironment has set a new userAspect for the backendUser that is not the same as the original one
         * Given simulateFrontendEnvironment has set a new workspaceAspect that is not the same as the original one
         * When the method is called
         * Then the method returns true
         * Then the dateTimeAspect is set to the original one
         * Then the visibilityAspect is set to the original one
         * Then the languageAspect is set to the original one
         * Then the userAspect for the frontendUser is set to the original one
         * Then the userAspect for the backendUser is set to the original one
         * Then the workspaceAspect is set to the original one
         */

        // get the aspects
        $beforeDateTimeAspect = GeneralUtility::makeInstance(Context::class)->getAspect('date');
        $beforeVisibilityAspect = GeneralUtility::makeInstance(Context::class)->getAspect('visibility');
        $beforeLanguageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $beforeFrontendUserAspect = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user');
        $beforeBackendUserAspect = GeneralUtility::makeInstance(Context::class)->getAspect('backend.user');
        $beforeWorkspaceAspect = GeneralUtility::makeInstance(Context::class)->getAspect('workspace');

        // DURING
        FrontendSimulatorUtility::simulateFrontendEnvironment(3);

        self::assertNotSame($beforeDateTimeAspect, GeneralUtility::makeInstance(Context::class)->getAspect('date'));
        self::assertNotSame($beforeVisibilityAspect, GeneralUtility::makeInstance(Context::class)->getAspect('visibility'));
        self::assertNotSame($beforeLanguageAspect, GeneralUtility::makeInstance(Context::class)->getAspect('language'));
        self::assertNotSame($beforeFrontendUserAspect, GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user'));
        self::assertNotSame($beforeBackendUserAspect, GeneralUtility::makeInstance(Context::class)->getAspect('backend.user'));
        self::assertNotSame($beforeWorkspaceAspect, GeneralUtility::makeInstance(Context::class)->getAspect('workspace'));

        self::assertTrue(FrontendSimulatorUtility::resetFrontendEnvironment());

        // AFTER
        self::assertSame($beforeDateTimeAspect, GeneralUtility::makeInstance(Context::class)->getAspect('date'));
        self::assertSame($beforeVisibilityAspect, GeneralUtility::makeInstance(Context::class)->getAspect('visibility'));
        self::assertSame($beforeLanguageAspect, GeneralUtility::makeInstance(Context::class)->getAspect('language'));
        self::assertSame($beforeFrontendUserAspect, GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user'));
        self::assertSame($beforeBackendUserAspect, GeneralUtility::makeInstance(Context::class)->getAspect('backend.user'));
        self::assertSame($beforeWorkspaceAspect, GeneralUtility::makeInstance(Context::class)->getAspect('workspace'));

    }


    /**
     * @test
     */
    public function resetFrontendEnvironmentDoesNotSetEmptyValues()
    {

        /**
         * Scenario:
         *
         * Given we were in BE-mode
         * Given the $GLOBALS['TSFE']-object was not set
         * Given simulateFrontendEnvironment was called before
         * When the method is called
         * Then the method returns true
         * Then the $GLOBALS['TSFE']-object is not set
         */
        FrontendSimulatorUtility::simulateFrontendEnvironment(3);
        self::assertTrue(FrontendSimulatorUtility::resetFrontendEnvironment());
        self::assertNull($GLOBALS['TSFE']);

    }

    /**
     * @test
     */
    public function resetFrontendEnvironmentSetsBackendConfigurationManager()
    {

        /**
         * Scenario:
         *
         * Given we were in BE-Mode
         * Given a sub-page in the rootline
         * Given simulateFrontendEnvironment was called before
         * When the method is called
         * Then the method returns true
         * Then the Typoscript-configuration for backend is available via configurationManager
         */

        FrontendSimulatorUtility::simulateFrontendEnvironment(3);
        self::assertTrue(FrontendSimulatorUtility::resetFrontendEnvironment());

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);

        $settings = $configurationManager->getConfiguration($configurationManager::CONFIGURATION_TYPE_SETTINGS, 'rkwBasics');
        self::assertEquals(1, $settings['backendContext']);
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
