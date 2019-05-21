<?php

namespace Botble\ACL\Activations;

use Botble\ACL\Models\User;

interface ActivationRepositoryInterface
{
    /**
     * Create a new activation record and code.
     *
     * @param  \Botble\ACL\Models\User $user
     * @return \Botble\ACL\Activations\ActivationInterface
     */
    public function create(User $user);

    /**
     * Checks if a valid activation for the given user exists.
     *
     * @param  \Botble\ACL\Models\User $user
     * @param  string $code
     * @return \Botble\ACL\Activations\ActivationInterface|bool
     */
    public function exists(User $user, $code = null);

    /**
     * Completes the activation for the given user.
     *
     * @param  \Botble\ACL\Models\User $user
     * @param  string $code
     * @return bool
     */
    public function complete(User $user, $code);

    /**
     * Checks if a valid activation has been completed.
     *
     * @param  \Botble\ACL\Models\User $user
     * @return \Botble\ACL\Activations\ActivationInterface|bool
     */
    public function completed(User $user);

    /**
     * Remove an existing activation (deactivate).
     *
     * @param  \Botble\ACL\Models\User $user
     * @return bool|null
     */
    public function remove(User $user);

    /**
     * Remove expired activation codes.
     *
     * @return int
     */
    public function removeExpired();
}
