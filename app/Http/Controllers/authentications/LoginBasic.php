<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginBasic extends Controller
{
    public function index()
    {
        return view('content.authentications.auth-login-basic');
    }

    public function checkAuthenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Painel administrativo: apenas quem é admin ou profissional (employee)
            // em ALGUMA empresa. Clientes devem usar o portal do salão.
            $tenantIds = Auth::user()->tenantIdsWithAnyRole(['admin', 'employee']);

            if (empty($tenantIds)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['email' => 'Acesso restrito a administradores e profissionais. Clientes devem usar o portal.']);
            }

            // Empresa ativa = primeira em que tem acesso (switcher virá na Fase 3).
            session(['tenant_id' => $tenantIds[0]]);

            return redirect()->intended('/dashboard');
        }

        // Autenticação falhou
        return back()->withErrors(['email' => 'E-mail ou senha inválidos']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin');
    }
}
