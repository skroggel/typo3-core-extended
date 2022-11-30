<?php
namespace Madj2k\CoreExtended\Routing\Aspect;

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

use Madj2k\CoreExtended\DataHandling\SlugHelper;
use Madj2k\CoreExtended\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PersistedSlugifiedPatternMapper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('persisted_sanitized_routing')) {

    class PersistedSlugifiedPatternMapper extends \Calien\PersistedSanitizedRouting\Routing\Aspect\PersistedSanitizedPatternMapper
    {

        /**
         * @return SlugHelper
         */
        protected function getSlugHelper(): SlugHelper
        {
            if ($this->slugHelper === null) {
                $this->slugHelper = GeneralUtility::makeInstance(
                    SlugHelper::class,
                    $this->tableName,
                    '',
                    []
                );
            }

            return $this->slugHelper;
        }

    }

} else {

    class PersistedSlugifiedPatternMapper
    {
        /**
         * @throws Exception
         */
        public function __construct(array $settings)
        {
            throw new Exception('Extension persisted_sanitized_routing has to be installed');
        }

    }
}
