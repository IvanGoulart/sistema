<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    if (!Auth::attempt($credentials, true)) {
      return back()->withErrors(['email' => 'E-mail ou senha inválidos.'])->onlyInput('email');
    }

    $request->session()->regenerate();

    // Só deixa entrar no portal se for cliente
    if (!auth()->user()->hasPermission('client')) {
      Auth::logout();
      return back()->withErrors(['email' => 'Acesso permitido apenas para clientes.'])->onlyInput('email');
    }

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
