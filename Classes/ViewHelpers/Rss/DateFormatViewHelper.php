<?php
namespace Madj2k\CoreExtended\ViewHelpers\Rss;

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
 * Class RssDateFormatViewHelper
 *
 * @package Madj2k_CoreExtended
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */
class DateFormatViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {


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
        $this->registerArgument('dateTime', 'integer', 'timestamp to format.', true);
        $this->registerArgument('format', 'string', 'format for date.', false, "D, d M Y H:i:s O");
    }


    /**
     * Format timestamp for usage in RSS-feeds
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

        $dateTime = $arguments['dateTime'];
        $format = $arguments['format'];

        return date($format, $dateTime);
    }

}
