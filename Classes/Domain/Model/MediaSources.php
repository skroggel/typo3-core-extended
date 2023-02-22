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
 * Class MediaSources
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MediaSources extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var string
     */
    protected string $name = '';


    /**
     * @var string
     */
    protected string $url = '';


    /**
     * @var boolean
     */
    protected bool $internal = false;


    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }


    /**
     * Returns the url
     *
     * @return string $url
     */
    public function getUrl(): string
    {
        return $this->url;
    }


    /**
     * Sets the url
     *
     * @param string $url
     * @return void
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }


    /**
     * Returns the internal
     *
     * @return boolean $internal
     */
    public function getInternal(): bool
    {
        return $this->internal;
    }


    /**
     * Sets the internal
     *
     * @param bool $isInternal
     * @return void
     */
    public function setInternal(bool $isInternal)
    {
        $this->internal = $isInternal;
    }


    /**
     * Returns the boolean state of internal
     *
     * @return boolean
     */
    public function isInternal(): bool
    {
        return $this->internal;
    }
}
