<?php

namespace Botble\ACL\Permissions;

class StandardPermissions implements PermissionsInterface
{
    use PermissionsTrait;

    /**
     * {@inheritDoc}
     */
    protected function createPreparedPermissions()
    {
        $prepared = [];

        if (!empty($this->secondaryPermissions)) {
            foreach ($this->secondaryPermissions as $permissions) {
                $this->preparePermissions($prepared, $permissions);
            }
        }

        if (!empty($this->permissions)) {
            $permissions = [];

            $this->preparePermissions($permissions, $this->permissions);

            $prepared = array_merge($prepared, $permissions);
        }

        return $prepared;
    }
}
