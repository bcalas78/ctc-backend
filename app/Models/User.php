<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'email_verified_token',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->email;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function championships()
    {
        return $this->hasMany(Championship::class, 'user_id');
    }

    public function games()
    {
        return $this->hasMany(Game::class, 'user_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'user_id');
    }

    public function teams()
    {
        $role = $this->roles()->first();

        if ($role && $role->name === 'joueur') {
            return $this->belongsToMany(Team::class, 'user_team');
        } else {
            return $this->hasMany(Team::class, 'user_id');
        }
    }

    public function hasPermission(string $permissionName): bool
    {
        $roles = $this->roles()->with('permissions')->get();

        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                if ($permission->name === $permissionName) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        } else {
            return 'avatar';
        }
    }

    public function assignRole($role)
    {
        $existingRole = Role::where('name', $role)->first();

        if ($existingRole) {
            $this->roles()->attach($existingRole->id);
        } else {
            return response()->json(['error' => "Le rôle n'existe pas"], 404);
        }
    }
}
