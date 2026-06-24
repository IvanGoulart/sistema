<?php

namespace App\Models;

use App\Models\services\Services;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function services()
    {
        return $this->belongsToMany(
            Services::class,
            'user_services',
            'user_id',
            'service_id'
        );
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'user_permissions',
            'user_id',
            'code_permission'
        );
    }

    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()
            ->where('name', $permissionName)
            ->exists();
    }

    /**
     * Papel canônico do usuário: 'admin' | 'employee' | 'client' | null.
     * Fonte da verdade: user_permissions.code_permission (papéis exclusivos).
     */
    public function role(): ?string
    {
        return $this->permissions->first()?->name;
    }

    public function isAdmin(): bool
    {
        return $this->hasPermission('admin');
    }

    public function isProfessional(): bool
    {
        return $this->hasPermission('employee');
    }

    public function isClient(): bool
    {
        return $this->hasPermission('client');
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class)
            ->withPivot(['role', 'is_active'])
            ->withTimestamps();
    }
}
