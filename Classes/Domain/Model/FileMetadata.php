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
 * Class FileMetadata
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileMetadata extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var string
     */
    protected string $caption = '';


    /**
     * @var int
     */
    protected int $contentCreationDate = 0;


    /**
     * @var string
     */
    protected string $creator = '';


    /**
     * @var string
     */
    protected string $keywords = '';


    /**
     * @var string
     */
    protected string $publisher = '';


    /**
     * @var string
     */
    protected string $source = '';


    /**
     * @var string
     */
    protected string $text = '';


    /**
     * @var string
     */
    protected string $description = '';


    /**
     * @var string
     */
    protected string $title = '';


    /**
     * @var \Madj2k\CoreExtended\Domain\Model\File|null
     */
    protected ?File $file = null;


    /**
     * @var string
     */
    protected string $txCoreextendedPublisher = '';


    /**
     * @var \Madj2k\CoreExtended\Domain\Model\MediaSources|null
     */
    protected ?MediaSources $txCoreextendedSource = null;


    /**
     * Returns the caption
     *
     * @return string $caption
     */
    public function getCaption(): string
    {
        return $this->caption;
    }


    /**
     * Sets the caption
     *
     * @param string $caption
     * @return void
     */
    public function setCaption(string $caption): void
    {
        $this->caption = $caption;
    }


    /**
     * Returns the contentCreationDate
     *
     * @return int $contentCreationDate
     */
    public function getContentCreationDate(): int
    {
        return $this->contentCreationDate;
    }


    /**
     * Sets the contentCreationDate
     *
     * @param int $contentCreationDate
     * @return void
     */
    public function setContentCreationDate(int $contentCreationDate): void
    {
        $this->contentCreationDate = $contentCreationDate;
    }


    /**
     * Returns the creator
     *
     * @return string $creator
     */
    public function getCreator():string
    {
        return $this->creator;
    }


    /**
     * Sets the creator
     *
     * @param string $creator
     * @return void
     */
    public function setCreator(string $creator): void
    {
        $this->creator = $creator;
    }


    /**
     * Returns the keywords
     *
     * @return string $keywords
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }


    /**
     * Sets the keywords
     *
     * @param string $keywords
     * @return void
     */
    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }


    /**
     * Returns the publisher
     *
     * @return string $publisher
     */
    public function getPublisher(): string
    {
        return $this->publisher;
    }


    /**
     * Sets the publisher
     *
     * @param string $publisher
     * @return void
     */
    public function setPublisher(string $publisher): void
    {
        $this->publisher = $publisher;
    }


    /**
     * Returns the source
     *
     * @return string $source
     */
    public function getSource(): string
    {
        return $this->source;
    }


    /**
     * Sets the source
     *
     * @param string $source
     * @return void
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }


    /**
     * Returns the text
     *
     * @return string $text
     */
    public function getText(): string
    {
        return $this->text;
    }


    /**
     * Sets the text
     *
     * @param string $text
     * @return void
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }


    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }


    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
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
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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
    public function getFile():? File
    {
        return $this->file;
    }


    /**
     * Returns the txCoreextendedPublisher
     *
     * @return string
     */
    public function getTxCoreextendedPublisher(): string
    {
        return $this->txCoreextendedPublisher;
    }


    /**
     * Sets the txCoreextendedPublisher
     *
     * @param string $txCoreextendedPublisher
     * @return void
     */
    public function setTxCoreextendedPublisher(string $txCoreextendedPublisher)
    {
        $this->txCoreextendedPublisher = $txCoreextendedPublisher;
    }


    /**
     * Returns the txCoreextendedSource
     *
     * @return \Madj2k\CoreExtended\Domain\Model\MediaSources|null
     */
    public function getTxCoreextendedSource():? MediaSources
    {
        return $this->txCoreextendedSource;
    }


    /**
     * Sets the txCoreextendedSource
     *
     * @param \Madj2k\CoreExtended\Domain\Model\MediaSources $txCoreextendedSource
     * @return void
     */
    public function setTxCoreextendedSource(MediaSources $txCoreextendedSource)
    {
        $this->txCoreextendedSource = $txCoreextendedSource;
    }

}
