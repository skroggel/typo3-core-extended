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

/**
 * CsvUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CsvUtility
{
    /**
     * @var resource $csv
     */
    private static $csv;


    /**
     * creates a CSV file with all data of the given objectStorage
     *
     * @param \Iterator $iteratable
     * @param string $fileName
     * @param string $separator The CSV file separator. Default is ";"
     * @return void
     * @throws \Madj2k\CoreExtended\Exception
     */
    public static function createCsv(
        \Iterator $iteratable,
        string $fileName = '',
        string $separator = ';'
    ): void {

        if (! $iteratable instanceof \Countable) {
            throw new Exception('Given object must be an instance of \Countable', 1689155508);
        }

        if ($fileName) {
            self::$csv = fopen($fileName, 'w');

        } else {

            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Pragma: no-cache");

            $fileName = date('Y-m-d', time()) . '.csv';
            self::$csv = fopen('php://output', 'w');
        }

        if (count($iteratable)) {

            // get all getter-methods
            $firstModel = $iteratable->current();
            $allMethods = get_class_methods($firstModel);
            $primaryPropertyGetterMap = [];
            $secondaryPropertyGetterMap = [];
            $primaryProperties = ['uid', 'pid', 'crdate', 'tstamp', 'hidden', 'deleted', 'sysLanguageUid'];

            // get some primary properties in the order we define
            foreach ($primaryProperties as $property) {
                $getter = 'get' . ucfirst($property);
                if (method_exists($firstModel, $getter)) {
                    $primaryPropertyGetterMap[$property] = $getter ;
                }
            }

            // get all other properties
            foreach ($allMethods as $method) {
                if (strpos($method, 'get') === 0) {
                    $property = lcfirst(str_replace('get', '', $method));
                    if (! in_array($property, $primaryProperties)) {
                        $secondaryPropertyGetterMap[$property] = $method;
                    }
                }
            }

            $fullPropertyGetterMap = array_merge($primaryPropertyGetterMap, $secondaryPropertyGetterMap);

            // create CSV header based on first object
            $headings = array_keys($fullPropertyGetterMap);
            fputcsv(self::$csv, $headings, $separator);

            // now add the data
            /** @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $model */
            foreach($iteratable as $model) {

                $row = [];
                foreach ($fullPropertyGetterMap as $key => $getter) {
                    if (method_exists($model, $getter)) {

                        $value =  $model->$getter();
                        if (is_array($value)) {
                            $value = serialize($value);
                        }
                        $row[$key] = $value;
                    }
                }

                fputcsv(self::$csv, $row, $separator);
            }
        }

        fclose(self::$csv);
    }
}

