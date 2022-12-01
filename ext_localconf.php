<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
    {

        //=================================================================
        // Register Caching
        //=================================================================

        if( !is_array($GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations'][$extKey] ) ) {
            $GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations'][$extKey] = array();
        }

        if( !isset($GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations'][$extKey]['frontend'])) {
            $GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations'][$extKey]['frontend'] = 'TYPO3\\CMS\\Core\\Cache\\Frontend\\VariableFrontend';
        }

        if( !isset($GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations'][$extKey]['backend'])) {
            $GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations'][$extKey]['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend';
        }

        //=================================================================
        // Add Rootline Fields
        //=================================================================
        $rootlineFields = &$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'];
        $newRootlineFields = 'keywords,abstract,description,tx_coreextended_fe_layout_next_level,tx_coreextended_no_index,tx_coreextended_no_follow';
        $rootlineFields .= (empty($rootlineFields))? $newRootlineFields : ',' . $newRootlineFields;

        //=================================================================
        // Register Hooks
        //=================================================================
        if (TYPO3_MODE !== 'BE') {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'Madj2k\\CoreExtended\\Hooks\\ReplaceExtensionPathsHook->hook_contentPostProc';
        }

        //=================================================================
        // Asset for routing
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['PersistedSlugifiedPatternMapper']
            = \Madj2k\CoreExtended\Routing\Aspect\PersistedSlugifiedPatternMapper::class;

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['CHashRemovalMapper'] =
            \Madj2k\CoreExtended\Routing\Aspect\CHashRemovalMapper::class;

        //=================================================================
        // XClasses
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Extbase\Service\EnvironmentService::class] = [
            'className' => Madj2k\CoreExtended\XClasses\Extbase\Service\EnvironmentService::class
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Extbase\Service\ExtensionService::class] = [
            'className' => Madj2k\CoreExtended\XClasses\Extbase\Service\ExtensionService::class
        ];


        //====================re=============================================
        // Configure Logger
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['Madj2k']['CoreExtended']['writerConfiguration'] = array(

            // configuration for WARNING severity, including all
            // levels with higher severity (ERROR, CRITICAL, EMERGENCY)
            \TYPO3\CMS\Core\Log\LogLevel::WARNING => array(
                // add a FileWriter
                'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
                    // configuration for the writer
                    'logFile' => 'typo3temp/var/logs/tx_coreextended.log'
                )
            ),
        );
    },
    'accelerator'
);


