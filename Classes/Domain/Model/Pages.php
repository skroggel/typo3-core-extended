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
     * sysLanguageUid
     *
     * @var int
     */
    protected int $sysLanguageUid = 0;


    /**
     * @var int
     */
    protected int $crdate = 0;


    /**
     * @var int
     */
    protected int $tstamp = 0;


    /**
     * @var bool
     */
    protected bool $hidden = false;


    /**
     * @var bool
     */
    protected bool $deleted = false;


    /**
     * @var int
     */
    protected int $sorting = 0;


    /**
     * @var int
     */
    protected int $doktype = 1;


    /**
     * @var string
     */
    protected string $title = '';


    /**
     * @var string
     */
    protected string $subtitle = '';


    /**
     * @var string
     */
    protected string $abstract = '';


    /**
     * @var string
     */
    protected string $description = '';


    /**
     * @var bool
     */
    protected bool $noSearch = false;


    /**
     * @var int
     */
    protected int $lastUpdated = 0;


    /**
     * @var string
     */
    protected string $txCoreextendedAlternativeTitle = '';


    /**
     * @var int
     */
    protected int $txCoreextendedFeLayoutNextLevel = 0;


    /**
     * @var \Madj2k\CoreExtended\Domain\Model\FileReference|null
     */
    protected ?FileReference $txCoreextendedPreviewImage = null;


    /**
     * @var \Madj2k\CoreExtended\Domain\Model\FileReference|null
     */
    protected ?FileReference $txCoreextendedOgImage = null;


    /**
     * @var \Madj2k\CoreExtended\Domain\Model\FileReference|null
     */
    protected ?FileReference $txCoreextendedFile = null;


    /**
     * @var \Madj2k\CoreExtended\Domain\Model\FileReference|null
     */
    protected ?FileReference $txCoreextendedCover = null;


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
     * Returns the sysLanguageUid
     *
     * @return int
     */
    public function getSysLanguageUid(): int
    {
        return $this->sysLanguageUid;
    }


    /**
     * Returns the crdate value
     *
     * @return int
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
     * @return int
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
     * Returns the TxCoreextendedAlternativeTitle
     *
     * @return string $TxCoreextendedAlternativeTitle
     */
    public function getTxCoreextendedAlternativeTitle(): string
    {
        return $this->txCoreextendedAlternativeTitle;
    }


    /**
     * Sets the TxCoreextendedAlternativeTitle
     *
     * @param string $txCoreextendedAlternativeTitle
     * @return void
     */
    public function setTxCoreextendedAlternativeTitle(string $txCoreextendedAlternativeTitle): void
    {
        $this->txCoreextendedAlternativeTitle = $txCoreextendedAlternativeTitle;
    }


    /**
     * Returns the TxCoreextendedFeLayoutNextLevel
     *
     * @return int TxCoreextendedFeLayoutNextLevel
     */
    public function getTxCoreextendedFeLayoutNextLevel(): int
    {
        return $this->txCoreextendedFeLayoutNextLevel;
    }


    /**
     * Sets the TxCoreextendedFeLayoutNextLevel
     *
     * @param int $txCoreextendedFeLayoutNextLevel
     * @return void
     */
    public function setTxCoreextendedFeLayoutNextLevel(int $txCoreextendedFeLayoutNextLevel): void
    {
        $this->txCoreextendedFeLayoutNextLevel = $txCoreextendedFeLayoutNextLevel;
    }


    /**
     * Returns the txCoreextendedPreviewImage
     *
     * @return \Madj2k\CoreExtended\Domain\Model\FileReference
     */
    public function getTxCoreextendedPreviewImage(): ?FileReference
    {
        return $this->txCoreextendedPreviewImage;
    }


    /**
     * Sets the txCoreextendedPreviewImage
     *
     * @param \Madj2k\CoreExtended\Domain\Model\FileReference
     * @return void
     */
    public function setCoreExtendedPreviewImage(FileReference $txCoreextendedPreviewImage): void
    {
        $this->txCoreextendedPreviewImage = $txCoreextendedPreviewImage;
    }


    /**
     * Returns the txCoreextendedOgImage
     *
     * @return \Madj2k\CoreExtended\Domain\Model\FileReference
     */
    public function getTxCoreextendedOgImage(): ?FileReference
    {
        return $this->txCoreextendedOgImage;
    }


    /**
     * Sets the txCoreextendedOgImage
     *
     * @param \Madj2k\CoreExtended\Domain\Model\FileReference $txCoreextendedOgImage
     * @return void
     */
    public function setCoreExtendedOgImage(FileReference $txCoreextendedOgImage): void
    {
        $this->txCoreextendedOgImage = $txCoreextendedOgImage;
    }


    /**
     * Sets the txCoreextendedFile
     *
     * @param \Madj2k\CoreExtended\Domain\Model\FileReference $txCoreextendedFile
     * @return void
     */
    public function setCoreExtendedFile(FileReference $txCoreextendedFile): void
    {
        $this->txCoreextendedFile = $txCoreextendedFile;
    }

    /**
     * Returns the txCoreextendedFile
     *
     * @return \Madj2k\CoreExtended\Domain\Model\FileReference
     */
    public function getTxCoreextendedFile(): ?FileReference
    {
        return $this->txCoreextendedFile;
    }


    /**
     * Returns the txCoreextendedCover
     *
     * @return \Madj2k\CoreExtended\Domain\Model\FileReference
     */
    public function getTxCoreextendedCover(): ?FileReference
    {
        return $this->txCoreextendedCover;
    }


    /**
     * Sets the txCoreextendedCover
     *
     * @param \Madj2k\CoreExtended\Domain\Model\FileReference $txCoreextendedCover
     * @return void
     */
    public function setCoreExtendedCover(FileReference $txCoreextendedCover): void
    {
        $this->txCoreextendedCover = $txCoreextendedCover;
    }
}
