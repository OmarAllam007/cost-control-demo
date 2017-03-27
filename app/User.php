<?php

namespace App;

use App\Behaviors\HasChangeLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasChangeLog;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    function scopeOptions(Builder $query)
    {
        return $query->orderBy('name')->pluck('name', 'id')->prepend('Select User', '');
    }

    function modules()
    {
        return $this->belongsToMany(Module::class, 'modules_users')->withPivot(['read', 'write', 'delete']);
    }

    function getPermissionsAttribute()
    {
        return $this->modules->keyBy('pivot.module_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (User $user) {
            $user->password = bcrypt($user->password);
        });

        static::updating(function (User $user) {
            $originalPassword = $user->getOriginal('password');
            if ($user->password != $originalPassword) {
                $user->password = bcrypt($user->password);
            }
        });
    }
}
