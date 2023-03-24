<?php
namespace Madj2k\CoreExtended\MetaTag;

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
 * Class CanonicalGenerator
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CanonicalGenerator extends \TYPO3\CMS\Seo\Canonical\CanonicalGenerator
{

    /**
     * @return string
     */
    public function getPath(): string
    {
        // 1) Check if page show content from other page
        $href = $this->checkContentFromPid();

        if (empty($href)) {
            // 2) Check if page has canonical URL set
            $href = $this->checkForCanonicalLink();
        }
        if (empty($href)) {
            // 3) Fallback, create canonical URL
            $href = $this->checkDefaultCanonical();
        }

        if (!empty($href)) {
            return $href;
        }
        return '';
    }
}
