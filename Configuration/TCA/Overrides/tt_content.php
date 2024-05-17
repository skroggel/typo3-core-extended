<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function (string $extKey) {

        //=================================================================
        // Register Plugin
        //=================================================================

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

    },
    'core_extended'
);
