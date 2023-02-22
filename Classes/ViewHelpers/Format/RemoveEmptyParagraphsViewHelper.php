<?php
namespace Madj2k\CoreExtended\ViewHelpers\Format;
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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Class RemoveEmptyParagraphsViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RemoveEmptyParagraphsViewHelper extends AbstractViewHelper {

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
        $this->registerArgument('value', 'string', 'string to format');
    }


    /**
     * Removes empty paragraphs from beginning and end of a string
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {

        // some cleanup for better matching
        $string = trim(preg_replace("#\r|\n#", "", $renderChildrenClosure()));

        $bodyTextWrapStartRegExp = '<div[^>]+class="(frame|ce-bodytext)[^>]+>';
        $bodyTextWrapEndRegExp = '</div>';

        $pregMatch = [

            // for usage with FluidStyledContents
            "#(" . $bodyTextWrapStartRegExp . ")(<p[^>]*>&nbsp;</p>){1,}#i",
            "#(<p[^>]*>&nbsp;</p>){1,}(" . $bodyTextWrapEndRegExp . ")#i",

            // for usage in custom content elements
            "#^(<p[^>]*>&nbsp;</p>){1,}#i",
            "#(<p[^>]*>&nbsp;</p>){1,}$#i",
        ];

        $pregReplace = [
            '$1',
            '$2',
            '',
            ''
        ];

        return preg_replace($pregMatch, $pregReplace, $string);
    }
}
