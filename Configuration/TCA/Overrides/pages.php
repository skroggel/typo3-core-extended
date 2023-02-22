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
    'tx_coreextended_preview_image' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_preview_image',
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
            'txCoreextendedPreviewImage',
            [
                'maxitems' => 1,
                'overrideChildTca' => [
                    'types' => [
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                    ],
                ],
            ],
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
        ),
    ],
    'tx_coreextended_og_image' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_og_image',
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
            'txCoreextendedOgImage',
            [
                'maxitems' => 1,
                'overrideChildTca' => [
                    'types' => [
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                    ],
                ],
            ],
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
        ),
    ],
    'tx_coreextended_file' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_file',
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
            'txCoreextendedFile',
            ['maxitems' => 1],
            'doc,docx,docm,xls,xlsx,pdf,zip'
        ),
        'onChange' => 'reload'
    ],
    'tx_coreextended_cover' => [
        'exclude' => 0,
        'displayCond' => 'FIELD:tx_coreextended_file:=:1',
        'label' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:pages.tx_coreextended_cover',
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
            'txCoreextendedCover',
            [
                'maxitems' => 1,
                'overrideChildTca' => [
                    'types' => [
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                    ],
                ],
            ],
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
        ),
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
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'media','--linebreak--,tx_coreextended_preview_image,--linebreak--,tx_coreextended_og_image,--linebreak--,tx_coreextended_file,--linebreak--,tx_coreextended_cover,--linebreak--','after:media');
