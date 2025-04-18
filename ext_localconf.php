<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Madj2k.CoreExtended',
            'GoogleSitemap',
            array(
                'Google' => 'sitemap',
            ),
            // non-cacheable actions
            array(

            )
        );

        /**
         * @deprecated
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Madj2k.CoreExtended',
            'AssetNotFound',
            array(
                'NotFound' => 'assets',
            ),
            // non-cacheable actions
            array(
                'NotFound' => 'assets',
            )
        );

        //=================================================================
        // Register Caching
        //=================================================================
        $cacheIdentifier = \Madj2k\CoreExtended\Utility\GeneralUtility::underscore($extKey);
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier] = [
            'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
            'backend' => \TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend::class,
            'groups' => [
                'all',
                'pages',
            ],
        ];

        $cacheIdentifier = \Madj2k\CoreExtended\Utility\GeneralUtility::underscore($extKey). '_treelist';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier] = [
            'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
            'backend' => \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
            'groups' => [
                'all',
                'pages',
            ],
        ];

        //=================================================================
        // Add TypoScript automatically
        //=================================================================
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
            'CoreExtended',
            'constants',
            '<INCLUDE_TYPOSCRIPT: source="FILE: EXT:core_extended/Configuration/TypoScript/constants.typoscript">'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
            'CoreExtended',
            'setup',
            '<INCLUDE_TYPOSCRIPT: source="FILE: EXT:core_extended/Configuration/TypoScript/setup.typoscript">'
        );

        //=================================================================
        // Add Rootline Fields
        //=================================================================
        $rootlineFields = &$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'];
        $newRootlineFields = 'tx_coreextended_fe_layout_next_level,tx_coreextended_preview_image,tx_coreextended_og_image,tx_coreextended_cover,tx_coreextended_file';
        $rootlineFields .= (empty($rootlineFields))? $newRootlineFields : ',' . $newRootlineFields;

         //=================================================================
        // Register Hooks
        //=================================================================
        if (TYPO3_MODE !== 'BE') {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'Madj2k\\CoreExtended\\Hooks\\ReplaceExtensionPathsHook->hook_contentPostProc';
        }

        //=================================================================
        // XClasses
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Extbase\Service\EnvironmentService::class] = [
            'className' => Madj2k\CoreExtended\XClasses\Extbase\Service\EnvironmentService::class
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class] = [
            'className' => Madj2k\CoreExtended\XClasses\Extbase\Configuration\ConfigurationManager::class
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Extbase\Service\ExtensionService::class] = [
            'className' => Madj2k\CoreExtended\XClasses\Extbase\Service\ExtensionService::class
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication::class] = [
            'className' => Madj2k\CoreExtended\XClasses\Frontend\Authentication\FrontendUserAuthentication::class
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Extbase\Mvc\Controller\ActionController::class] = [
            'className' =>Madj2k\CoreExtended\XClasses\Extbase\Mvc\ActionController::class
        ];

        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('sr_freecap')) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][SJBR\SrFreecap\Controller\ImageGeneratorController::class] = [
                'className' => Madj2k\CoreExtended\XClasses\SrFreecap\Controller\ImageGeneratorController::class
            ];
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][SJBR\SrFreecap\Validation\Validator\CaptchaValidator::class] = [
                'className' => Madj2k\CoreExtended\XClasses\SrFreecap\Validation\Validator\CaptchaValidator::class
            ];
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][SJBR\SrFreecap\Domain\Session\SessionStorage::class] = [
                'className' => Madj2k\CoreExtended\XClasses\SrFreecap\Session\SessionStorage::class
            ];
        }

        //=================================================================
        // ATTENTION: deactivated due to faulty mapping in TYPO3 9.5
        // Add XClasses for extending existing classes
        //=================================================================
//        // for TYPO3 12+
//        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Domain\Model\BackendUser::class] = [
//            'className' => \Madj2k\CoreExtended\Domain\Model\BackendUser::class
//        ];
//
//        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
//        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
//            ->registerImplementation(
//                \TYPO3\CMS\Extbase\Domain\Model\BackendUser::class,
//                \Madj2k\CoreExtended\Domain\Model\BackendUser::class
//            );
//
//        // for TYPO3 12+
//        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Domain\Model\FrontendUser::class] = [
//            'className' => \Madj2k\CoreExtended\Domain\Model\FrontendUser::class
//        ];
//
//        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
//        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
//            ->registerImplementation(
//                \TYPO3\CMS\Extbase\Domain\Model\FrontendUser::class,
//                \Madj2k\CoreExtended\Domain\Model\FrontendUser::class
//            );
//
//        // for TYPO3 12+
//        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup::class] = [
//            'className' => \Madj2k\CoreExtended\Domain\Model\FrontendUserGroup::class
//        ];
//
//        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
//        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
//            ->registerImplementation(
//                \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup::class,
//                \Madj2k\CoreExtended\Domain\Model\FrontendUserGroup::class
//            );
//
//        // for TYPO3 12+
//        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Domain\Model\File::class] = [
//            'className' => \Madj2k\CoreExtended\Domain\Model\File::class
//        ];
//
//        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
//        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
//            ->registerImplementation(
//                \TYPO3\CMS\Extbase\Domain\Model\File::class,
//                \Madj2k\CoreExtended\Domain\Model\File::class
//            );
//
//        // for TYPO3 12+
//        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Domain\Model\FileReference::class] = [
//            'className' => \Madj2k\CoreExtended\Domain\Model\FileReference::class
//        ];
//
//        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
//        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
//            ->registerImplementation(
//                \TYPO3\CMS\Extbase\Domain\Model\FileReference::class,
//                \Madj2k\CoreExtended\Domain\Model\FileReference::class
//            );


        //====================re=============================================
        // Configure Logger
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['Madj2k']['CoreExtended']['writerConfiguration'] = array(

            // configuration for WARNING severity, including all
            // levels with higher severity (ERROR, CRITICAL, EMERGENCY)
            \TYPO3\CMS\Core\Log\LogLevel::DEBUG => array(
                // add a FileWriter
                'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
                    // configuration for the writer
                    'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath()  . '/log/tx_coreextended.log'
                )
            ),
        );
    },
    'core_extended'
);


