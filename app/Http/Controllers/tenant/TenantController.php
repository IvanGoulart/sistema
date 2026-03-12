<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends Controller
{
  public function index()
  {
    $tenants = Tenant::all();

    return view('tenant.index', compact('tenants'));
  }

  public function create()
  {
    return view('content.tenant.tenant-create');

  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'name' => 'required|string|max:255',
    ]);

    $tenant = Tenant::create([
      'name' => $data['name'],
      'slug' => Str::slug($data['name']),
      'is_active' => true
    ]);

    return redirect()->route('tenant.index')
      ->with('success', 'Tenant criado com sucesso.');
  }

  public function show($id)
  {
    $tenant = Tenant::findOrFail($id);

    return view('tenant.show', compact('tenant'));
  }

  public function edit($id)
  {
    $tenant = Tenant::findOrFail($id);

    return view('tenant.edit', compact('tenant'));
  }

  public function update(Request $request, $id)
  {
    $tenant = Tenant::findOrFail($id);

    $data = $request->validate([
      'name' => 'required|string|max:255',
    ]);

    $tenant->update([
      'name' => $data['name'],
      'slug' => Str::slug($data['name'])
    ]);

    return redirect()->route('tenant.index')
      ->with('success', 'Tenant atualizado.');
  }

  public function destroy($id)
  {
    $tenant = Tenant::findOrFail($id);
    $tenant->delete();

    return redirect()->route('tenant.index')
      ->with('success', 'Tenant removido.');
  }
}
