<?php
namespace Madj2k\CoreExtended\MetaTag;

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

use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;

/**
 * Class MetaTagGenerator
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MetaTagGenerator
{
    /**
     * Generate the meta tags that can be set in backend and add them to frontend by using the MetaTag API
     *
     * @param array $params
     * @return void
     */
    public function generate(array $params): void
    {
        $metaTagManagerRegistry = GeneralUtility::makeInstance(MetaTagManagerRegistry::class);

        $description = '';
        $keywords = '';
        if (!empty($params['page']['description'])) {
          $description = $params['page']['description'];
        }

        if (!empty($params['page']['keywords'])) {
            $keywords = $params['page']['keywords'];
        }

        if (
            (
               (empty($description))
               || (empty($keywords))
            )
            && ($pageId = $params['page']['uid'])
        ){

            // get rootLine based on given id
            /** @var \TYPO3\CMS\Core\Utility\RootlineUtility $rootLineUtility */
            $rootLineUtility = new RootlineUtility($pageId);
            $pages = $rootLineUtility->get();

            foreach ($pages as $page) {
                if (
                    empty($description)
                    && isset($page['description'])
                    && ($page['description'])
                ){
                    $description = $page['description'];
                }

                if (
                    (empty($keywords))
                    && isset($page['keywords'])
                    && ($page['keywords'])
                ){
                    $keywords = $page['keywords'];
                }

                if (!empty($description) && !empty($keywords)) {
                    break;
                }
            }
        }

        if (!empty($description)) {
            $manager = $metaTagManagerRegistry->getManagerForProperty('description');
            $manager->addProperty('description', $description);
        }

        if (!empty($keywords)) {
            $manager = $metaTagManagerRegistry->getManagerForProperty('keywords');
            $manager->addProperty('keywords', $keywords);
        }
    }
}
