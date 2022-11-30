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
    protected $mediaSourcesRepository = null;

    /**
     * shows all resources
     *
     * @return void
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
            /** @var \TYPO3\CMS\Core\Database\QueryGenerator $queryGenerator */
            $queryGenerator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Database\\QueryGenerator');
            $childPidList = $queryGenerator->getTreeList($rootPage, 999999, 0, 1);
            if ($childPidList) {
                $pagesList = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $childPidList);
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
     */
    public function listPageAction()
    {

        // set page list - include current page
        $pagesList = array();

        // check if some other fields are to be included, too
        if ($fieldList = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->settings['includeFieldsList'], true)) {

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
        }

        $mediaSources = $this->mediaSourcesRepository->findAllWithPublisher($pagesList);
        $this->view->assign('mediaSources', $mediaSources);
    }

}
