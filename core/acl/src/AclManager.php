<?php

namespace Botble\ACL;

use Botble\ACL\Activations\ActivationRepositoryInterface;
use Botble\ACL\Models\User;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\ACL\Roles\RoleRepositoryInterface;
use InvalidArgumentException;

class AclManager
{

    /**
     * The User repository.
     *
     * @var \Botble\ACL\Repositories\Interfaces\UserInterface
     */
    protected $users;

    /**
     * The Role repository.
     *
     * @var \Botble\ACL\Roles\RoleRepositoryInterface
     */
    protected $roles;

    /**
     * The Activations repository.
     *
     * @var \Botble\ACL\Activations\ActivationRepositoryInterface
     */
    protected $activations;

    /**
     * Create a new Acl instance.
     *
     * @param  \Botble\ACL\Repositories\Interfaces\UserInterface $users
     * @param  \Botble\ACL\Roles\RoleRepositoryInterface $roles
     * @param  \Botble\ACL\Activations\ActivationRepositoryInterface $activations
     */
    public function __construct(
        UserInterface $users,
        RoleRepositoryInterface $roles,
        ActivationRepositoryInterface $activations
    )
    {
        $this->users = $users;

        $this->roles = $roles;

        $this->activations = $activations;
    }

    /**
     * Activates the given user.
     *
     * @param  mixed $user
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function activate($user)
    {
        if (!$user instanceof User) {
            throw new InvalidArgumentException('No valid user was provided.');
        }

        event('acl.activating', $user);

        $activations = $this->getActivationRepository();

        $activation = $activations->create($user);

        event('acl.activated', [$user, $activation]);

        return $activations->complete($user, $activation->getCode());
    }

    public function getUserRepository()
    {
        return $this->users;
    }

    /**
     * Returns the role repository.
     *
     * @return \Botble\ACL\Roles\RoleRepositoryInterface
     */
    public function getRoleRepository()
    {
        return $this->roles;
    }

    /**
     * Sets the role repository.
     *
     * @param  \Botble\ACL\Roles\RoleRepositoryInterface $roles
     * @return void
     */
    public function setRoleRepository(RoleRepositoryInterface $roles)
    {
        $this->roles = $roles;
    }

    /**
     * Returns the activations repository.
     *
     * @return \Botble\ACL\Activations\ActivationRepositoryInterface
     */
    public function getActivationRepository()
    {
        return $this->activations;
    }

    /**
     * Sets the activations repository.
     *
     * @param  \Botble\ACL\Activations\ActivationRepositoryInterface $activations
     * @return void
     */
    public function setActivationRepository(ActivationRepositoryInterface $activations)
    {
        $this->activations = $activations;
    }
}
