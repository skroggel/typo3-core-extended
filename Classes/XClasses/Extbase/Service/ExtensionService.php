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
 * Service for determining basic extension params
 * @internal only to be used within Extbase, not part of TYPO3 Core API.
 */
class ExtensionService extends \TYPO3\CMS\Extbase\Service\ExtensionService
{

    /**
     * Iterates through the global TypoScript configuration and returns the name of the plugin
     * that matches specified extensionName, controllerName and actionName.
     * If no matching plugin was found, NULL is returned.
     * If more than one plugin matches and the current plugin is not configured to handle the action,
     * an Exception will be thrown
     *
     * @param string $extensionName name of the target extension (UpperCamelCase)
     * @param string $controllerName name of the target controller (UpperCamelCase)
     * @param string $actionName name of the target action (lowerCamelCase)
     * @return string name of the target plugin (UpperCamelCase) or NULL if no matching plugin configuration was found
     * @throws \TYPO3\CMS\Extbase\Exception
     */
    public function getPluginNameByAction($extensionName, $controllerName, $actionName)
    {

        // fixing weird core bug
        try {
            return parent::getPluginNameByAction($extensionName, $controllerName, $actionName);
        } catch (\TYPO3\CMS\Extbase\Exception $e) {
            return null;
        }
    }
}

