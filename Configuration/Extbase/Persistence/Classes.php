<?php
declare(strict_types = 1);

return [
    \Madj2k\CoreExtended\Domain\Model\BackendUser::class => [
        'tableName' => 'be_users',
    ],
    \Madj2k\CoreExtended\Domain\Model\FrontendUser::class => [
        'tableName' => 'fe_users',
        //'recordType' => 0,
        'properties' => [
            'txExtbaseType' => [
                'fieldName' => 'tx_extbase_type'
            ],
        ],
    ],
    \Madj2k\CoreExtended\Domain\Model\FrontendUserGroup::class => [
        'tableName' => 'fe_groups',
    ],
    \Madj2k\CoreExtended\Domain\Model\File::class => [
        'tableName' => 'sys_file',
        'identifier' => 'identifier'
    ],
    \Madj2k\CoreExtended\Domain\Model\FileReference::class => [
        'tableName' => 'sys_file_reference',
        'properties' => [
            'file' => [
                'fieldName' => 'uid_local'
            ],
        ],
    ],
    \Madj2k\CoreExtended\Domain\Model\FileMetadata::class => [
        'tableName' => 'sys_file_metadata',
        'identifier' => 'identifier'
    ],
    \Madj2k\CoreExtended\Domain\Model\Pages::class => [
        'tableName' => 'pages',
        'properties' => [
            'sysLanguageUid' => [
                'fieldName' => 'sys_language_uid'
            ],
            /*
            'doktype' => [
                'fieldName' => 'doktype'
            ],
            'title' => [
                'fieldName' => 'title'
            ],
            'subtitle' => [
                'fieldName' => 'subtitle'
            ],*/
            'noSearch' => [
                'fieldName' => 'no_search'
            ],
            /*
            'lastUpdated' => [
                'fieldName' => 'lastUpdated'
            ],
            'abstract' => [
                'fieldName' => 'abstract'
            ],*/
        ],
    ],
];
