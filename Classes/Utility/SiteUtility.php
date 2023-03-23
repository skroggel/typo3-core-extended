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

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Core\Versioning\VersionState;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class SiteUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SiteUtility
{

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    static public function getCurrentTypo3Language (): string
    {

        /** @var \TYPO3\CMS\Core\Context\Context $context */
        $context = \Madj2k\CoreExtended\Utility\GeneralUtility::makeInstance(Context::class);
        $currentLanguageUid = $context->getPropertyFromAspect('language', 'id');

        return self::getTypo3LanguageByLanguageUid($currentLanguageUid);
    }


    /**
     * Returns typo3Language-string based on given languageUid
     *
     * @param int $languageUid
     * @return string
     */
    static public function getTypo3LanguageByLanguageUid (int $languageUid = 0): string
    {

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Core\Site\SiteFinder $siteFinder */
        $siteFinder = $objectManager->get(\TYPO3\CMS\Core\Site\SiteFinder::class);

        try {

            /** @var int $rootPage */
            $rootPage = 0;

            /** @var array $rootLine */
            $rootLine = GeneralUtility::makeInstance(RootlineUtility::class, $GLOBALS['TSFE']->id)->get();
            foreach ($rootLine as $siteArray) {
                if ($siteArray['is_siteroot']) {
                    $rootPage = intval($siteArray['uid']);
                }
            }

            $site = $siteFinder->getSiteByRootPageId($rootPage);
            $siteConfiguration = $site->getConfiguration();

            foreach ( $siteConfiguration['languages'] as $languageArray) {
                if (
                    (isset($languageArray['typo3Language']))
                    && (isset($languageArray['languageId']))
                    && ($languageArray['languageId'] == $languageUid)
                ){
                    return $languageArray['typo3Language'];
                }
            }
        } catch (\Exception $e) {
            // do nothing
        }

        return 'default';
    }


    /**
     * Returns languageUid based on given typo3Language-String
     *
     * @param string $typo3Language
     * @return int
     */
    static public function getLanguageUidByTypo3Language (string $typo3Language = 'default'): int
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Core\Site\SiteFinder $siteFinder */
        $siteFinder = $objectManager->get(\TYPO3\CMS\Core\Site\SiteFinder::class);

        try {

            /** @var int $rootPage */
            $rootPage = 0;

            /** @var array $rootLine */
            $rootLine = GeneralUtility::makeInstance(RootlineUtility::class, $GLOBALS['TSFE']->id)->get();
            foreach ($rootLine as $siteArray) {
                if ($siteArray['is_siteroot']) {
                    $rootPage = intval($siteArray['uid']);
                }
            }

            $site = $siteFinder->getSiteByRootPageId($rootPage);
            $siteConfiguration = $site->getConfiguration();

            foreach ( $siteConfiguration['languages'] as $languageArray) {

                if (
                    (isset($languageArray['typo3Language']))
                    && (isset($languageArray['languageId']))
                    && ($languageArray['typo3Language'] == $typo3Language)
                ){
                    return intval($languageArray['languageId']);
                }
            }
        } catch (\Exception $e) {
            // do nothing
        }

        return 0;

    }

}
