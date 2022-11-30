<?php
namespace Madj2k\CoreExtended\XClasses\Validation\Validator;

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
use SJBR\SrFreecap\Domain\Repository\WordRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('sr_freecap')) {

    /**
     * Captcha validator
     */
    class CaptchaValidator extends \SJBR\SrFreecap\Validation\Validator\CaptchaValidator
    {

        /**
         * (added by MF)
         * JUST OVERRIDE "sr_freecap" TO "srFreecap" TO MAKE TYPOSCRIPT TRANSLATION POSSIBLE IN TYPO3 8.7
         *
         *
         * Check the word that was entered against the hashed value
         * Returns true, if the given property ($word) matches the session captcha value.
         *
         * @param string $word : the word that was entered and should be validated
         * @return boolean true, if the word entered matches the hash value, false if an error occured
         */
        public function isValid($word)
        {
            $isValid = false;
            // This validator needs a frontend user session
            if (is_object($GLOBALS ['TSFE']) && isset($GLOBALS ['TSFE']->fe_user)) {
                // Get session data
                $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                $wordRepository = $objectManager->get(WordRepository::class);
                $wordObject = $wordRepository->getWord();
                $wordHash = $wordObject->getWordHash();
                // Check the word hash against the stored hash value
                if (!empty($wordHash) && !empty($word)) {
                    if ($wordObject->getHashFunction() == 'md5') {
                        // All freeCap words are lowercase.
                        // font #4 looks uppercase, but trust me, it's not...
                        if (md5(strtolower(utf8_decode($word))) == $wordHash) {
                            // Reset freeCap session vars
                            // Cannot stress enough how important it is to do this
                            // Defeats re-use of known image with spoofed session id
                            $wordRepository->cleanUpWord();
                            $isValid = true;
                        }
                    }
                }
            } else {
                $isValid = empty($word);
            }
            if (!$isValid) {
                // Please enter the word or number as it appears in the image. The entered value was incorrect.
                $this->addError(
                    $this->translateErrorMessage(
                        '9221561048',
                        'srFreecap'
                    ),
                    9221561048
                );
            }
            return $isValid;
        }
    }
}
