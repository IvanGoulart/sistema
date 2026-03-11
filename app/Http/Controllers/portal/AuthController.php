<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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

    if (!Auth::attempt($credentials, true)) {
      return back()->withErrors(['email' => 'E-mail ou senha inválidos.'])->onlyInput('email');
    }

    $request->session()->regenerate();

    if (!auth()->user()->hasPermission('client')) {
      Auth::logout();
      return back()->withErrors(['email' => 'Acesso permitido apenas para clientes.'])->onlyInput('email');
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

    $user = User::create([
      'name' => $data['name'],
      'email' => $data['email'],
      'password' => Hash::make($data['password']),
      'active' => 1,
    ]);

    $permission = Permission::where('name', 'client')->first();

    if ($permission) {
      UserPermission::create([
        'user_id' => $user->id,
        'code_permission' => $permission->id,
      ]);
    }

    Auth::login($user);

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
