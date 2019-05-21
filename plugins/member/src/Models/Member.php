<?php

namespace Botble\Member\Models;

use Botble\Member\Notifications\MemberResetPassword;
use Botble\Member\Supports\Gravatar;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use Notifiable;

    /**
     * @var string
     */
    protected $table = 'members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'dob',
        'phone',
        'confirmed_at',
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
     * Send the password reset notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MemberResetPassword($token));
    }

    /**
     * @return string
     */
    public function getAvatarAttribute()
    {
        if (!$this->attributes['avatar']) {
            return (new Gravatar())->image($this->attributes['email']);
        }
        return $this->attributes['avatar'];
    }
}
