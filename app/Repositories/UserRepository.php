<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
  public function getAllUsers()
  {
    $users = User::with('userPermission.permission')->get();

    // dd($users);

    return $users;
  }

  public function getUserPorId($userId)
  {
    return User::with('userPermission.permission')->find($userId);
  }

  public function createUser(User $userData): User
  {
    $userData->save();

    return $userData;
  }
  public function updateUser($userId, object $userRequest): bool
  {
    $user = User::findOrFail($userId);

    $user->name = $userRequest->input('name');
    $user->email = $userRequest->input('email');
    if (!empty($userRequest->input('password'))) {
      $user->password = $userRequest->input('password');
    }

    return $user->save();
  }

  public function setActive(User $user)
  {
    return $user->update(['active' => true]);
  }

  public function setInactive(User $user)
  {
    return $user->update(['active' => false]);
  }
}
