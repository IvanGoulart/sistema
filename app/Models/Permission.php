<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserPermission;
use App\Models\User;

class Permission extends Model
{
  use HasFactory;

  protected $table = 'permissions';

  protected $fillable = [
    'name',
  ];

  public function userPermissions()
  {
    return $this->hasMany(UserPermission::class, 'code_permission', 'id');
  }

  public function users()
  {
    return $this->belongsToMany(
      User::class,
      'user_permissions',
      'code_permission',
      'user_id',
      'id',
      'id'
    );
  }

  public function permissions()
  {
    return $this->belongsToMany(
      Permission::class,
      'user_permissions',
      'user_id',
      'code_permission',
      'id',
      'id'
    );
  }

  public function hasPermission(string $permissionName): bool
  {
    return $this->permissions()
      ->where('name', $permissionName)
      ->exists();
  }
}
