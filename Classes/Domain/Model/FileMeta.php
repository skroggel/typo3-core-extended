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
 * Class FileMeta
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileMeta extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * TxCoreExtendedPublisher
     *
     * @var string
     */
    protected $TxCoreExtendedPublisher = '';


    /**
     * TxCoreExtendedSource
     *
     * @var \Madj2k\CoreExtended\Domain\Model\MediaSources
     */
    protected $TxCoreExtendedSource;


    /**
     * Returns the TxCoreExtendedPublisher
     *
     * @return string $TxCoreExtendedPublisher
     */
    public function getTxCoreExtendedPublisher()
    {
        return $this->TxCoreExtendedPublisher;
    }

    /**
     * Sets the TxCoreExtendedPublisher
     *
     * @param string $TxCoreExtendedPublisher
     * @return void
     */
    public function setTxCoreExtendedPublisher($TxCoreExtendedPublisher)
    {
        $this->TxCoreExtendedPublisher = $TxCoreExtendedPublisher;
    }


    /**
     * Returns the TxCoreExtendedSource
     *
     * @return \Madj2k\CoreExtended\Domain\Model\MediaSources $TxCoreExtendedSource
     */
    public function getTxCoreExtendedSource()
    {
        return $this->TxCoreExtendedSource;
    }


    /**
     * Sets the TxCoreExtendedSource
     *
     * @param \Madj2k\CoreExtended\Domain\Model\MediaSources $TxCoreExtendedSource
     * @return void
     */
    public function setTxCoreExtendedSource(\Madj2k\CoreExtended\Domain\Model\MediaSources $TxCoreExtendedSource)
    {
        $this->TxCoreExtendedSource = $TxCoreExtendedSource;
    }

}
