<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey) {

        //=================================================================
        // Add tables
        //=================================================================
        // "\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages" is allowed here:
        // https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ExtensionArchitecture/ConfigurationFiles/Index.html#id4
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_coreextended_domain_model_mediasources'
        );

        //=================================================================
        // Add CSS style to cache-delete menu according to application context
        //=================================================================

        // $GLOBALS['TBE_STYLES'] are allowed here:
        // https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ExtensionArchitecture/ConfigurationFiles/Index.html#id4
        if (TYPO3_MODE == "BE") {
            if (\TYPO3\CMS\Core\Utility\GeneralUtility::getApplicationContext()->isProduction()) {

                if (\TYPO3\CMS\Core\Utility\GeneralUtility::getApplicationContext()->__toString() === 'Production/Staging') {
                    $GLOBALS['TBE_STYLES']['skins'][$extKey]['stylesheetDirectories'][] = 'EXT:accelerator/Resources/Public/Backend/Css/Staging';
                } else {
                    $GLOBALS['TBE_STYLES']['skins'][$extKey]['stylesheetDirectories'][] = 'EXT:accelerator/Resources/Public/Backend/Css/Production';
                }

            } else {
                $GLOBALS['TBE_STYLES']['skins'][$extKey]['stylesheetDirectories'][] = 'EXT:accelerator/Resources/Public/Backend/Css/Development';
            }
        }

    },
    'core_extended'
);


