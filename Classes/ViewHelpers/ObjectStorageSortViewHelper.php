<?php
namespace Madj2k\CoreExtended\ViewHelpers;

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
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Class ObjectStorageSortViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ObjectStorageSortViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * @return void
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('objectStorage', ObjectStorage::class, 'The objectStorage to sort', true);
        $this->registerArgument('sortBy', 'string', 'Which property/field to sort by - leave out for numeric sorting based on indexes(keys)', false, false);
        $this->registerArgument('order', 'string', 'ASC or DESC', false, 'ASC');
        $this->registerArgument('sortFlags', 'string', 'Constant name from PHP for SORT_FLAGS: SORT_REGULAR, SORT_STRING, SORT_NUMERIC, SORT_NATURAL, SORT_LOCALE_STRING or SORT_FLAG_CASE', false, 'SORT_REGULAR');
    }


    /**
     * "Render" method - sorts a target list-type target. Either $array or $objectStorage must be specified. If both are,
     * ObjectStorage takes precedence.
     *
     * @return  \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function render(): ObjectStorage
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage $objectStorage */
        $objectStorage = $this->arguments['objectStorage'];
        return $this->sortObjectStorage($objectStorage);
    }


    /**
     * Sort an ObjectStorage
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $objectStorage
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected function sortObjectStorage(ObjectStorage $objectStorage): ObjectStorage
    {

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage $tempObjectStorage */
        $tempObjectStorage = $objectManager->get(ObjectStorage::class);

        // put all into a temporary storage in order to keep the original one untouched
        foreach ($objectStorage as $item) {
            $tempObjectStorage->attach($item);
        }

        // now build an index based on the given field
        $sorted = array();
        foreach ($objectStorage as $index => $item) {
            if ($this->arguments['sortBy']) {
                $index = $this->getSortValue($item);
            }
            // if index already exists, append "1" as string
            while (isset($sorted[$index])) {
                $index .= '1';
            }
            $sorted[$index] = $item;
        }

        // now do the real sorting
        if ($this->arguments['order'] === 'ASC') {
            ksort($sorted, constant($this->arguments['sortFlags']));
        } else {
            krsort($sorted, constant($this->arguments['sortFlags']));
        }

        // now we finally rebuild our object storage
        $storage = $objectManager->get(ObjectStorage::class);
        foreach ($sorted as $item) {
            $storage->attach($item);
        }

        return $storage;
    }


    /**
     * Gets the value to use as sorting value from $object
     *
     * @param mixed $object
     * @return mixed
     */
    protected function getSortValue($object)
    {
        $field = $this->arguments['sortBy'];

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Reflection\ObjectAccess $objectAccess */
        $objectAccess = $objectManager->get(ObjectAccess::class);
        $value = $objectAccess::getProperty($object, $field);

        if ($value instanceof \DateTime) {
            $value = $value->format('U');

        } elseif ($value instanceof ObjectStorage) {
            $value = $value->count();

        } elseif (is_array($value)) {
            $value = count($value);
        }

        return $value;
    }
}
