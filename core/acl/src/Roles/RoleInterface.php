<?php

namespace Botble\ACL\Roles;

interface RoleInterface
{
    /**
     * Returns the role's primary key.
     *
     * @return int
     */
    public function getRoleId();

    /**
     * Returns the role's slug.
     *
     * @return string
     */
    public function getRoleSlug();

    /**
     * Returns all users for the role.
     *
     * @return \IteratorAggregate
     */
    public function getUsers();

    /**
     * Returns the users model.
     *
     * @return string
     */
    public static function getUsersModel();

    /**
     * Sets the users model.
     *
     * @param  string $usersModel
     * @return void
     */
    public static function setUsersModel($usersModel);
}
