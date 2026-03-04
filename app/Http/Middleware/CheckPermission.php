<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
  public function handle(Request $request, Closure $next, $permission)
  {
    $user = auth()->user();

    if (!$user || !$user->hasPermission($permission)) {
      abort(403, 'Sem permissão');
    }

    return $next($request);
  }
}
