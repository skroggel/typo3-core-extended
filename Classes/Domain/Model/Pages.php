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
 * Class Pages
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Pages extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{


    /**
     * crdate
     *
     * @var integer
     */
    protected $crdate = 0;


    /**
     * tstamp
     *
     * @var integer
     */
    protected $tstamp = 0;


    /**
     * hidden
     *
     * @var bool
     */
    protected $hidden = 0;


    /**
     * deleted
     *
     * @var bool
     */
    protected $deleted = 0;


    /**
     * sorting
     *
     * @var int
     */
    protected $sorting = 0;


    /**
     * doktype
     *
     * @var int
     */
    protected $doktype = 1;


    /**
     * title
     *
     * @var string
     */
    protected $title = '';


    /**
     * subtitle
     *
     * @var string
     */
    protected $subtitle = '';


    /**
     * abstract
     *
     * @var string
     */
    protected $abstract = '';


    /**
     * description
     *
     * @var string
     */
    protected $description = '';


    /**
     * noSearch
     *
     * @var bool
     */
    protected $noSearch = false;


    /**
     * lastUpdated
     *
     * @var integer
     */
    protected $lastUpdated = 0;


    /**
     * TxCoreExtendedAlternativeTitle
     *
     * @var string
     */
    protected $TxCoreExtendedAlternativeTitle = '';


    /**
     * TxCoreExtendedFeLayoutNextLevel
     *
     * @var \integer
     */
    protected $TxCoreExtendedFeLayoutNextLevel = 0;



    /**
     * Returns the pid
     *
     * @return int $pid
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * Returns the crdate value
     *
     * @return integer
     * @api
     */
    public function getCrdate(): int
    {
        return $this->crdate;
    }


    /**
     * Sets the crdate value
     *
     * @param int $crdate
     * @return void
     * @api
     */
    public function setCrdate(int $crdate): void
    {
        $this->crdate = $crdate;
    }

    /**
     * Returns the tstamp value
     *
     * @return integer
     * @api
     */
    public function getTstamp(): int
    {
        return $this->tstamp;
    }

    /**
     * Sets the tstamp value
     *
     * @param int $tstamp
     * @return void
     * @api
     */
    public function setTstamp(int $tstamp): void
    {
        $this->tstamp = $tstamp;
    }

    /**
     * Returns the hidden value
     *
     * @return bool
     * @api
     */
    public function getHidden(): bool
    {
        return $this->hidden;
    }


    /**
     * Sets the hidden value
     *
     * @param bool $hidden
     * @return void
     * @api
     */
    public function setHidden(bool $hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * Returns the deleted value
     *
     * @return bool
     * @api
     */
    public function getDeleted(): int
    {
        return $this->deleted;
    }


    /**
     * Sets the deleted value
     *
     * @param bool $deleted
     * @return void
     * @api
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }


    /**
     * Returns the sorting
     *
     * @return int $sorting
     */
    public function getSorting(): int
    {
        return $this->sorting;
    }


    /**
     * Sets the sorting value
     *
     * @param int $sorting
     * @return void
     * @api
     */
    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }


    /**
     * Returns the doktype
     *
     * @return int $doktype
     */
    public function getDoktype(): int
    {
        return $this->doktype;
    }


    /**
     * Sets the doktype value
     *
     * @param int $doktype
     * @return void
     * @api
     */
    public function setDoktype(int $doktype): void
    {
        $this->doktype = $doktype;
    }


    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * Returns the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }


    /**
     * Returns the subtitle
     *
     * @return string $subtitle
     */
    public function getSubtitle(): string
    {
        return $this->subtitle;
    }


    /**
     * Set the subtitle
     *
     * @param string $subtitle
     * @return void
     */
    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }


    /**
     * Returns the abstract
     *
     * @return string $abstract
     */
    public function getAbstract(): string
    {
        return $this->abstract;
    }


    /**
     * Set the abstract
     *
     * @param string $abstract
     * @return void
     */
    public function setAbstract(string $abstract): void
    {
        $this->abstract = $abstract;
    }


    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription(): string
    {
        return $this->description;
    }


    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


    /**
     * Returns the noSearch
     *
     * @return bool $noSearch
     */
    public function getNoSearch(): bool
    {
        return $this->noSearch;
    }


    /**
     * Set the noSearch
     *
     * @param bool $noSearch
     * @return void
     */
    public function setNoSearch(bool $noSearch): void
    {
        $this->noSearch = $noSearch;
    }


    /**
     * Returns the lastUpdated
     *
     * @return int $lastUpdated
     */
    public function getLastUpdated(): int
    {
        return $this->lastUpdated;
    }


    /**
     * Set the lastUpdated
     *
     * @param int $lastUpdated
     * @return void
     */
    public function setLastUpdated(int $lastUpdated): void
    {
        $this->lastUpdated = $lastUpdated;
    }


    /**
     * Returns the TxCoreExtendedAlternativeTitle
     *
     * @return string $TxCoreExtendedAlternativeTitle
     */
    public function getTxCoreExtendedAlternativeTitle(): string
    {
        return $this->TxCoreExtendedAlternativeTitle;
    }


    /**
     * Sets the TxCoreExtendedAlternativeTitle
     *
     * @param string $TxCoreExtendedAlternativeTitle
     * @return void
     */
    public function setTxCoreExtendedAlternativeTitle(string $TxCoreExtendedAlternativeTitle): void
    {
        $this->TxCoreExtendedAlternativeTitle = $TxCoreExtendedAlternativeTitle;
    }


    /**
     * Returns the TxCoreExtendedFeLayoutNextLevel
     *
     * @return int TxCoreExtendedFeLayoutNextLevel
     */
    public function getTxCoreExtendedFeLayoutNextLevel(): int
    {
        return $this->TxCoreExtendedFeLayoutNextLevel;
    }


    /**
     * Sets the TxCoreExtendedFeLayoutNextLevel
     *
     * @param \integer $TxCoreExtendedFeLayoutNextLevel
     * @return \integer TxCoreExtendedFeLayoutNextLevel
     */
    public function setTxCoreExtendedFeLayoutNextLevel(int $TxCoreExtendedFeLayoutNextLevel): void
    {
        $this->TxCoreExtendedFeLayoutNextLevel = $TxCoreExtendedFeLayoutNextLevel;
    }

}
