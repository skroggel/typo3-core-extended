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
 * File
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class File extends \TYPO3\CMS\Extbase\Domain\Model\File
{

    /**
     * @var string
     */
    protected string $identifier = '';


    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("persist") persist
     * @var \Madj2k\CoreExtended\Domain\Model\FileMetadata|null
     */
    protected ?FileMetadata $metadata = null;


    /**
     * Returns the identifier
     *
     * @return string $identifier
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }


    /**
     * Sets the identifier
     *
     * @param string $identifier
     * @return void
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }


    /**
     * Return the metadata
     *
     * @return \Madj2k\CoreExtended\Domain\Model\FileMetadata|null $metadata
     */
    public function getMetadata():? FileMetadata
    {
        return $this->metadata;
    }


    /**
     * Set the fileMetadata
     *
     * @param \Madj2k\CoreExtended\Domain\Model\FileMetadata $metadata
     * @return void
     */
    public function setMetadata(FileMetadata $metadata): void
    {
        $this->metadata = $metadata;
    }

}
