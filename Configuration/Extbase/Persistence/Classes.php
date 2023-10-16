<?php
declare(strict_types = 1);

return [
    \Madj2k\CoreExtended\Domain\Model\BackendUser::class => [
        'tableName' => 'be_users',
    ],
    \Madj2k\CoreExtended\Domain\Model\FrontendUser::class => [
        'tableName' => 'fe_users',
        'recordType' => 0,
        'properties' => [
            'tstamp' => [
                'fieldName' => 'tstamp'
            ],
            'starttime' => [
                'fieldName' => 'starttime'
            ],
            'endtime' => [
                'fieldName' => 'endtime'
            ],
            'crdate' => [
                'fieldName' => 'crdate'
            ],
            'disable' => [
                'fieldName' => 'disable'
            ],
            'deleted' => [
                'fieldName' => 'deleted'
            ],
            'password' => [
                'fieldName' => 'password'
            ],
            'txExtbaseType' => [
                'fieldName' => 'tx_extbase_type'
            ],
        ],
    ],
    \Madj2k\CoreExtended\Domain\Model\FrontendUserGroup::class => [
        'tableName' => 'fe_groups',
        'properties' => [
            'tstamp' => [
                'fieldName' => 'tstamp'
            ],
            'crdate' => [
                'fieldName' => 'crdate'
            ],
            'hidden' => [
                'fieldName' => 'hidden'
            ],
            'deleted' => [
                'fieldName' => 'deleted'
            ],
        ],
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
            'uid' => [
                'fieldName' => 'uid'
            ],
            'pid' => [
                'fieldName' => 'pid'
            ],
            'sysLanguageUid' => [
                'fieldName' => 'sys_language_uid'
            ],
            'sorting' => [
                'fieldName' => 'sorting'
            ],
            'tstamp' => [
                'fieldName' => 'tstamp'
            ],
            'crdate' => [
                'fieldName' => 'crdate'
            ],
            'hidden' => [
                'fieldName' => 'hidden'
            ],
            'doktype' => [
                'fieldName' => 'doktype'
            ],
            'title' => [
                'fieldName' => 'title'
            ],
            'subtitle' => [
                'fieldName' => 'subtitle'
            ],
            'noSearch' => [
                'fieldName' => 'no_search'
            ],
            'lastUpdated' => [
                'fieldName' => 'lastUpdated'
            ],
            'abstract' => [
                'fieldName' => 'abstract'
            ],
        ],
    ],
];
