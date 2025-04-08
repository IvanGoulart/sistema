<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\PermissionRepositoryInterface;
use App\Models\User;

class UserController extends Controller
{
  private UserRepositoryInterface $userRepository;
  private PermissionRepositoryInterface $permissionRepository;

  public function __construct(
    UserRepositoryInterface $userRepository,
    PermissionRepositoryInterface $permissionRepository
  ) {
    $this->userRepository = $userRepository;
    $this->permissionRepository = $permissionRepository;
  }

  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('content.user.users-list', ['users' => $this->userRepository->getAllUsers()]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    // Validação dos dados
    $validatedData = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8',
      'permission' => 'required',
    ]);

    $userData = new User($validatedData);

    $this->userRepository->createUser($userData);
    // Redirecionar para alguma rota após salvar
    return back()->with('success', 'Usuário criado com sucesso!');
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    $user = $this->userRepository->getUserPorId($id);

    $permissions = $this->permissionRepository->getAllPermissions();

    $selectedPermissionId = $user->userPermission->code_permission; // Supondo que o usuário tenha uma permissão atribuída

    return view('content.authentications.auth-register-basic', [
      'user' => $user,
      'permissions' => $permissions,
      'selectedPermissionId' => $selectedPermissionId,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, int $userId)
  {
    $this->userRepository->updateUser($userId, $request);
    $this->permissionRepository->updatePermission($userId, $request->permission);
    return back()->with('success', 'Usuário alterado com sucesso.');
  }

  /**
   * Active the specified resource in storage.
   */
  public function active(int $userId)
  {
    $user = $this->userRepository->getUserPorId($userId);

    if (!$user) {
      return back()->with('error', 'Usuário não encontrado.');
    }

    $this->userRepository->setActive($user);

    return redirect()
      ->route('users-list')
      ->with('success', 'Usuário ativado com sucesso.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    $user = $this->userRepository->getUserPorId($id);

    if (!$user) {
      return back()->with('error', 'Usuário não encontrado.');
    }

    $this->userRepository->setInactive($user);

    return redirect()
      ->route('users-list')
      ->with('success', 'Usuário inativado com sucesso.');
  }
}
