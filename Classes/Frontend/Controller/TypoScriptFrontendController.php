<?php
namespace Madj2k\CoreExtended\Frontend\Controller;

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

use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Used to simulate a frontend in backend context
 * We need to disable the groupAccessCheck e.g. in order to be able to send emails from pages with feGroups set
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TypoScriptFrontendController extends \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
{
    /**
     * If $this->id contains a translated page record, this needs to be resolved to the default language
     * in order for all rootline functionality and access restrictions to be in place further on.
     *
     * Additionally, if a translated page is found, $this->sys_language_uid/sys_language_content is set as well.
     *
     * @return void
     */
    protected function resolveTranslatedPageId(): void
    {
        $this->page = $this->sys_page->getPage($this->id, true); // the only change is here - disableGroupAccessCheck!

        if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 10000000) {

            // Accessed a default language page record, nothing to resolve
            if (empty($this->page) || (int)$this->page[$GLOBALS['TCA']['pages']['ctrl']['languageField']] === 0) {
                return;
            }
            $languageId = (int)$this->page[$GLOBALS['TCA']['pages']['ctrl']['languageField']];
            $this->page = $this->sys_page->getPage($this->page[$GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField']]);
            $this->context->setAspect('language', GeneralUtility::makeInstance(LanguageAspect::class, $languageId));
            $this->id = $this->page['uid'];
            // For common best-practice reasons, this is set, however, will be optional for new routing mechanisms
            if (!$this->getCurrentSiteLanguage()) {
                $_GET['L'] = $languageId;
                $GLOBALS['HTTP_GET_VARS']['L'] = $languageId;
            }

        } else {

            // Accessed a default language page record, nothing to resolve
            if (empty($this->page) || (int)$this->page[$GLOBALS['TCA']['pages']['ctrl']['languageField']] === 0) {
                return;
            }
            $languageId = (int)$this->page[$GLOBALS['TCA']['pages']['ctrl']['languageField']];
            $this->page = $this->sys_page->getPage($this->page[$GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField']]);
            $this->context->setAspect('language', GeneralUtility::makeInstance(LanguageAspect::class, $languageId));
            $this->id = $this->page['uid'];
        }
    }
}
