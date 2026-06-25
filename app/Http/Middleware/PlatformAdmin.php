<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe o acesso ao dono da plataforma (super-admin).
 *
 * Diferente de CheckPermission, NÃO depende da empresa ativa: usa a flag global
 * users.is_super_admin. Protege o cadastro/gestão de empresas (tenants), que é
 * uma área exclusiva do dono do SaaS.
 */
class PlatformAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'Área restrita ao administrador da plataforma.');
        }

        return $next($request);
    }
}
