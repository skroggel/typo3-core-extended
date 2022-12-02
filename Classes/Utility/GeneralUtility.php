<?php

namespace Madj2k\CoreExtended\Utility;

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

use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class GeneralUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GeneralUtility extends \TYPO3\CMS\Core\Utility\GeneralUtility
{

    /**
     * @var array Setter/Getter underscore transformation cache
     */
    protected static array $_underscoreCache = [];

    /**
     * @var array Setter/Getter backslash transformation cache
     */
    protected static array $_backslashCache = [];

    /**
     * @var array Setter/Getter camlize transformation cache
     */
    protected static array $_camelizeCache = [];


    /**
     * Converts field names for setters and getters
     * Uses cache to eliminate unnecessary preg_replace
     *
     * @param string $name
     * @return string
     */
    public static function underscore(string $name): string
    {
        if (isset(self::$_underscoreCache[$name])) {
            return self::$_underscoreCache[$name];
        }

        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        self::$_underscoreCache[$name] = $result;

        return $result;
    }


    /**
     * Converts field names for setters and getters
     * Uses cache to eliminate unnecessary preg_replace
     *
     * @param string $name
     * @return string
     */
    public static function backslash(string $name): string
    {

        if (isset(self::$_backslashCache[$name])) {
            return self::$_backslashCache[$name];
        }

        $result = preg_replace('/(.)([A-Z])/', "$1\\\\$2", $name);
        self::$_backslashCache[$name] = $result;

        return $result;
    }


    /**
     * Converts field names for setters and getters
     * Uses cache to eliminate unnecessary preg_replace
     *
     * @param string $name
     * @param string $destSep
     * @param string $srcSep
     * @return string
     */
    public static function camelize(string $name, string $destSep = '', string $srcSep = '_'): string
    {
        if (isset(self::$_camelizeCache[$name])) {
            return self::$_camelizeCache[$name];
        }

        $result = lcfirst(str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $name))));
        self::$_camelizeCache[$name] = $result;

        return $result;
    }


    /**
     * Allows multiple delimiter replacement for explode
     *
     * @param array  $delimiters
     * @param string $string
     * @return array
     */
    public static function multiExplode(array $delimiters, string $string): array
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $result = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode($delimiters[0], $ready, true);
        return $result;
    }


    /**
     * Splits string at upper-case chars
     *
     * @param string  $string String to process
     * @param integer $key Key to return
     * @return array
     * @see http://stackoverflow.com/questions/8577300/explode-a-string-on-upper-case-characters
     */
    public static function splitAtUpperCase(string $string, $key = null)
    {
        $result = preg_split('/(?=[A-Z])/', $string, -1, PREG_SPLIT_NO_EMPTY);

        if ($key !== null) {
            return $result[$key];
        }

        return $result;
    }


    /**
     * Get TypoScript configuration
     *
     * @param string $extension
     * @param string $type
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public static function getTypoScriptConfiguration(
        string $extension = '',
        string $type = \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
    ): array {

        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);

        // load configuration
        if ($configurationManager) {
            $settings = $configurationManager->getConfiguration($type, $extension);
            if (
                ($settings)
                && (is_array($settings))
            ) {
                return $settings;
            }
        }

        return array();
    }


    /**
     * Recursively fetch all descendants of a given page - slightly modified version of core-method
     *
     * @param int $id uid of the page
     * @param int $depth
     * @param int $begin
     * @param string $permClause
     * @return string comma separated list of descendant pages
     * @see \TYPO3\CMS\Core\Database\QueryGenerator::getTreeList()
     */
    static public function getTreeList(int $id, int $depth, int $begin = 0, string $permClause = ''): string
    {

        if ($id < 0) {
            $id = abs($id);
        }
        if ($begin === 0) {
            $theList = $id;
        } else {
            $theList = '';
        }
        if ($id && $depth > 0) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
            $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $statement = $queryBuilder->select('uid', 'tx_coreextended_no_index')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)),
                    QueryHelper::stripLogicalOperatorPrefix($permClause)
                )
                ->execute();

            while ($row = $statement->fetch()) {
                if ($begin <= 0) {
                    $theList .= ',' . $row['uid'];
                }
                if (
                    ($depth > 1)
                    && (! $row['tx_coreextended_no_index'])
                ){
                    $theSubList = self::getTreeList($row['uid'], $depth - 1, $begin - 1, $permClause);
                    if (!empty($theList) && !empty($theSubList) && ($theSubList[0] !== ',')) {
                        $theList .= ',';
                    }
                    $theList .= $theSubList;
                }
            }
        }

        return $theList;
    }


    /**
     * Merges arrays by numeric key and sorts them in zipper procedure
     *
     * @param array ...$arrays
     * @return array
     */
    static public function arrayZipMerge(
        array ...$arrays
    ): array {

        // find array with highest number of keys
        $maxCount = 0;
        foreach ($arrays as $array) {
            if (count($array) > $maxCount) {
                $maxCount = count($array);
            }
        }

        // move all keys to new numeric index
        foreach($arrays as $key => $array) {
            $arrays[$key] = array_values($array);
        }

        // now rebuild array
        $result = [];
        for ($i = 0; $i < $maxCount; $i++) {
            foreach ($arrays as $array) {
                if (isset($array[$i])) {
                    $result[] = $array[$i];
                }
            }
        }

        return $result;
    }


    /**
     * Merges array recursively but behaves like array_merge
     *
     * @param array $array1
     * @param array $array2
     * @return array
     * @author Daniel <daniel@danielsmedegaardbuus.dk>
     * @author Gabriel Sobrinho <gabriel.sobrinho@gmail.com>
     * @author fantomx1 <fantomx1@gmail.om>
     * @author Steffen Kroggel <developer@steffenkroggel.de>
     */
    static function arrayMergeRecursiveDistinct (array &$array1, array &$array2 ): array
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {

            // numeric keys are simply added
            if (is_numeric($key)) {
                $merged [] = $value;

            } else {

                // recursive call if array
                if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = self::arrayMergeRecursiveDistinct($merged[$key], $value);

                    // associative keys are overridden
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }


    /**
     * Sanitizes slugs and removes slashes, too
     *
     * @param string $slug
     * @param string $separator
     * @return string
     * @see \TYPO3\CMS\Core\DataHandling\SlugHelper
     */
    static public function slugify(string $slug, string $separator = '-'): string
    {
        // use "mb_strtolower" instead of "strtolower" for ÄÜÖ
        $slug = mb_strtolower($slug, 'utf-8');
        $slug = strip_tags($slug);

        // Convert some special tokens (space, "_" and "-") to the separator character
        $slug = preg_replace('/[ \t\x{00A0}\-+_]+/u', $separator, $slug);

        // handle german umlauts separately
        $slug = str_replace(['ä', 'ä', 'ö', 'ü', 'ß', '/'], ['ae', 'ae', 'oe', 'ue', 'ss', $separator], $slug);

        // Replace @ with the word '-at-'
        $slug = str_replace('@', $separator . 'at' . $separator, $slug);

        // Convert extended letters to ascii equivalents
        // The specCharsToASCII() converts "€" to "EUR"
        $slug = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(CharsetConverter::class)->specCharsToASCII('utf-8', $slug);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $slug = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', $slug);

        // Convert multiple fallback characters to a single one
        $slug = preg_replace('/' . preg_quote($separator) . '{2,}/', $separator, $slug);

        // Ensure slug is lowercase after all replacement was done
        $slug = mb_strtolower($slug, 'utf-8');

        return trim($slug, $separator);
    }


}
