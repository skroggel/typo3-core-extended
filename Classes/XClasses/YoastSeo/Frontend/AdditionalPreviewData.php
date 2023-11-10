<?php
namespace Madj2k\CoreExtended\XClasses\YoastSeo\Frontend;

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

use TYPO3\CMS\Core\SingletonInterface;

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('yoast_seo')) {

    /**
     * Class AdditionalPreviewData
     *
     * @author Steffen Kroggel <developer@steffenkroggel.de>
     * @copyright Steffen Kroggel
     * @package Madj2k_CoreExtended
     * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
     */
    class AdditionalPreviewData implements SingletonInterface
    {

        /**
         * @param array $params
         * @param object $pObj
         * @return void
         */
        public function render(array &$params, object $pObj): void
        {
            // just do nothing at all! Nobody need this shit!
        }

    }
} else {
    /**
     * Class AdditionalPreviewData
     *
     * @author Steffen Kroggel <developer@steffenkroggel.de>
     * @copyright Steffen Kroggel
     * @package Madj2k_CoreExtended
     * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
     */
    class AdditionalPreviewData
    {
       // empty class to avoid errors
    }
}
