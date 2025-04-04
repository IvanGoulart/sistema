<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
  public function getAllUsers();
  public function getUserPorId($userId);
  public function createUser(User $user);
  public function updateUser($userId, object $request);
  public function setActive(User $user);
  public function setInactive(User $user);
}
