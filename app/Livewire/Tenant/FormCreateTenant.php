<?php

namespace App\Livewire\Tenant;

use App\Models\Tenant;
use Illuminate\Support\Str;
use Livewire\Component;

class FormCreateTenant extends Component
{
    public string $name  = '';
    public string $slug  = '';
    public ?string $email = null;
    public ?string $phone = null;
    public bool $is_active = true;

    public ?int $editId            = null;
    public ?int $confirmingDeleteId = null;

    public function updatedName($value): void
    {
        if ($this->editId === null && blank($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $rules = [
            'name'      => ['required', 'string', 'max:255'],
            'slug'      => ['required', 'string', 'max:255', $this->editId ? "unique:tenants,slug,{$this->editId}" : 'unique:tenants,slug'],
            'email'     => ['nullable', 'email', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
        ];

        $data = $this->validate($rules, [
            'name.required' => 'O nome da empresa é obrigatório.',
            'slug.required' => 'O slug é obrigatório.',
            'slug.unique'   => 'Este slug já está em uso.',
            'email.email'   => 'Informe um e-mail válido.',
        ]);

        if ($this->editId) {
            Tenant::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Empresa atualizada com sucesso.');
        } else {
            Tenant::create($data);
            session()->flash('success', 'Empresa cadastrada com sucesso.');
        }

        $this->resetForm();
    }

    public function openEdit(int $id): void
    {
        $tenant          = Tenant::findOrFail($id);
        $this->editId    = $id;
        $this->name      = $tenant->name;
        $this->slug      = $tenant->slug;
        $this->email     = $tenant->email;
        $this->phone     = $tenant->phone;
        $this->is_active = (bool) $tenant->is_active;
        $this->confirmingDeleteId = null;
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id): void
    {
        Tenant::findOrFail($id)->delete();
        $this->confirmingDeleteId = null;
        session()->flash('success', 'Empresa removida com sucesso.');
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'slug', 'email', 'phone', 'editId', 'confirmingDeleteId']);
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.tenant.form-create-tenant', [
            'tenants' => Tenant::latest()->get(),
        ]);
    }
}
