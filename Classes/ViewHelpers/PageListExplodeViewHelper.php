<?php

namespace Madj2k\CoreExtended\ViewHelpers;

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

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Class PageListExplodeViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageListExplodeViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var bool
     */
    protected $escapeOutput = false;


    /**
     * Initialize arguments.
     *
     * @return void
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('list', 'string', 'The list that is to split', false, '');
        $this->registerArgument('delimiter', 'string', 'The first delimiter to use', false, '|');
        $this->registerArgument('delimiterTwo', 'string', 'The second delimiter to use', false, '###');
    }


    /**
     * Explodes a list of pages with two given delimiters
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     * @return array
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): array {

        $list = $arguments['list'];
        $delimiter = $arguments['delimiter'];
        $delimiterTwo = $arguments['delimiterTwo'];

        $result = [];
        $items = explode($delimiter, $list);

        foreach ($items as $item) {

            $explodeTemp = explode($delimiterTwo, $item);
            $result[$explodeTemp[0]] = $explodeTemp[1];
        }

        return $result;
    }
}
