<?php

namespace App\Interfaces;

interface PermissionRepositoryInterface
{
  public function getAllPermissions();
  public function updatePermission($userId, int $permissionCode);
}
