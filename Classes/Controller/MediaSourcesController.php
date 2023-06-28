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

use Madj2k\CoreExtended\Domain\Repository\MediaSourcesRepository;
use Madj2k\CoreExtended\Utility\GeneralUtility;
use Madj2k\CoreExtended\Utility\QueryUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\RootlineUtility;

/**
 * Class MediaSourcesController
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MediaSourcesController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * mediaSourcesRepository
     *
     * @var \Madj2k\CoreExtended\Domain\Repository\MediaSourcesRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected MediaSourcesRepository $mediaSourcesRepository;


    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;


    /**
     * shows all resources
     *
     * @return void
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function listAction()
    {
        $pagesList = array();

        // get root page of current page
        $rootlinePages = GeneralUtility::makeInstance(RootlineUtility::class, intval($GLOBALS['TSFE']->id))->get();
        if (
            ($rootlinePages[0])
            && ($rootPage = $rootlinePages[0]['uid'])
        ) {

            // now get all pages below the root page
            $childPidList = QueryUtility::getTreeList($rootPage);
            if ($childPidList) {
                $pagesList = GeneralUtility::trimExplode(',', $childPidList);
            }
        }

        $mediaSources = $this->mediaSourcesRepository->findAllWithPublisher($pagesList, false);

        $this->view->assign('mediaSources', $mediaSources);
        $this->view->assign('mediaSourcesSum', count($mediaSources));

    }


    /**
     * shows resources of current page - including those which have been inherited
     *
     * @return void
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     * @todo has no good performance. For the time being we just display a link to the list-page
     */
    public function listPageAction()
    {

        $this->view->assign('pid', $GLOBALS['TSFE']->id);

        if (! $this->settings['resources']['listPid']) {

            // set page list - include current page
            $pagesList = array();

            // check if some other fields are to be included, too
            if ($fieldList = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->settings['resources']['includeFieldsList'], true)) {

                // get PageRepository and rootline
                $rootlinePages = GeneralUtility::makeInstance(RootlineUtility::class, intval($GLOBALS['TSFE']->id))->get();

                // go through all defined fields
                foreach ($fieldList as $includeField) {
                    $cleanedIncludeField = str_replace('pages.', '', $includeField);

                    // if the field refers to tt_content we take the current page
                    if ((strpos($includeField, 'tt_content.') === 0)) {
                        $pagesList[intval($GLOBALS['TSFE']->id)][] = $includeField;

                    } else {

                        // go through the pageTree and check for values in this field
                        // if there is a value we take this page and continue to the next field
                        // otherwise we at least take the rootpage for this field
                        foreach ($rootlinePages as $page => $values) {
                            if (
                                ($values[$cleanedIncludeField] > 0)
                                || ($values['is_siteroot'])
                            ) {

                                $pagesList[intval($values['uid'])][] = $includeField;
                                continue 2;
                                //===
                            }
                        }
                    }
                }

                $this->getLogger()->log(
                    \TYPO3\CMS\Core\Log\LogLevel::INFO,
                    'Showing referenced images of page.'
                );


            } else {
                $this->getLogger()->log(
                    \TYPO3\CMS\Core\Log\LogLevel::INFO,
                    'Showing only link to site-wide image list.'
                );
            }

            $mediaSources = $this->mediaSourcesRepository->findAllWithPublisher($pagesList);
            $this->view->assign('mediaSources', $mediaSources);
        }
    }

    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger(): Logger
    {

        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }

}
