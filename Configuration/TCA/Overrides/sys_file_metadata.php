<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function (string $extKey) {

        $tempColumnsMedia = [
            'columns' => [
                'tx_coreextended_publisher' => [
                    'exclude' => 1,
                    'label' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.tx_coreextended_publisher',
                    'config' => [
                        'type' => 'input',
                        'size' => 20,
                        'eval' => 'trim'
                    ],
                ],
                'tx_coreextended_source' => [
                    'exclude' => 1,
                    'label' => 'LLL:EXT:core_extended/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.tx_coreextended_source',
                    'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'size' => 5,
                        'foreign_table' => 'tx_coreextended_domain_model_mediasources',
                        'foreign_table_where' => 'ORDER BY name ASC',
                        'minitems' => 0,
                        'maxitems' => 1,
                        'default' => 0,
                        'items' => [
                            ['---', '0'],
                        ],
                    ],
                ],
            ],
        ];

        // insert columns
        $GLOBALS['TCA']['sys_file_metadata'] = array_replace_recursive($GLOBALS['TCA']['sys_file_metadata'], $tempColumnsMedia);

        // replace default fields with ours
        foreach ($GLOBALS['TCA']['sys_file_metadata']['types'] as $type => &$config) {

            // replace old ones
            foreach (['creator', 'creator_tool', 'publisher', 'source', 'copyright'] as $field) {
                $config = str_replace($field . ',', '', $config);
            }

            // insert new ones
            $config = str_replace(
                'LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:tabs.metadata,',
                'LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:tabs.metadata,' . implode(',', array_keys($tempColumnsMedia['columns'])) . ',',
                $config
            );
        }

    },
    'core_extended'
);
