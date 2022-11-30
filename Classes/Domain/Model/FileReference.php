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
 * FileReference
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference
{

    /**
     * @var string
     */
    protected $tableLocal = 'sys_file';


    /**
     * @var string
     */
    protected $fieldname = 'image';


    /**
     * @var \Madj2k\CoreExtended\Domain\Model\File
     */
    protected $file;


    /**
     * @var string
     */
    protected $tablenames = '';


    /**
     * @var int
     */
    protected $uidForeign = 0;


    /**
     * Returns the tableLocal
     *
     * @return string $tableLocal
     */
    public function getTableLocal(): string
    {
        return $this->tableLocal;
    }

    /**
     * Sets the tableLocal
     *
     * @param string $tableLocal
     * @return void
     */
    public function setTableLocal(string $tableLocal): void
    {
        $this->tableLocal = $tableLocal;
    }


    /**
     * Get fieldname
     *
     * @return string
     */
    public function getFieldname(): string
    {
        return $this->fieldname;
    }


    /**
     * Set fieldname
     *
     * @param string
     */
    public function setFieldname(string $fieldname): void
    {
        $this->fieldname = $fieldname;
    }


    /**
     * Set file
     *
     * @param \Madj2k\CoreExtended\Domain\Model\File $file
     */
    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    /**
     * Get file
     *
     * @return \Madj2k\CoreExtended\Domain\Model\File
     */
    public function getFile()
    {
        return $this->file;
    }


    /**
     * Returns the tablenames
     *
     * @return string $tablenames
     */
    public function getTablenames(): string
    {
        return $this->tablenames;
    }

    /**
     * Sets the tablenames
     *
     * @param string $tablenames
     * @return void
     */
    public function setTablenames(string $tablenames): void
    {
        $this->tablenames = $tablenames;
    }

    /**
     * Returns the uidForeign
     *
     * @return int $uidForeign
     */
    public function getUidForeign(): int
    {
        return $this->uidForeign;
    }

    /**
     * Sets the uidForeign
     *
     * @param int $uidForeign
     * @return void
     */
    public function setUidForeign(int $uidForeign): void
    {
        $this->uidForeign = $uidForeign;
    }


}
