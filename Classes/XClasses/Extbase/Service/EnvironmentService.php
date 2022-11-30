<?php
namespace Madj2k\CoreExtended\XClasses\Extbase\Service;

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
 * Utility to simulate a frontend in backend context
 * This class is need as addition to Madj2k\CoreExtended\Utility\FrontendSimulatorUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EnvironmentService extends \TYPO3\CMS\Extbase\Service\EnvironmentService
{


    /**
     * Detects if TYPO3_MODE is defined and its value is "FE"
     *
     * @return bool
     */
    public function isEnvironmentInFrontendMode()
    {
        if (
            (isset($GLOBALS['TSFE']))
            && (is_object($GLOBALS['TSFE']))
        ){
            return true;
        }

        return (defined('TYPO3_MODE') && TYPO3_MODE === 'FE') ?: false;
    }

    /**
     * Detects if TYPO3_MODE is defined and its value is "BE"
     *
     * @return bool
     */
    public function isEnvironmentInBackendMode()
    {
        if (
            (isset($GLOBALS['TSFE']))
            && (is_object($GLOBALS['TSFE']))
        ){
            return false;
        }

        return (defined('TYPO3_MODE') && TYPO3_MODE === 'BE') ?: false;
    }

}
