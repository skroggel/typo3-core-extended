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

use Madj2k\Accelerator\Cache\CacheAbstract;
use Madj2k\CoreExtended\Cache\SitemapCache;
use Madj2k\CoreExtended\Domain\Repository\PagesRepository;
use Madj2k\CoreExtended\Utility\QueryUtility;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
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
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;


    /**
     * pagesRepository
     *
     * @var \Madj2k\CoreExtended\Domain\Repository\PagesRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected PagesRepository $pagesRepository;


    /**
     * action sitemap
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function sitemapAction(): string
    {

        $cache = $this->getCache()->setEntryIdentifier(GeneralUtility::getIndpEnv('HTTP_HOST'));
        if (!$sitemap = $cache->getContent()) {

            $currentPid = $GLOBALS['TSFE']->id;
            $treeList = explode(
                ',',
                QueryUtility::getTreeList($currentPid, 99999, 0, '', true)
            );

            $pages = $this->pagesRepository->findByUidListAndDokTypes($treeList);
            $this->view->assign('pages', $pages);
            $sitemap = $this->view->render();

            // flush caches
            $cache->flushByTag(CacheAbstract::TAG_PLUGIN);

            // save results in cache
            $cache->setContent(
                $sitemap,
                (
                    $this->settings['googleSitemap']['ttl']
                        ? intval($this->settings['googleSitemap']['ttl'])
                        : 21600
                )
            );

            $this->getLogger()->log(
                \TYPO3\CMS\Core\Log\LogLevel::INFO,
               'Successfully rebuilt Google sitemap feed.'
            );

        } else {
            $this->getLogger()->log(
                \TYPO3\CMS\Core\Log\LogLevel::INFO,
                'Successfully loaded Google sitemap from cache.'
            );
        }

        return $sitemap;

    }


    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger(): Logger
    {

        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }


    /**
     * Returns the cache object
     *
     * @return \Madj2k\CoreExtended\Cache\SitemapCache
     */
    protected function getCache(): SitemapCache
    {
        $cache = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(SitemapCache::class);
        $cache->setIdentifier($this->extensionName);
        $cache->setRequest($this->request);
        return $cache;
    }

}
