<?php
namespace Madj2k\CoreExtended\Domain\Model;

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
 * Class BackendUser
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendUser extends \TYPO3\CMS\Extbase\Domain\Model\BackendUser
{
    /**
     * @var string
     */
    protected string $lang = 'en';


    /**
     * Gets the lang of the user
     *
     * @param string $lang
     */
    public function setLang(string $lang)
    {
        $this->lang = $lang;
    }


    /**
     * Gets the lang of the user
     *
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

}
