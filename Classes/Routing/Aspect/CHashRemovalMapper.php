<?php
namespace Madj2k\CoreExtended\Routing\Aspect;

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

use TYPO3\CMS\Core\Routing\Aspect\StaticMappableAspectInterface;
use TYPO3\CMS\Core\Site\SiteLanguageAwareTrait;

/**
 * Class CHashRemovalMapper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @deprecated since 2024-04-12 - do not use it anymore!
 */
class CHashRemovalMapper implements StaticMappableAspectInterface
{

    use SiteLanguageAwareTrait;


    /**
     * @param string $value
     * @return string
     */
    public function generate(string $value): ?string
    {
        trigger_error(__CLASS__ . '::' . __METHOD__ . '(): Please do not use this method any more.', E_USER_DEPRECATED);
        return $value ?: null;
    }


    /**
     * @param string $value
     * @return string|null
     */
    public function resolve(string $value): ?string
    {
        trigger_error(__CLASS__ . '::' . __METHOD__ . '(): Please do not use this method any more.', E_USER_DEPRECATED);
        return $value ?: null;
    }

}
