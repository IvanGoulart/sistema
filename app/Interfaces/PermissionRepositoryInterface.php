<?php

namespace App\Interfaces;

use App\Models\Permission;

interface PermissionRepositoryInterface
{
  public function getAllPermissions();
  public function updatePermission($userId, int $permissionCode);
}
