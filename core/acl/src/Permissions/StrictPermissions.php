<?php

namespace Botble\ACL\Permissions;

class StrictPermissions implements PermissionsInterface
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
            $this->preparePermissions($prepared, $this->permissions);
        }

        return $prepared;
    }
}
