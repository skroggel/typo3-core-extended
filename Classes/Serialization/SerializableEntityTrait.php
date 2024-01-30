<?php
declare(strict_types=1);
namespace Madj2k\CoreExtended\Serialization;

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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class SerializableEntityTrait
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
trait SerializableEntityTrait
{
    /**
     * Unsets all properties of type ObjectStorage on serialization since they can not be serialized.
     *
     * @return array
     */
    public function __serialize(): array
    {
        $data = get_object_vars($this);
        foreach ($data as $key => $value) {
            if ($value instanceof ObjectStorage) {
                unset($data[$key]);
            }
        }

        return $data;
    }


    /**
     * Sets the object's properties and calls a method "initializeObject" which should initialize all object storages.
     *
     * @param $data
     * @return void
     */
    public function __unserialize($data): void
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        if (method_exists($this, 'initializeObject')) {
            $this->initializeObject();
        }
    }


    /**
     * Initializes all ObjectStorage properties by default.
     *
     * @return void
     */
    public function initializeObject(): void
    {
        $data = get_object_vars($this);
        foreach ($data as $key => $value) {
            if ($value instanceof ObjectStorage) {
                $this->{$key} = new ObjectStorage();
            }
        }
    }
}
