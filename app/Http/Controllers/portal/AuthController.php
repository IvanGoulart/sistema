<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\TenantUser;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('portal.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Empresa ativa resolvida pelo slug da URL (middleware ResolveTenant).
        $tenantId = session('tenant_id');

        if (! Auth::attempt($credentials, true)) {
            return back()->withErrors(['email' => 'E-mail ou senha inválidos.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        session(['tenant_id' => $tenantId]);

        // Só é cliente DESTE salão quem tem o papel client na empresa do slug.
        if (! auth()->user()->isClient($tenantId)) {
            Auth::logout();

            return back()->withErrors(['email' => 'Esta conta não é cliente deste salão.'])->onlyInput('email');
        }

        return redirect()->route('portal.agendar');
    }

    /**
     * Tela de cadastro
     */
    public function showRegister()
    {
        return view('portal.register');
    }

    /**
     * Criar conta
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        // O cadastro pelo portal cria um CLIENTE do salão do slug atual
        // (resolvido pelo middleware ResolveTenant), não uma nova empresa.
        $tenant = app('tenant');

        DB::transaction(function () use ($data, $tenant, &$user) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'active' => 1,
            ]);

            TenantUser::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'role' => 'client',
                'is_active' => true,
            ]);

            $permission = Permission::where('name', 'client')->first();

            if ($permission) {
                UserPermission::create([
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->id,
                    'code_permission' => $permission->id,
                ]);
            }
        });

        Auth::login($user);
        session(['tenant_id' => $tenant->id]);

        return redirect()->route('portal.agendar');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}
