<?php

return [
    [
        'name' => 'Translation',
        'flag' => 'translations.list',
    ],
    [
        'name' => 'Create',
        'flag' => 'translations.create',
        'parent_flag' => 'translations.list',
    ],
    [
        'name' => 'Edit',
        'flag' => 'translations.edit',
        'parent_flag' => 'translations.list',
    ],
    [
        'name' => 'Delete',
        'flag' => 'translations.delete',
        'parent_flag' => 'translations.list',
    ],
];