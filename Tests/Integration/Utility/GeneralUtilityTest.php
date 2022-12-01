<?php
namespace Madj2k\CoreExtended\Tests\Unit\Utility;

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

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * GeneralUtilityTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GeneralUtilityTest extends FunctionalTestCase
{

    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/GeneralUtilityTest/Fixtures';


    /**
     * @var string[]
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/core_extended',
    ];

    /**
     * @var string[]
     */
    protected $coreExtensionsToLoad = [ ];



    /**
     * @var \Madj2k\CoreExtended\Utility\GeneralUtility
     */
    private $subject;


    /**
     * Setup
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = GeneralUtility::makeInstance(\Madj2k\CoreExtended\Utility\GeneralUtility::class);

    }


    //=============================================

    /**
     * @test
     */
    public function arrayZipMergeWithTwoArrays()
    {

        /**
         * Scenario:
         *
         * Given an array A with non-continuous numeric keys
         * Given that array A as four key-value-pairs
         * Given an array B with string-keys
         * Given that array B as two key-value-pairs
         * When the method is called
         * Then an array is returned
         * Then the array contains six items
         * Then the array has continuous numeric keys
         * Then the array contains the array-items of both arrays in zipper-method
         */

        $array1 = [
            100 => 'test1.0',
            200 => 'test1.1',
            300 => 'test1.2',
            400 => 'test1.3'
        ];

        $array2 = [
            'a' => 'test2.0',
            'b' => 'test2.1'
        ];

        $result = $this->subject::arrayZipMerge($array1, $array2);
        self::assertIsArray($result);
        self::assertCount(6, $result);
        self::assertEquals('test1.0', $result[0]);
        self::assertEquals('test2.0', $result[1]);
        self::assertEquals('test1.1', $result[2]);
        self::assertEquals('test2.1', $result[3]);
        self::assertEquals('test1.2', $result[4]);
        self::assertEquals('test1.3', $result[5]);
    }

    /**
     * @test
     */
    public function arrayZipMergeWithThreeArrays()
    {

        /**
         * Scenario:
         *
         * Given an array A with non-continuous numeric keys
         * Given that array A as four key-value-pairs
         * Given an array B with string-keys
         * Given that array B as two key-value-pairs
         * Given an array C with mixed-keys
         * Given that array C as three key-value-pairs
         * When the method is called
         * Then an array is returned
         * Then the array contains nine items
         * Then the array has continuous numeric keys
         * Then the array contains the array-items of both arrays in zipper-method
         */

        $array1 = [
            100 => 'test1.0',
            200 => 'test1.1',
            300 => 'test1.2',
            400 => 'test1.3'
        ];

        $array2 = [
            'a' => 'test2.0',
            'b' => 'test2.1'
        ];

        $array3 = [
            'a' => 'test3.0',
            200  => 'test3.1',
            '445'  => 'test3.2'
        ];

        $result = $this->subject::arrayZipMerge($array1, $array2, $array3);
        self::assertIsArray($result);
        self::assertCount(9, $result);
        self::assertEquals('test1.0', $result[0]);
        self::assertEquals('test2.0', $result[1]);
        self::assertEquals('test3.0', $result[2]);
        self::assertEquals('test1.1', $result[3]);
        self::assertEquals('test2.1', $result[4]);
        self::assertEquals('test3.1', $result[5]);
        self::assertEquals('test1.2', $result[6]);
        self::assertEquals('test3.2', $result[7]);
        self::assertEquals('test1.3', $result[8]);

    }


    //=============================================


    /**
     * @test
     */
    public function slugifyWithCommonString()
    {

        /**
         * Scenario:
         *
         * Given is a string with upper and lowercase letters and whitespaces and a dot
         * When the method is called
         * Then a lowercase string without dot and with hyphens is returned
         */


        $string = "Fischers Fritz fischt frische Fische.";

        $result = $this->subject::slugify($string);

        self::assertEquals('fischers-fritz-fischt-frische-fische', $result);
    }


    /**
     * @test
     */
    public function slugifyWithCommonStringAndAdditionalWhitespaces()
    {

        /**
         * Scenario:
         *
         * Given is a common string with additional whitespaces
         * When the method is called
         * Then a lowercase string without dot and with hyphens is returned
         */

        $string = "Fischers      Fritz fischt frische Fische.";

        $result = $this->subject::slugify($string);

        self::assertEquals('fischers-fritz-fischt-frische-fische', $result);
    }


    /**
     * @test
     */
    public function slugifyWithCommonStringAndDifferentSeparator()
    {
        /**
         * Scenario:
         *
         * Given is a common string
         * Given is a different separator
         * When the method is called
         * Then the common string is returned with the different separator
         */

        $string = "Fischers Fritz fischt frische Fische.";
        $separator = "_";

        $result = $this->subject::slugify($string, $separator);

        self::assertEquals('fischers_fritz_fischt_frische_fische', $result);
    }



    /**
     * @test
     */
    public function slugifyWithUmlautsAndSpecialSigns()
    {

        /**
         * Scenario:
         *
         * Given a string with a lot of special signs
         * When the method is called
         * Then a lowercase string with hyphens, converted umlauts and removed special signs is returned
         */


        $string = "ÄÜÖß!§§$% Fischers &/()= Fritz.";

        $result = $this->subject::slugify($string);

        self::assertEquals('aeueoess-fischers-fritz', $result);
    }


    /**
     * @test
     */
    public function slugifyWithStringWhichNeedNoChanges()
    {

        /**
         * Scenario:
         *
         * Given a string with a lot of special signs
         * When the method is called
         * Then a lowercase string without dot and hyphens is returned
         */


        $string = "fischers-super-test-abcdefg123456";

        $result = $this->subject::slugify($string);

        self::assertEquals($string, $result);
    }


    /**
     * @test
     */
    public function slugifyWithAtSymbol()
    {

        /**
         * Scenario:
         *
         * Given a string with a @ smybol
         * When the method is called
         * Then the @ is converted to "at"
         */

        $string = "@";

        $result = $this->subject::slugify($string);

        self::assertEquals('at', $result);
    }

    //=============================================

    /**
     * TearDown
     */
    protected function tearDown(): void
    {
        parent::tearDown();


    }


}
