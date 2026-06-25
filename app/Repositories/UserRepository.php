<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
  /**
   * Empresa (tenant) ativa. Toda gestão de usuários é escopada por ela:
   * "meus usuários" = quem tem papel (user_permissions) nesta empresa.
   */
  private function tenantId(): ?int
  {
    return session('tenant_id');
  }

  /**
   * Usuários que pertencem à empresa ativa, com o papel daquela empresa
   * já carregado (permissions filtradas pelo tenant).
   */
  private function scopedToTenant(): Builder
  {
    $tenantId = $this->tenantId();

    return User::query()
      ->whereHas('permissions', fn ($q) => $q->where('user_permissions.tenant_id', $tenantId))
      ->with(['permissions' => fn ($q) => $q->wherePivot('tenant_id', $tenantId)]);
  }

  public function getAllUsers()
  {
    return $this->scopedToTenant()->get();
  }

  public function getUserPorId($userId)
  {
    // Retorna null se o usuário não pertencer à empresa ativa — impede
    // editar/ativar/inativar usuários de outras empresas via ID.
    return $this->scopedToTenant()->find($userId);
  }

  public function createUser(User $userData): User
  {
    $userData->save();

    return $userData;
  }

  public function updateUser($userId, object $userRequest): bool
  {
    // Defesa em profundidade: só atualiza se o usuário for da empresa ativa.
    $user = $this->getUserPorId($userId);

    if (! $user) {
      return false;
    }

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
