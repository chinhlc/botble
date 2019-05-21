<?php

namespace Botble\ACL\Permissions;

interface PermissibleInterface
{
    /**
     * Returns the permissions instance.
     *
     * @return \Botble\ACL\Permissions\PermissionsInterface
     */
    public function getPermissionsInstance();

    /**
     * Adds a permission.
     *
     * @param  string $permission
     * @param  bool $value
     * @return \Botble\ACL\Permissions\PermissibleInterface
     */
    public function addPermission($permission, $value = true);

    /**
     * Updates a permission.
     *
     * @param  string $permission
     * @param  bool $value
     * @param  bool $create
     * @return \Botble\ACL\Permissions\PermissibleInterface
     */
    public function updatePermission($permission, $value = true, $create = false);

    /**
     * Removes a permission.
     *
     * @param  string $permission
     * @return \Botble\ACL\Permissions\PermissibleInterface
     */
    public function removePermission($permission);
}
