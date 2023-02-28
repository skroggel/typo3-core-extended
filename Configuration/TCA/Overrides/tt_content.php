<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function (string $extKey) {

        //=================================================================
        // Register Plugin
        //=================================================================

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Madj2k.CoreExtended',
            'MediaSources',
            'Media Sources'
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Madj2k.CoreExtended',
            'GoogleSitemap',
            'Google Sitemap'
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Madj2k.CoreExtended',
            'AssetNotFound',
            'Asset Not Found'
        );

        //=================================================================
        // Add Flexforms
        //=================================================================
        $extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($extKey));
        $pluginName = strtolower('MediaSources');
        $pluginSignature = $extensionName.'_'.$pluginName;

        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
            $pluginSignature,
            'FILE:EXT:' . $extKey . '/Configuration/FlexForms/MediaSources.xml'
        );

        //=================================================================
        // OTHER STUFF
        //=================================================================

        $tempColumnsContent = [

            'tx_coreextended_images_no_copyright' => [
                'exclude' => 0,
                'label' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:tt_content.tx_coreextended_images_no_copyright',
                'config' => [
                    'type' => 'check',
                    'default' => 0,
                    'items' => [
                        '1' => [
                            '0' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:tt_content.tx_coreextended_images_no_copyright.I.disabled'
                        ],
                    ],
                ],
            ],

        ];

        // Add TCA
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content',$tempColumnsContent);

        // Add fields
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('tt_content', 'image_settings','tx_coreextended_images_no_copyright','after:imageborder');
    },
    'core_extended'
);
