<?php

namespace App\Repositories;

use App\Interfaces\PermissionRepositoryInterface;
use App\Models\Permission;
use App\Models\UserPermission;

class PermissionRepository implements PermissionRepositoryInterface
{
  public function getAllPermissions()
  {
    $permissions = Permission::get();

    return $permissions;
  }

  public function updatePermission($userId, int $permissionCode)
  {
    UserPermission::updateOrCreate(
      [
        'user_id' => $userId,
      ],
      [
        'code_permission' => $permissionCode,
      ]
    );
  }

  public function createPermission(int $userId, int $permissionCode): bool
  {
    $userPermission = new UserPermission();
    $userPermission->user_id = $userId;
    $userPermission->code_permission = $permissionCode;

    return $userPermission->save();
  }
}
