<?php
namespace Madj2k\CoreExtended\ViewHelpers\Captcha;

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


/**
 * Class ImageViewHelper
 *
 * Placeholder in case the sr_freecap is not installed!
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('sr_freecap')) {
    class AudioViewHelper extends \SJBR\SrFreecap\ViewHelpers\AudioViewHelper
    {

    }

} else {

    class AudioViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
    {

        /**
         * Initialize arguments.
         *
         * @return void
         * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
         */
        public function initializeArguments(): void
        {
            parent::initializeArguments();
            $this->registerArgument('suffix', 'string', 'Suffix to be appended to the extension key when forming css class names', false, '');
        }

        /**
         * Render the captcha image html
         *
         * @param string suffix to be appended to the extension key when forming css class names
         * @return string The html used to render the captcha image
         */
        public function render($suffix = ''): string
        {
            return '';
        }

    }
}

