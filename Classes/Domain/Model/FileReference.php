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
    protected string $tableLocal = 'sys_file';


    /**
     * @var int
     */
    protected int $uidForeign = 0;


    /**
     * @var string
     */
    protected string $fieldname = 'image';


    /**
     * @var \Madj2k\CoreExtended\Domain\Model\File|null
     */
    protected ?File $file = null;


    /**
     * @var string
     */
    protected string $tablenames = '';



    /**
     * Returns the uidLocal
     *
     * @return int $uidLocal
     */
    public function getUidLocal(): int
    {
        return $this->uidLocal;
    }


    /**
     * Sets the uidLocal
     *
     * @param int $uidLocal
     * @return void
     */
    public function setUidLocal(int $uidLocal): void
    {
        $this->uidLocal = $uidLocal;
    }


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
     * @param string $fieldname
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
     * @return \Madj2k\CoreExtended\Domain\Model\File|null
     */
    public function getFile(): ?File
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


}
