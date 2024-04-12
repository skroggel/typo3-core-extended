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
 * @deprecated use \Madj2k\DrSeo\DataHandling\SlugHelper::slugify() instead, deprecated since 2024-04-12
 */
class SlugHelper extends \Madj2k\DrSeo\DataHandling\SlugHelper
{

    /**
     * Cleans a slug value so it is used directly in the path segment of a URL.
     *
     * @param string $slug
     * @return string
     */
    public function sanitize(string $slug): string
    {
        trigger_error(__CLASS__ . '::' . __METHOD__ . '(): Please do not use this method any more.', E_USER_DEPRECATED);
        return parent::sanitize($slug);
    }

}
