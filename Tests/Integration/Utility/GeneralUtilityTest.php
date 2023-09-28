<?php
namespace Madj2k\CoreExtended\Tests\Integration\Utility;

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

use Madj2k\CoreExtended\Utility\StringUtility;
use Madj2k\Postmaster\Cache\RenderCache;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * GeneralUtilityTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
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
    public function arrayMergeRecursiveDistinctMergesOneDimensionalArray ()
    {

        /**
         * Scenario:
         *
         * Given array1 has one dimension with keys and numbers
         * Given array2 has one dimension with keys and numbers
         * When arrayMergeRecursiveDistinct is called
         * Then the arrays are merged like array_merge does
         */

        $array1 = [
            'farbe' => 'rot',
            2,
            4
        ];

        $array2 = [
            'a',
            'b',
            'farbe' => 'grün',
            'form' => 'trapezoid',
            4
        ];

        $expected = [
            'farbe' => 'grün',
            0 => 2,
            1 => 4,
            2 => 'a',
            3 => 'b',
            'form' => 'trapezoid',
            4 => 4,
        ];

        $result = array_merge ($array1, $array2);
        $result2 = $this->subject::arrayMergeRecursiveDistinct($array1, $array2);
        self::assertEquals($expected, $result);
        self::assertEquals($expected, $result2);
    }


    /**
     * @test
     */
    public function arrayMergeRecursiveDistinctMergesTwoDimensionalArray ()
    {

        /**
         * Scenario:
         *
         * Given array1 has two dimensions with keys and numbers
         * Given array2 has two dimension with keys and numbers
         * When arrayMergeRecursiveDistinct is called
         * Then the arrays are merged like array_merge does, but recursively
         */

        $array1 = [
            'farbe' => 'rot',
            2,
            4,
            'sub' => [
                'farbe' => 'rot',
                2,
                3,
            ]
        ];

        $array2 = [
            'a',
            'b',
            'farbe' => 'grün',
            'form' => 'trapezoid',
            4,
            'sub' => [
                'a',
                'b',
                'farbe' => 'blau',
                'form' => 'trapezoid',
                3,
            ]
        ];

        $expected = [
            'farbe' => 'grün',
            0 => 2,
            1 => 4,
            'sub' => [
                'farbe' => 'blau',
                0 => 2,
                1 => 3,
                2 => 'a',
                3 => 'b',
                'form' => 'trapezoid',
                4 => 3,
            ],
            2 => 'a',
            3 => 'b',
            'form' => 'trapezoid',
            4 => 4,
        ];

        $result = $this->subject::arrayMergeRecursiveDistinct($array1, $array2);
        self::assertEquals($expected, $result);
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

    #==============================================================================

    /**
     * @test
     * @throws \Exception
     */
    public function getUniqueRandomStringGeneratesRandomStrings ()
    {
        /**
         * Scenario:
         *
         * When the method is called multiple times
         * Then each time a string is returned
         * Then every string is unique
         */

        $arrayOfStrings = [];
        for ($i = 1; $i <= 10000; $i++) {

            $string = $this->subject::getUniqueRandomString();
            self::assertNotContains($string, $arrayOfStrings);
            $arrayOfStrings[] = $string;
        }
    }


    /**
     * @test
     * @throws \Exception
     */
    public function getUniqueRandomStringReturnsStringOfDefinedLength ()
    {
        /**
         * Scenario:
         *
         * When the method is called multiple times
         * Then a string is returned
         * Then the string has the length of GeneralUtility::RANDOM_STRING_LENGTH
         */

        $string = $this->subject::getUniqueRandomString();
        self::assertEquals($this->subject::RANDOM_STRING_LENGTH, strlen($string));

    }

    //=============================================
    /**
     * @test
     * @throws \Exception
     */
    public function getUniqueRandomNumberThrowsException ()
    {
        /**
         * Scenario:
         *
         * When the method is called with a number that is too big
         * Then an exception-instance of \Madj2k\CoreExtended\Exception is thrown
         * Then the exception-code is 1689765905
         */
        self::expectException(\Madj2k\CoreExtended\Exception::class);
        self::expectExceptionCode(1689765905);

        $string = $this->subject::getUniqueRandomNumber(25);
        self::assertEquals(8, strlen(strval($string)));

    }

    /**
     * @test
     * @throws \Exception
     */
    public function getUniqueRandomNumberGeneratesRandomNumbers ()
    {
        /**
         * Scenario:
         *
         * When the method is called multiple times
         * Then each time a number is returned
         * Then every number is unique
         */

        $arrayOfNumbers = [];
        for ($i = 1; $i <= 10; $i++) {

            $number = $this->subject::getUniqueRandomNumber();
            self::assertNotContains($number, $arrayOfNumbers);
            $arrayOfNumbers[] = $number;
        }
    }


    /**
     * @test
     * @throws \Exception
     */
    public function getUniqueRandomNumberReturnsNumberOfDefinedLength ()
    {
        /**
         * Scenario:
         *
         * When the method is called with a defined length-parameter
         * Then a number is returned
         * Then the number has the given length
         */

        $string = $this->subject::getUniqueRandomNumber(8);
        self::assertEquals(8, strlen(strval($string)));

    }

    //=============================================

    /**
     * @test
     * @throws \Exception
     */
    public function protectFolderThrowsException()
    {

        /**
         * Scenario:
         *
         * Given a relative path
         * When the method is called
        * Then an exception-instance of \Madj2k\CoreExtended\Exception is thrown
         * Then the exception-code is 1682006516
         */
        self::expectException(\Madj2k\CoreExtended\Exception::class);
        self::expectExceptionCode(1682006516);

        $folderPath = '../test';
        $this->subject::protectFolder($folderPath);
    }


    /**
     * @test
     * @throws \Exception
     */
    public function protectFolderReturnsTrueAndWritesFiles()
    {

        /**
         * Scenario:
         *
         * Given the path to a folder in the documentRoot
         * When the method is called
         * Then true is returned
         * Then the htaccess-file is written to the given folder
         * Then the nginx-file is written to the publicWeb-dir
         * Then the nginx-file contains the relative path to the var-dir as location-directive
         */
        $folderPath = DIRECTORY_SEPARATOR . trim(Environment::getPublicPath(), '/') . DIRECTORY_SEPARATOR . 'typo3conf' . DIRECTORY_SEPARATOR;
        $publicPath = DIRECTORY_SEPARATOR . trim(Environment::getPublicPath(), '/') . DIRECTORY_SEPARATOR;
        $folderPathRelative = trim(PathUtility::getRelativePath($publicPath, $folderPath), '/');
        $hash = substr(md5($folderPath), 0, 12);

        self::assertTrue($this->subject::protectFolder($folderPath));

        self::assertFileExists($folderPath . '.htaccess');
        self::assertFileExists($publicPath . 'ext_' . $hash . '.nginx');

        $content = file_get_contents($publicPath . 'ext_' . $hash . '.nginx');
        self::assertStringContainsString('location /' . $folderPathRelative . ' {', $content);

        // remove files again!
        unlink($folderPath . '.htaccess');
        unlink($publicPath . 'ext_' . $hash . '.nginx');

    }

    /**
     * @test
     * @throws \Exception
     */
    public function protectFolderReturnsTrueAndWritesNoFiles()
    {

        /**
         * Scenario:
         *
         * Given the path to a folder above in the documentRoot
         * When the method is called
         * Then true is returned
         * Then no htaccess-file is written to the given folder
         * Then no nginx-file is written to the publicWeb-dir
         */
        $explodedPath = GeneralUtility::trimExplode(DIRECTORY_SEPARATOR, Environment::getPublicPath(), true) ;
        array_pop($explodedPath);
        $folderPath = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $explodedPath) . DIRECTORY_SEPARATOR;
        $publicPath = DIRECTORY_SEPARATOR . trim(Environment::getPublicPath(), '/') . DIRECTORY_SEPARATOR;
        $hash = substr(md5($folderPath), 0, 12);

        self::assertTrue($this->subject::protectFolder($folderPath));

        self::assertFileDoesNotExist($folderPath . '.htaccess');
        self::assertFileDoesNotExist($publicPath . 'ext_' . $hash . '.nginx');

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
