<?php
namespace Madj2k\CoreExtended\Validation;


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

use Madj2k\CoreExtended\Utility\FrontendUserSessionUtility;
use SJBR\SrFreecap\Domain\Repository\WordRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class CaptchaValidator
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CaptchaValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{

    /**
     * @var bool
     */
    protected bool $isValid = true;


    /**
     * Validation of captcha
     *
     * @param object $value
     * @return boolean
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function isValid($value): bool
    {

        if (
            (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('sr_freecap'))
           // && (! FrontendUserSessionUtility::isLoggedIn())
        ){

            $this->isValid = false;
            if (
                (
                    (is_object($value))
                    && (method_exists($value, 'getCaptchaResponse'))
                    && ($word = $value->getCaptchaResponse())
                )
                || (
                    (is_array($value))
                    && (key_exists('captchaResponse', $value))
                    && ($word = $value['captchaResponse'])
                )
            ) {

                // Get session data
                /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
                $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class);

                /** @var \SJBR\SrFreecap\Domain\Repository\WordRepository $wordRepository */
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
                            $this->isValid = true;
                        }
                    }
                }
            }

            if (!$this->isValid) {
                $this->result->forProperty('captchaResponse')->addError(
                    new \TYPO3\CMS\Extbase\Error\Error(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            '9221561048',
                            'srfreecap'
                        ), 1666038470
                    )
                );
            }
        }

        return $this->isValid;
    }


}

