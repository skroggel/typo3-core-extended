<?php
namespace Madj2k\CoreExtended\ExpressionLanguage\Functions;

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

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * ExtensionLoadedFunctionsProvider
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ExtensionLoadedFunctionsProvider implements ExpressionFunctionProviderInterface
{

    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions(): array
    {
        return [
            $this->getExtensionLoadedFunction(),
        ];
    }


    /**
     * @return ExpressionFunction
     */
    protected function getExtensionLoadedFunction(): ExpressionFunction
    {
        return new ExpressionFunction('extensionLoaded', function () {
            // Not implemented, we only use the evaluator
        }, function ($existingVariables, $extensionName) {
            return \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($extensionName);
        });
    }

}
