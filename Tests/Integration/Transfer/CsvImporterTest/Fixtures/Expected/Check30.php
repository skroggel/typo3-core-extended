<?php
return [
    'usergroup' => [
        'type' => 'select',
        'foreign_table' => 'fe_groups',
        'foreign_table_where' => 'ORDER BY fe_groups.title',
        'minitems' => 1,
        'maxitems' => 50
    ]
];
