<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
  /**
   * Aceita um ou mais papéis: permission:admin  ou  permission:admin,employee.
   * Libera se o usuário tiver QUALQUER um deles na empresa ativa.
   */
  public function handle(Request $request, Closure $next, string ...$permissions)
  {
    $user = auth()->user();

    $allowed = $user && collect($permissions)->contains(
      fn (string $permission) => $user->hasPermission($permission)
    );

    if (!$allowed) {
      abort(403, 'Sem permissão');
    }

    return $next($request);
  }
}
