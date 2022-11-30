<?php
namespace Madj2k\CoreExtended\Cache;

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
 * Class CacheAbstract
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class CacheAbstract implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var string Key for cache
     */
    protected $_key = 'core_extended';

    /**
     * @var string Identifier for cache
     */
    protected $_identifier = 'core_extended';

    /**
     * @var string Contains context mode (Production, Development...)
     */
    protected $contextMode  = '';

    /**
     * @var string Contains environment mode (FE or BE)
     */
    protected $environmentMode = '';


    /**
     * Returns cache identifier
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->_identifier;
    }


    /**
     * Returns cache identifier
     *
     * @param mixed $identifier
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->_identifier = sha1($identifier);
        return $this;
    }


    /**
     * Returns cached object
     *
     * @param mixed $identifier
     * @return mixed
     */
    public function getContent($identifier = null)
    {

        if ($identifier) {
            $this->setIdentifier($identifier);
        }

        // only use cache when in production
        // and when called from FE
        if (
            ($this->getContextMode() != 'Production')
            || ($this->getEnvironmentMode() != 'FE')
        ) {
            return false;
        }

        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')
            ->getCache($this->_key)
            ->get($this->getIdentifier());
    }


    /**
     * sets cached content
     *
     * @param mixed $data
     * @param array $tags
     * @param mixed $identifier
     * @param integer $lifetime
     * @return $this
     */
    public function setContent($data, $tags = array(), $identifier = null, $lifetime = 21600)
    {

        if ($identifier) {
            $this->setIdentifier($identifier);
        }

        // only use cache when in production
        // and when called from FE
        if (
            ($this->getContextMode() != 'Production')
            || ($this->getEnvironmentMode() != 'FE')
        ) {
            return $this;
        }

        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')
            ->getCache($this->_key)
            ->set($this->getIdentifier(), $data, $tags, $lifetime);

        return $this;
    }


    /**
     * Returns cached object
     *
     * @return \TYPO3\CMS\Core\Cache\CacheManager
     */
    public function getCacheManager()
    {
        /** @var $cacheManager \TYPO3\CMS\Core\Cache\CacheManager */
        $cacheManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager');
        return $cacheManager;
    }


    /**
     * Function to return the current TYPO3_CONTEXT.
     *
     * @return string|NULL
     */
    protected function getContextMode()
    {

        if (!$this->contextMode) {
            if (getenv('TYPO3_CONTEXT')) {
                $this->contextMode = getenv('TYPO3_CONTEXT');
            }
        }

        return $this->contextMode;
    }

    /**
     * Function to return the current TYPO3_MODE.
     * This function can be mocked in unit tests to be able to test frontend behaviour.
     *
     * @return string
     * @see \TYPO3\CMS\Core\Resource\AbstractRepository
     */
    protected function getEnvironmentMode()
    {

        if (!$this->environmentMode) {
            if (TYPO3_MODE) {
                $this->environmentMode = TYPO3_MODE;
            }
        }

        return $this->environmentMode;
    }


    /**
     * Constructor
     *
     * @param string $environmentMode
     * @param string $contextMode
     */
    public function __construct($environmentMode = null, $contextMode = null)
    {

        if ($environmentMode) {
            $this->environmentMode = $environmentMode;
        }

        if ($contextMode) {
            $this->contextMode = $contextMode;
        }
    }

}
