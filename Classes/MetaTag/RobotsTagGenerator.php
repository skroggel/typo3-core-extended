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
 * Class RobotsTagGenerator
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RobotsTagGenerator
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

        $noIndex = false;
        $noFollow = false;

        if ($pageId = $params['page']['uid']) {

            // get rootLine based on given id
            /** @var \TYPO3\CMS\Core\Utility\RootlineUtility $rootLineUtility */
            $rootLineUtility = new RootlineUtility($pageId);
            $pages = $rootLineUtility->get();

            foreach ($pages as $page) {
                if (
                    isset($page['no_index'])
                    && ($page['no_index'])
                ){
                    $noIndex = true;
                }
                if (
                    isset($page['no_follow'])
                    && ($page['no_follow'])
                ){
                    $noFollow = true;
                }

                if (($noIndex) && ($noFollow)) {
                    break;
                }
            }

            $noIndex = $noIndex ? 'noindex' : 'index';
            $noFollow = $noFollow ? 'nofollow' : 'follow';

            $manager = $metaTagManagerRegistry->getManagerForProperty('robots');
            $manager->addProperty('robots', implode(',', [$noIndex, $noFollow, 'noodp', 'noydir']));
        }
    }
}
