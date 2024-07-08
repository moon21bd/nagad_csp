<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// use Laravel\Passport\HasApiTokens;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

// class User extends Authenticatable implements MustVerifyEmail
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;
    protected $guard_name = 'api';
    // protected $appends = ['must_verify_email'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Determine if the user is an administrator.
     *
     * @return bool
     */
    public function getMustVerifyEmailAttribute()
    {
        return config('auth.must_verify_email');
    }

    /*public function groups()
    {
        return $this->belongsToMany(NCGroup::class, 'nc_user_groups', 'user_id', 'group_id');
    }

    public function hasPermissionForPath($path)
    {
        return $this->groups->pluck('permissions')->flatten()->contains(function ($permission) use ($path) {
            return $permission->path && str_is($permission->path, $path);
        });
    }*/
}
