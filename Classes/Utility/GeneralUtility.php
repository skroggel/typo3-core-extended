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

use Madj2k\CoreExtended\Exception;
use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\PathUtility;
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
     * @const int
     */
    const RANDOM_STRING_LENGTH = 30;


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
     * Get TypoScript configuration
     *
     * @param string $extension
     * @param string $type
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public static function getTypoScriptConfiguration(
        string $extension = '',
        string $type = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
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

        return [];
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
        return \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode($delimiters[0], $ready, true);
    }


    /**
     * Splits string at upper-case chars
     *
     * @param string $string String to process
     * @param int $key Key to return
     * @return mixed
     * @see http://stackoverflow.com/questions/8577300/explode-a-string-on-upper-case-characters
     */
    public static function splitAtUpperCase(string $string, int $key = -1)
    {
        $result = preg_split('/(?=[A-Z])/', $string, -1, PREG_SPLIT_NO_EMPTY);

        if ($key !== -1) {
            return $result[$key];
        }

        return $result;
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
     * @author Christian Dilger <c.dilger@addorange.de>
     * @author Steffen Kroggel <developer@steffenkroggel.de>
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


    /**
     * creates a random string of the defined length
     *
     * @return string
     * @throws \Exception
     * @see https://www.php.net/manual/en/function.random-bytes.php
     */
    public static function getUniqueRandomString(): string
    {
        $bytes = random_bytes(self::RANDOM_STRING_LENGTH / 2);
        return bin2hex($bytes);
    }


    /**
     * Generate a random number
     *
     * @param int $length
     * @return int
     * @throws \Exception
     */
    public static function getUniqueRandomNumber(int $length = 10): int
    {
        $min = intval(pow(10, $length-1));
        $max = intval(pow(10, $length) -1);

        if ($max > PHP_INT_MAX) {
            throw new \Exception('Number is too big.', 1689765905);
        }

        return random_int($min, $max);
    }


    /**
     * .htaccess-based protection for SimpleFileBackend-Cache
     *
     * @param string $folderToProtect
     * @return bool
     * @throws Exception
     */
    public static function protectFolder (string $folderToProtect): bool
    {
        // check if given path is absolute and valid
        if (
            !static::isAbsPath($folderToProtect)
            || !static::validPathStr($folderToProtect)
        ) {
            throw new Exception('Given path has to be a valid absolute path.', 1682006516);
        }

        // if the folder is above the publicPath (which is normally the documentRoot), we need no explicit protection.
        if (! static::isFirstPartOfStr($folderToProtect, Environment::getPublicPath())){
            return true;
        }

        $sanitizedFolderToProtect = DIRECTORY_SEPARATOR . trim($folderToProtect, '/') . DIRECTORY_SEPARATOR;
        $sanitizedPublicPath = DIRECTORY_SEPARATOR . trim(Environment::getPublicPath(), '/') . DIRECTORY_SEPARATOR;
        $hash = substr(md5($folderToProtect), 0, 12);

        $pathToApacheFile =  $sanitizedFolderToProtect  . '.htaccess';
        $pathToNginxFile =  $sanitizedPublicPath . DIRECTORY_SEPARATOR . 'ext_' . $hash . '.nginx';

        // create .htaccess if there is none!
        $errorCnt = 0;
        if (! file_exists($pathToApacheFile)) {

            $content = '# This file is automatically generated.' . "\n" .
                '# Please to not modify it manually because all changes may be lost.' . "\n\n" .

                '# Apache < 2.3' . "\n" .
                '<IfModule !mod_authz_core.c>'. "\n" .
                "\t" . 'Order allow,deny'. "\n" .
                "\t" . 'Deny from all'. "\n" .
                "\t" . 'Satisfy All'. "\n" .
                '</IfModule>'. "\n\n" .

                '# Apache ≥ 2.3' . "\n" .
                '<IfModule mod_authz_core.c>'. "\n" .
                "\t" . 'Require all denied' . "\n" .
                '</IfModule>';

            if (! file_put_contents($pathToApacheFile, $content)) {
                $errorCnt++;
            }
        }

        // create .nginx-file if there is none!
        if (! file_exists($pathToNginxFile)) {

            $content = '# This file is automatically generated.' . "\n" .
                '# Please to not modify it manually because all changes may be lost.' . "\n\n" .
                'location /' . trim(PathUtility::getRelativePath($sanitizedPublicPath, $folderToProtect), '/') . ' {'. "\n" .
                "\t" . 'deny all;' . "\n" .
                "\t" . 'satisfy all;'  . "\n" .
                '}';

            if (! file_put_contents($pathToNginxFile, $content)) {
                $errorCnt++;
            }
        }

        return $errorCnt == 0;
    }
}
