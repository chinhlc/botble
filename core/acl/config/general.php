<?php

return [
    'avatar' => [
        'container_dir' => 'avatars',
        'default' => '/vendor/core/images/default-avatar.jpg',
    ],

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    |
    | Please provide the user model
    |
    */

    'users' => [

        'model' => 'Botble\ACL\Users\EloquentUser',

    ],

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    |
    | Please provide the role model
    |
    */

    'roles' => [

        'model' => 'Botble\ACL\Roles\EloquentRole',

    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | Here you may specify the permissions class. ACL ships with two
    | permission types.
    |
    | 'Botble\ACL\Permissions\StandardPermissions'
    | 'Botble\ACL\Permissions\StrictPermissions'
    |
    | "StandardPermissions" will assign a higher priority to the user
    | permissions over role permissions, once a user is allowed or denied
    | a specific permission, it will be used regardless of the
    | permissions set on the role.
    |
    | "StrictPermissions" will deny any permission as soon as it finds it
    | rejected on either the user or any of the assigned roles.
    |
    */

    'permissions' => [

        'class' => 'Botble\ACL\Permissions\StandardPermissions',

    ],

    /*
    |--------------------------------------------------------------------------
    | Checkpoints
    |--------------------------------------------------------------------------
    |
    | When logging in, checking for existing sessions and failed logins occur,
    | you may configure an indefinite number of "checkpoints". These are
    | classes which may respond to each event and handle accordingly.
    | We ship with two, a throttling checkpoint and an activation
    | checkpoint. Feel free to add, remove or re-order
    | these.
    |
    */

    'checkpoints' => [

        'activation',

    ],

    /*
    |--------------------------------------------------------------------------
    | Activations
    |--------------------------------------------------------------------------
    |
    | Here you may specify the activations model used and the time (in seconds)
    | which activation codes expire. By default, activation codes expire after
    | three days. The lottery is used for garbage collection, expired
    | codes will be cleared automatically based on the provided odds.
    |
    */

    'activations' => [

        'model' => 'Botble\ACL\Activations\EloquentActivation',

        'expires' => 259200,

        'lottery' => [2, 100],

    ],
];
