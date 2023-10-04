<?php
return [
    'usergroup' => [
        'type' => 'select',
        'foreign_table' => 'fe_groups',
        'foreign_table_where' => 'ORDER BY fe_groups.title',
        'minitems' => 1,
        'maxitems' => 50
    ],
    'image' => [
        'type' => 'inline',
        'foreign_table' => 'sys_file_reference',
        'foreign_field' => 'uid_foreign',
        'foreign_sortby' => 'sorting_foreign',
        'foreign_table_field' => 'tablenames',
        'foreign_match_fields' => [
            'fieldname' => 'image'
        ],
        'foreign_label' => 'uid_local',
        'foreign_selector' => 'uid_local',
        'minitems' => 0,
        'maxitems' => 6
    ]
];
