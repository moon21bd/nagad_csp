<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Sanctum\HasApiTokens;
use Ramsey\Uuid\Uuid as Generator;

// class User extends Authenticatable implements MustVerifyEmail
class User extends Authenticatable
{

    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, LaratrustUserTrait;

    // It also adds the following methods roles(), hasRole($name), hasPermission($permission), isAbleTo($permission), ability($roles, $permissions, $options), and rolesTeams() to the model.

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id',
        'parent_id',
        'mobile_no',
        'name',
        'user_type',
        'api_token',
        'email',
        'created_by',
        'updated_by',
        'status',
        'avatar',
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

    // Assuming 'avatar' is the column name in your users table that stores the file path of the avatar
    protected $appends = ['avatar_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->uuid = str_replace('-', '', Generator::uuid4()->toString());
            } catch (\Exception $e) {
                abort(500);
            }
        });
    }

    /**
     * Determine if the user is an administrator.
     *
     * @return bool
     */
    public function getMustVerifyEmailAttribute()
    {
        return config('auth.must_verify_email');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function user_activity()
    {
        return $this->belongsTo(UserActivity::class, 'id', 'user_id');
    }

    public function user_login_activity()
    {
        return $this->hasMany(UserLoginActivity::class, 'user_id', 'id');
    }

    public function user_details()
    {
        return $this->belongsTo(UserDetail::class, 'id', 'user_id');
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('uploads/' . $this->avatar) : null;
    }
}
