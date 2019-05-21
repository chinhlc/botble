<?php

namespace Botble\ACL\Permissions;

interface PermissionsInterface
{
    /**
     * Returns if access is available for all given permissions.
     *
     * @param  array|string $permissions
     * @return bool
     */
    public function hasAccess($permissions);

    /**
     * Returns if access is available for any given permissions.
     *
     * @param  array|string $permissions
     * @return bool
     */
    public function hasAnyAccess($permissions);
}
