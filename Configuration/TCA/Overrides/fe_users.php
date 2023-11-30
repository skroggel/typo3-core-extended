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
            'starttime' => [
                'config' => [
                    'type' => 'passthrough',
                ]
            ],
            'endtime' => [
                'config' => [
                    'type' => 'passthrough',
                ]
            ],
            'disable' => [
                'config' => [
                    'type' => 'passthrough',
                ]
            ],
            'deleted' => [
                'config' => [
                    'type' => 'passthrough',
                ]
            ],
            'crdate' => [
                'config' => [
                    'type' => 'passthrough',
                ]
            ],
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users',$tempCols);

    },
    'core_extended'
);
