<?php

namespace Madj2k\CoreExtended\Hooks;

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

use Madj2k\CoreExtended\ContentProcessing\ReplaceExtensionPaths;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ReplaceExtensionPathHook
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package core_extended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ReplaceExtensionPathsHook
{

    /**
     * Called before page is outputed in order to include INT-Scripts
     *
     * @param array $params
     * @return void The content is passed by reference
     */
    function hook_contentPostProc(array &$params): void
    {

        // get object
        $obj = $params['pObj'];

        // get class
        $replaceExtensionPaths = GeneralUtility::makeInstance(ReplaceExtensionPaths::class);

        // Replace content
        $obj->content = $replaceExtensionPaths->process($obj->content);

    }

}
