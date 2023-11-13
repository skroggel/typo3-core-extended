<?php
declare(strict_types=1);

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
namespace Madj2k\CoreExtended\XClasses\Extbase\Mvc;


/**
 * Class ActionController
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var string
     * @deprecated Not an official part of TYPO3 v10 anymore.
     */
    protected string $extensionName = '';


    /**
     *  Legacy backup for older extensions using $this->extensionName
     */
    public function __construct()
    {
        $className = static::class;
        $classNameParts = explode('\\', $className, 4);

        // Skip vendor and product name for core classes
        if (strpos($className, 'TYPO3\\CMS\\') === 0) {
            $this->extensionName = $classNameParts[2];
        } else {
            $this->extensionName = $classNameParts[1];
        }
    }

}
