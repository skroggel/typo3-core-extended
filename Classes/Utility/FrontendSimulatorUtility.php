<?php
namespace Madj2k\CoreExtended\Utility;

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

use Madj2k\CoreExtended\Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Context\VisibilityAspect;
use TYPO3\CMS\Core\Context\WorkspaceAspect;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\PseudoSiteFinder;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\Localization\LanguageService;

/**
 * Utility to simulate a frontend in backend context.
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FrontendSimulatorUtility
{

    /**
     * @var array
     */
    protected static array $backup = [];


    /**
     * @var array
     */
    protected static array $cache = [];


    /**
     * @var \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication|null
     */
    protected static ?FrontendUserAuthentication $frontendUserAuthentication = null;


    /**
     * Sets $GLOBALS['TSFE'] in backend mode
     *
     * @param int $pid
     * @param int $lid
     * @return int
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     * @throws \Madj2k\CoreExtended\Exception
     */
    public static function simulateFrontendEnvironment(int $pid = 1, int $lid = 0): int
    {
        if (!$pid) {
            $pid = 1;
        }

        // only if in BE-Mode!!! Otherwise FE will crash
        if (TYPO3_MODE == 'BE') {

            // try to load from the cache
            if (self::applyStash($pid)) {

                // flush cache of environment variables
                GeneralUtility::flushInternalRuntimeCaches();

                // re-init configuration-manager
                self::initConfigurationManager();

                return 2;
            }

            // load frontend context
            try {

                // make a backup of the relevant data and also cache it
                self::storeStash(intval($GLOBALS['TSFE']->id));

                if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 10000000) {
                    // remove page-not-found-redirect in BE-context, obsolet in TYPO3 v10
                    // @extensionScannerIgnoreLine
                    $GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling'] = '';
                }

                // add correct domain to environment variables
                $_SERVER['HTTP_HOST'] = self::getHostname($pid);

                // flush cache of environment variables
                GeneralUtility::flushInternalRuntimeCaches();

                // set pid to $_GET - we need this for ConfigurationManager to load the right configuration
                $_GET['id'] = $_POST['id'] = $pid;

                self::initContextAspects($lid);
                self::initTypoScriptFrontendController($pid);

                /** @todo do we really need this? Currently we seem to need this for file access **/
                if (!is_object($GLOBALS['BE_USER'])) {
                    $GLOBALS['BE_USER'] = self::getBackendUserAuthentication();
                }

                self::initConfigurationManager();

                return 1;

            } catch (\Exception $e) {
                throw new Exception(
                    sprintf(
                        'Could not initialize frontend. Maybe page-configuration is missing. Error: %s',
                        $e->getMessage()
                    ),
                    1701363001);
            }
        }

        return 0;
    }


    /**
     * Resets $GLOBALS['TSFE'] if it was previously changed by simulateFrontendEnvironment()
     *
     * @return bool
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     * @see simulateFrontendEnvironment()
     */
    public static function resetFrontendEnvironment(): bool
    {

        if (TYPO3_MODE == 'BE') {
            if (self::applyStash()) {

                // flush cache of environment variables
                GeneralUtility::flushInternalRuntimeCaches();

                // re-init configuration-manager
                self::initConfigurationManager();

                return true;
            }
        }

        return false;
    }


    /**
     * Init configuration manager
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    protected static function initConfigurationManager(): void
    {

        // load correct concreteConfigurationManager based on new environmentContext (FE vs. BE)
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);
        $configurationManager->initializeObject();

        // reset configuration cache via setConfiguration
        $configurationManager->setConfiguration([]);

        // set contentObjectRenderer if not set
        // @extensionScannerIgnoreLine
        if (! $configurationManager->getContentObject()) {

            /** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObject */
            $contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $configurationManager->setContentObject($contentObject);
        }
    }


    /**
     * Init all relevant contextAspects
     * @param int $lid
     * @return void
     * @throws \Exception
     */
    protected static function initContextAspects (int $lid): void
    {

        /** @var  \TYPO3\CMS\Core\Context\Context $context */
        $context = GeneralUtility::makeInstance(Context::class);

        $time = $GLOBALS['EXEC_TIME'] ?? time();
        $context->setAspect('date', new DateTimeAspect(new \DateTimeImmutable('@' . $time)));
        $context->setAspect('visibility', new VisibilityAspect());
        $context->setAspect('frontend.user', GeneralUtility::makeInstance(UserAspect::class, self::getFrontendUserAuthentication()));
        $context->setAspect('backend.user', new UserAspect());
        $context->setAspect('workspace', new WorkspaceAspect());
        $context->setAspect('language', new LanguageAspect($lid));

    }


    /**
     * Init TypoScriptFrontendController
     * @param int $pid
     * @return void
     * @throws \TYPO3\CMS\Core\Error\Http\ServiceUnavailableException
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     * @throws \TYPO3\CMS\Core\Error\Http\InternalServerErrorException
     * @throws SiteNotFoundException
     */
    protected static function initTypoScriptFrontendController(int $pid): void
    {

        /** @var \TYPO3\CMS\Core\Site\SiteFinder $siteFinder */
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $siteFinder->getAllSites(false);
        $site = $siteFinder->getSiteByPageId($pid);
        $language = $site->getDefaultLanguage();

        /** @var  \TYPO3\CMS\Core\Context\Context $context */
        $context = GeneralUtility::makeInstance(Context::class);

        // Fake a Request-Object
        // Probably not needed in TYPO3 v11.0 anymore.
        $uri = new Uri((string) $site->getBase());

        /** @var $request \TYPO3\CMS\Core\Http\ServerRequest */
        $request = new ServerRequest(
            $uri,
            'GET',
            'php://input',
            [],
            [
                'HTTP_HOST' => $uri->getHost(),
                'SERVER_NAME' => $uri->getHost(),
                'HTTPS' => $uri->getScheme() === 'https',
                'SCRIPT_FILENAME' => __FILE__,
                'SCRIPT_NAME' => rtrim($uri->getPath(), '/') . '/'
            ]
        );

        $request = $request->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE);
        $GLOBALS['TYPO3_REQUEST'] = $request;

        // __construct($context = null, $siteOrId = null, $siteLanguageOrType = null, $pageArguments = null, $cHashOrFrontendUser = null, $_2 = null, $MP = null)
        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
            \Madj2k\CoreExtended\Frontend\Controller\TypoScriptFrontendController::class,
            $context,
            $site,
            $language
        );

        /**
         * Init database
         * @see \TYPO3\CMS\Frontend\Middleware\TypoScriptFrontendInitialization
         */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('pages');
        $connection->connect();

        /** @deprecated Keep the backwards-compatibility for TYPO3 v9, to have the fe_user within the global TSFE object */
        $GLOBALS['TSFE']->fe_user = self::getFrontendUserAuthentication();

        // Determine the id and evaluate any preview settings
        $GLOBALS['TSFE']->determineId();

        // Init TemplateService implicitly and load TypoScript
        $GLOBALS['TSFE']->getConfigArray();

        /** @var \TYPO3\CMS\Core\Localization\LanguageService $languageService */
        $languageService = GeneralUtility::makeInstance(LanguageService::class);
        $GLOBALS['LANG'] = $languageService;

        $GLOBALS['TSFE']->getPageAndRootlineWithDomain($pid, $request);

        // set absRefPrefix and baseURL accordingly
        $GLOBALS['TSFE']->config['config']['absRefPrefix'] = $GLOBALS['TSFE']->config['config']['baseURL'] = self::getHostname($pid);
        $GLOBALS['TSFE']->absRefPrefix = $GLOBALS['TSFE']->config['config']['absRefPrefix'] = '/';

    }


    /**
     * Get a frontendUserAuthentication
     *
     * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    protected static function getFrontendUserAuthentication(): FrontendUserAuthentication
    {

        if (! self::$frontendUserAuthentication) {

            /**
             * @var \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication $frontendUserAuthentication
             * @see \TYPO3\CMS\Frontend\Middleware\FrontendUserAuthenticator
             */
            self::$frontendUserAuthentication = GeneralUtility::makeInstance(FrontendUserAuthentication::class);
            self::$frontendUserAuthentication->start();
            self::$frontendUserAuthentication->unpack_uc();
        }

        return self::$frontendUserAuthentication;

    }


    /**
     * Get a BackendUserAuthentication
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected static function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Authentication\BackendUserAuthentication::class
        );
    }


    /**
     * Returns the hostname based on the given id
     *
     * @param int $pageId
     * @return string
     */
    protected static function getHostname(int $pageId): string
    {

        // get rootPage based on given id
        /** @var \TYPO3\CMS\Core\Utility\RootlineUtility $rootLineUtility */
        $rootLineUtility = new RootlineUtility($pageId);
        $pages = $rootLineUtility->get();

        $rootPageId = 0;
        foreach ($pages as $page) {
            if ($page['is_siteroot']) {
                $rootPageId = $page['uid'];
                break;
            }
        }

        $domain = '';
        try {
            /** @var  \TYPO3\CMS\Core\Site\SiteFinder $siteFinder */
            $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);

            /** @var \TYPO3\CMS\Core\Site\Entity\Site $site */
            $site = $siteFinder->getSiteByRootPageId($rootPageId);
            $domain = trim((string)$site->getBase(), '/');

        } catch (SiteNotFoundException $e) {

            //  fuck it!
        }

        return $domain;
    }


    /**
     * Set the stash for the given pid
     *
     * @param int $pid
     * @return void
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    protected static function storeStash(int $pid): void
    {

        // make a backup of the relevant data and also cache it
        self::$cache[$pid]['TSFE'] = self::$backup['TSFE'] = ($GLOBALS['TSFE'] ?? null);
        self::$cache[$pid]['LANG'] = self::$backup['LANG'] = ($GLOBALS['LANG'] ?? null);
        self::$cache[$pid]['BE_USER'] = self::$backup['BE_USER'] = ($GLOBALS['BE_USER'] ?? null);
        self::$cache[$pid]['TYPO3_CONF_VARS'] = self::$backup['TYPO3_CONF_VARS'] = ($GLOBALS['TYPO3_CONF_VARS'] ?? null);
        self::$cache[$pid]['TYPO3_REQUEST'] = self::$backup['TYPO3_REQUEST'] = ($GLOBALS['TYPO3_REQUEST'] ?? null);
        self::$cache[$pid]['_SERVER'] = self::$backup['_SERVER'] = ($_SERVER ?? null);
        self::$cache[$pid]['_GET'] = self::$backup['_GET'] = ($_GET ?? null);
        self::$cache[$pid]['_POST'] = self::$backup['_POST'] = ($_POST ?? null);

        // get contextAspects
        $context = GeneralUtility::makeInstance(Context::class);
        foreach (['date', 'visibility', 'backend.user', 'frontend.user', 'workspace', 'language'] as $aspect) {
            if ($context->hasAspect($aspect)) {
                self::$cache[$pid]['context'][$aspect] = self::$backup['context'][$aspect] = $context->getAspect($aspect);
            }
        }
    }

    /**
     * @param int $pid
     * @return bool
     */
    protected static function applyStash(int $pid = 0): bool
    {
        // use backup or pid-based cache?
        $stash = ($pid > 0) ? self::$cache[$pid] : self::$backup;

        // set globals
        if ($stash) {
            $GLOBALS['TSFE'] = ($stash['TSFE'] ?? null);
            $GLOBALS['LANG'] = ($stash['LANG'] ?? null);
            $GLOBALS['TYPO3_CONF_VARS'] = ($stash['TYPO3_CONF_VARS'] ?? null);
            $GLOBALS['BE_USER'] = ($stash['BE_USER'] ?? null);
            $GLOBALS['TYPO3_REQUEST'] = ($stash['TYPO3_REQUEST'] ?? null);
            $_SERVER = ($stash['_SERVER'] ?? null);
            $_GET = ($stash['_GET'] ?? null);
            $_POST = ($stash['_POST'] ?? null);

            // set contextAspects
            $context = GeneralUtility::makeInstance(Context::class);
            foreach (['date', 'visibility', 'backend.user', 'frontend.user', 'workspace', 'language'] as $aspect) {
                if ($stash['context'][$aspect]) {
                    $context->setAspect($aspect, $stash['context'][$aspect]);
                }
            }

            return true;
        }

        return false;
    }

}
