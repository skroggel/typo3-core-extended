<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function (string $extKey) {

        $tempCols = [

            // we have to define the system-fields in order to access them!
            'tstamp' => [
                'config' => [
                    'type' => 'passthrough',
                ]
            ],
            'crdate' => [
                'config' => [
                    'type' => 'passthrough',
                ]
            ],
            'hidden' => [
                'config' => [
                    'type' => 'passthrough',
                ]
            ],
            'deleted' => [
                'config' => [
                    'type' => 'passthrough',
                ]
            ],
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_groups',$tempCols);

    },
    'core_extended'
);

