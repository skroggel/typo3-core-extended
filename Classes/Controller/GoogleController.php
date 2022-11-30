<?php

namespace Madj2k\CoreExtended\Controller;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class GoogleController
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GoogleController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{


    /**
     * @var \Madj2k\CoreExtended\Cache\SitemapCache
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $cache;


    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * pagesRepository
     *
     * @var \Madj2k\CoreExtended\Domain\Repository\PagesRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $pagesRepository = null;


    /**
     * action sitemap
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function sitemapAction()
    {

        if (!$sitemap = $this->getCache()->getContent($this->getCacheKey())) {

            $currentPid = $GLOBALS['TSFE']->id;
            $depth = 999999;

            $treeList = explode(
                ',',
                \Madj2k\CoreExtended\Utility\GeneralUtility::getTreeList($currentPid , $depth, 0, 1)
            );

            $pages = $this->pagesRepository->findByUidListAndDokTypes($treeList);
            $this->view->assign('pages', $pages);
            $sitemap = $this->view->render();

            // flush caches
            $this->getCache()->getCacheManager()->flushCachesByTag('rkwbasics_sitemap');

            // save results in cache
            $this->getCache()->setContent(
                $sitemap,
                array(
                    'rkwbasics_sitemap',
                ),
                $this->getCacheKey()
            );

            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Successfully rebuilt Google sitemap feed.'));
        } else {
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Successfully loaded Google sitemap from cache.'));
        }

        return $sitemap;

    }


    /**
     * Returns cache key
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return GeneralUtility::getIndpEnv('HTTP_HOST');
    }



    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger()
    {

        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
        }

        return $this->logger;
    }


    /**
     * Returns the cache object
     *
     * @return \Madj2k\CoreExtended\Cache\SitemapCache
     */
    protected function getCache()
    {

        if (!$this->cache instanceof \Madj2k\CoreExtended\Cache\SitemapCache) {
            $this->cache = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Madj2k\CoreExtended\Cache\SitemapCache::class);
        }

        return $this->cache;
    }
}
