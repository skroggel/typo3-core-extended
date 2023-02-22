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
 * Class StripTagsViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class StripTagsViewHelper extends AbstractViewHelper {


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
        $this->registerArgument('value', 'string', 'string to strip tags from');
        $this->registerArgument('preserveSpaces', 'boolean', 'if set to true spaces are preserved');
    }


    /**
     * Applies stripTags() on the specified value.
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     * @return string
     * @author  bzplan_at_web_dot_de
     * @see https://www.php.net/manual/de/function.strip-tags.php#110280
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) : string {

        $string = $renderChildrenClosure();
        if ($arguments['preserveSpaces']) {

            // remove HTML TAGs
            $string = preg_replace ('/<[^>]*>/', ' ', $string);

            // remove control characters
            $string = str_replace("\r", '', $string);    // --- replace with empty space
            $string = str_replace("\n", ' ', $string);   // --- replace with space
            $string = str_replace("\t", ' ', $string);   // --- replace with space

            // remove multiple spaces
            $string = trim(preg_replace('/ {2,}/', ' ', $string));

            return $string;
        }

        return strip_tags($string);
    }
}
