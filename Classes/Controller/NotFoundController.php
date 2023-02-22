<?php

namespace Madj2k\CoreExtended\Controller;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use Madj2k\CoreExtended\Resource\AssetFileNotFound;

/**
 * Class NotFoundController
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class NotFoundController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{


    /**
     * search for alternative asset files
     *
     * @return void
     */
    public function assetsAction(): void
    {

        if ($file = AssetFileNotFound::searchFile(GeneralUtility::_GP('file'))) {

            $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . '/' .  $file['relativePath'];
            header('Location: ' . $url, true, 301);
            exit();
        }

        header('HTTP/1.0 404 Not Found');
        exit();

    }

}
