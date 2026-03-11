<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\services\Services;

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

  public function tenants()
  {
    return $this->belongsToMany(Tenant::class)
      ->withPivot(['role', 'is_active'])
      ->withTimestamps();
  }
}
