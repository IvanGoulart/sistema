<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolve a empresa (tenant) ativa a partir do slug na URL: /portal/{tenantSlug}/...
 *
 * - 404 se o slug não existir ou a empresa estiver inativa.
 * - Guarda o tenant na sessão (tenant_id) e no container (instância 'tenant').
 * - Define o slug como parâmetro default de rota, para que todos os route('portal.*')
 *   existentes continuem funcionando sem passar o slug explicitamente.
 */
class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('tenantSlug');

        $tenant = Tenant::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $tenant) {
            abort(404, 'Empresa não encontrada.');
        }

        session(['tenant_id' => $tenant->id]);
        app()->instance('tenant', $tenant);

        // Mantém route('portal.*') funcionando sem precisar passar o slug.
        URL::defaults(['tenantSlug' => $tenant->slug]);

        return $next($request);
    }
}
