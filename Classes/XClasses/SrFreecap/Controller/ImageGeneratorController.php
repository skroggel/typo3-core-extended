<?php
namespace Madj2k\CoreExtended\XClasses\SrFreecap\Controller;

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
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('sr_freecap')) {

    /**
     * Class ImageGeneratorController
     *
     * @author Stanislas Rolland <typo3@sjbr.ca>
     * @author Steffen Kroggel <developer@steffenkroggel.de>
     * @copyright Steffen Kroggel
     * @package Madj2k_CoreExtended
     * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
     */
    class ImageGeneratorController extends \SJBR\SrFreecap\Controller\ImageGeneratorController
    {

        /**
         * Show the CAPTCHA image
         *
         * @return bool
         */
        public function showAction(): bool
        {
            parent::showAction();

            /**
             * prevent calling $this->view->render() implicitly again!
             * @see \TYPO3\CMS\Extbase\Mvc\Controller\ActionController::callActionMethod()
             */
            return true;
        }
    }
} else {

    /**
     * Class ImageGeneratorController
     *
     * @author Steffen Kroggel <developer@steffenkroggel.de>
     * @copyright Steffen Kroggel
     * @package Madj2k_CoreExtended
     * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
     */
    class ImageGeneratorController
    {
        // empty class to avoid errors
    }
}
