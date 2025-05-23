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
 * Class RobotsTagGenerator
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @deprecated since 2024-04-12 - use  \Madj2k\DrSerp\MetaTag\RobotsTagGenerator instead
 */
class RobotsTagGenerator extends \Madj2k\DrSerp\MetaTag\RobotsTagGenerator
{
    /**
     * Generate the meta tags that can be set in backend and add them to frontend by using the MetaTag API
     *
     * @param array $params
     * @return void
     */
    public function generate(array $params): void
    {
        trigger_error(__CLASS__ . '::' . __METHOD__ . '(): Please do not use this method any more.', E_USER_DEPRECATED);
        parent::generate($params);
    }
}
