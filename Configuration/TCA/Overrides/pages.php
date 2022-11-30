<?php

$tempColumnsPages = [

    'tx_coreextended_alternative_title' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_alternative_title',
        'config' => [
            'type' => 'input',
            'size' => 50,
            'eval' => 'trim'
        ],
    ],

	'tx_coreextended_fe_layout_next_level' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_fe_layout_next_level',
		'config' => [
		    'type' => 'select',
            'renderType' => 'selectSingle',
			'items' => [
				['LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_fe_layout_next_level.I.0', '0'],
				['LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_fe_layout_next_level.I.1', '1'],
				['LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_fe_layout_next_level.I.2', '2'],
				['LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_fe_layout_next_level.I.3', '3'],
			],
			'size' => 1,
			'maxitems' => 1,
		],
	],
    'tx_coreextended_no_index' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_no_index',
        'config' => [
            'type' => 'check',
            'default' => 0,
            'exclude' => true,
            'items' => [
                '1' => [
                    '0' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_no_index.I.enabled'
                ],
            ],
        ],
    ],
    'tx_coreextended_no_follow' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_no_follow',
        'config' => [
            'type' => 'check',
            'default' => 0,
            'exclude' => true,
            'items' => [
                '1' => [
                    '0' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_no_follow.I.enabled'
                ],
            ],
        ],
    ],
];


//===========================================================================
// Add fields
//===========================================================================
// Add TCA
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages',$tempColumnsPages);

// Add field to the existing palette
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'title','--linebreak--,tx_coreextended_alternative_title','after:title');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','tx_coreextended_fe_layout_next_level','after:layout');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'visibility','--linebreak--,tx_coreextended_no_index, tx_coreextended_no_follow','after:nav_hide');
