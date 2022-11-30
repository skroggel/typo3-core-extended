<?php
namespace Madj2k\CoreExtended\DataHandling;

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
 * Class SlugHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SlugHelper extends \TYPO3\CMS\Core\DataHandling\SlugHelper
{

    /**
     * Cleans a slug value so it is used directly in the path segment of a URL.
     *
     * @param string $slug
     * @return string
     */
    public function sanitize(string $slug): string
    {
        return \Madj2k\CoreExtended\Utility\GeneralUtility::slugify($slug);
    }

}
