<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class TenantController extends Controller
{
    /**
     * Página de gestão de empresas (tenants). O CRUD em si é feito pelo
     * componente Livewire App\Livewire\Tenant\FormCreateTenant embutido na view.
     * Acesso restrito ao super-admin via middleware 'platform' (ver routes/web.php).
     */
    public function create()
    {
        return view('content.tenant.tenant-create');
    }
}
