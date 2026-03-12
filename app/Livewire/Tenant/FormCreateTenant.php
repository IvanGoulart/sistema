<?php

namespace App\Livewire\Tenant;

use App\Models\Tenant;
use Illuminate\Support\Str;
use Livewire\Component;

class FormCreateTenant extends Component
{
  public string $name = '';
  public string $slug = '';
  public ?string $email = null;
  public ?string $phone = null;
  public bool $is_active = true;

  public function updatedName($value): void
  {
    if (blank($this->slug)) {
      $this->slug = Str::slug($value);
    }
  }

  public function save(): void
  {
    $data = $this->validate([
      'name' => ['required', 'string', 'max:255'],
      'slug' => ['required', 'string', 'max:255', 'unique:tenants,slug'],
      'email' => ['nullable', 'email', 'max:255'],
      'phone' => ['nullable', 'string', 'max:30'],
      'is_active' => ['boolean'],
    ], [
      'name.required' => 'O nome da empresa é obrigatório.',
      'slug.required' => 'O slug é obrigatório.',
      'slug.unique' => 'Este slug já está em uso.',
      'email.email' => 'Informe um e-mail válido.',
    ]);

    Tenant::create($data);

    session()->flash('success', 'Tenant cadastrado com sucesso.');

    $this->reset([
      'name',
      'slug',
      'email',
      'phone',
    ]);

    $this->is_active = true;
  }

  public function render()
  {
    $tenants = Tenant::latest()->get();

    return view('livewire.tenant.form-create-tenant', [
      'tenants' => $tenants,
    ]);
  }
}
