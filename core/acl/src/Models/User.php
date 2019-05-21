<?php

namespace Botble\ACL\Models;

use Botble\ACL\Activations\EloquentActivation;
use Botble\ACL\Notifications\ResetPasswordNotification;
use Botble\ACL\Permissions\PermissibleInterface;
use Botble\ACL\Permissions\PermissibleTrait;
use Botble\ACL\Permissions\PermissionsTrait;
use Botble\ACL\Roles\EloquentRole;
use Botble\ACL\Roles\RoleableInterface;
use Botble\ACL\Roles\RoleInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements RoleableInterface, PermissibleInterface
{
    use PermissibleTrait;
    use Notifiable;
    use HasApiTokens;
    use Impersonate;

    /**
     * {@inheritDoc}
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'first_name',
        'last_name',
        'address',
        'password',
        'secondary_address',
        'dob',
        'job_position',
        'phone',
        'secondary_phone',
        'secondary_email',
        'gender',
        'website',
        'skype',
        'facebook',
        'twitter',
        'google_plus',
        'youtube',
        'github',
        'interest',
        'about',
        'super_user',
        'profile_image',
        'permissions'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'dob',
    ];

    /**
     * The Eloquent roles model name.
     *
     * @var string
     */
    protected static $rolesModel = EloquentRole::class;

    /**
     * The Eloquent activations model name.
     *
     * @var string
     */
    protected static $activationsModel = EloquentActivation::class;


    /**
     * Always capitalize the first name when we retrieve it
     * @param string $value
     * @return string
     * @author Sang Nguyen
     */
    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Always capitalize the last name when we retrieve it
     * @param string $value
     * @return string
     * @author Sang Nguyen
     */
    public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * @return string
     * @author Sang Nguyen
     */
    public function getFullName()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    /**
     * @return string
     * @author Sang Nguyen
     */
    public function getProfileImage()
    {
        if (empty($this->profile_image)) {
            return config('core.acl.general.avatar.default');
        } else {
            return $this->profile_image;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author Sang Nguyen
     */
    public function getRole()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id');
    }

    /**
     * @return boolean
     * @author Sang Nguyen
     */
    public function isSuperUser()
    {
        /**
         * @var PermissionsTrait $this
         */
        return $this->super_user || $this->hasAccess('superuser');
    }

    /**
     * @param string $permission
     * @return boolean
     * @author Sang Nguyen
     */
    public function hasPermission($permission)
    {
        if ($this->isSuperUser()) {
            return true;
        }
        /**
         * @var PermissionsTrait $this
         */
        return $this->hasAccess($permission);
    }

    /**
     * @param array $permissions
     * @return bool
     * @author Sang Nguyen
     */
    public function hasAnyPermission(array $permissions)
    {
        if ($this->isSuperUser()) {
            return true;
        }
        /**
         * @var PermissionsTrait $this
         */
        return $this->hasAnyAccess($permissions);
    }

    /**
     * @return array
     */
    public function authorAttributes()
    {
        return [
            'name' => $this->getFullName(),
            'email' => $this->email,
            'url' => $this->website,    // optional
            'avatar' => 'gravatar', // optional
        ];
    }

    /**
     * @param string $date
     * @author Sang Nguyen
     */
    public function setDobAttribute($date)
    {
        if (!empty($date)) {
            $this->attributes['dob'] = Carbon::createFromFormat(config('core.base.general.date_format.date'), $date)->toDateTimeString();
        } else {
            $this->attributes['dob'] = $date;
        }
    }

    /**
     * @param $date
     * @author Sang Nguyen
     * @return string
     */
    public function getDobAttribute($date)
    {
        return date_from_database($date, config('core.base.general.date_format.date'));
    }

    /**
     * @param string $value
     * @return array
     */
    public function getPermissionsAttribute($value)
    {
        try {
            return json_decode($value, true) ?: [];
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * Send the password reset notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Returns the roles relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(static::$rolesModel, 'role_users', 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * Returns the activations relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activations()
    {
        return $this->hasMany(static::$activationsModel, 'user_id');
    }

    /**
     * Set mutator for the "permissions" attribute.
     *
     * @param array $permissions
     * @return void
     */
    public function setPermissionsAttribute(array $permissions)
    {
        $this->attributes['permissions'] = $permissions ? json_encode($permissions) : '';
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritDoc}
     */
    public function inRole($role)
    {
        $roleId = null;
        if ($role instanceof RoleInterface) {
            $roleId = $role->getRoleId();
        }

        foreach ($this->roles as $instance) {
            /**
             * @var Role $instance
             */
            if ($role instanceof RoleInterface) {
                if ($instance->getRoleId() === $roleId) {
                    return true;
                }
            } else {
                if ($instance->getRoleId() == $role || $instance->getRoleSlug() == $role) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the roles model.
     *
     * @return string
     */
    public static function getRolesModel()
    {
        return static::$rolesModel;
    }

    /**
     * Sets the roles model.
     *
     * @param  string $rolesModel
     * @return void
     */
    public static function setRolesModel($rolesModel)
    {
        static::$rolesModel = $rolesModel;
    }

    /**
     * Returns the activations model.
     *
     * @return string
     */
    public static function getActivationsModel()
    {
        return static::$activationsModel;
    }

    /**
     * Sets the activations model.
     *
     * @param  string $activationsModel
     * @return void
     */
    public static function setActivationsModel($activationsModel)
    {
        static::$activationsModel = $activationsModel;
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        if ($this->exists) {
            $this->activations()->delete();
            $this->roles()->detach();
        }

        return parent::delete();
    }

    /**
     * Dynamically pass missing methods to the user.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $methods = ['hasAccess', 'hasAnyAccess'];

        if (in_array($method, $methods)) {
            $permissions = $this->getPermissionsInstance();

            return call_user_func_array([$permissions, $method], $parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Creates a permissions object.
     *
     * @return \Botble\ACL\Permissions\PermissionsInterface
     */
    protected function createPermissions()
    {
        $userPermissions = $this->permissions;

        $rolePermissions = [];

        foreach ($this->roles as $role) {
            $rolePermissions[] = $role->permissions;
        }

        return new static::$permissionsClass($userPermissions, $rolePermissions);
    }

    /**
     * @param $username
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function findForPassport($username)
    {
        return self::where('username', $username)->first();
    }
}
