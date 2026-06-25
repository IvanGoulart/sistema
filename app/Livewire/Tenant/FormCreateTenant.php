<?php

namespace App\Livewire\Tenant;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class FormCreateTenant extends Component
{
    public string $name  = '';
    public string $slug  = '';
    public ?string $email = null;
    public ?string $phone = null;
    public bool $is_active = true;

    // Onboarding: dados do primeiro admin da empresa (somente na criação).
    public string $admin_name     = '';
    public string $admin_email    = '';
    public string $admin_password = '';

    public ?int $editId            = null;
    public ?int $confirmingDeleteId = null;

    /**
     * Área exclusiva do dono da plataforma. As ações Livewire chegam por
     * /livewire/update (fora do middleware 'platform' das rotas), então a
     * autorização precisa ser garantida aqui também.
     */
    public function mount(): void
    {
        $this->authorizePlatform();
    }

    private function authorizePlatform(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403, 'Área restrita ao administrador da plataforma.');
    }

    public function updatedName($value): void
    {
        if ($this->editId === null && blank($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->authorizePlatform();

        $rules = [
            'name'      => ['required', 'string', 'max:255'],
            'slug'      => ['required', 'string', 'max:255', $this->editId ? "unique:tenants,slug,{$this->editId}" : 'unique:tenants,slug'],
            'email'     => ['nullable', 'email', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
        ];

        // Na criação, exigimos o primeiro admin da empresa (onboarding completo).
        if (! $this->editId) {
            $rules['admin_name']     = ['required', 'string', 'max:255'];
            $rules['admin_email']    = ['required', 'email', 'max:255', 'unique:users,email'];
            $rules['admin_password'] = ['required', 'string', 'min:8'];
        }

        $data = $this->validate($rules, [
            'name.required'           => 'O nome da empresa é obrigatório.',
            'slug.required'           => 'O slug é obrigatório.',
            'slug.unique'             => 'Este slug já está em uso.',
            'email.email'             => 'Informe um e-mail válido.',
            'admin_name.required'     => 'O nome do administrador é obrigatório.',
            'admin_email.required'    => 'O e-mail do administrador é obrigatório.',
            'admin_email.email'       => 'Informe um e-mail válido para o administrador.',
            'admin_email.unique'      => 'Já existe um usuário com este e-mail.',
            'admin_password.required' => 'A senha do administrador é obrigatória.',
            'admin_password.min'      => 'A senha deve ter ao menos 8 caracteres.',
        ]);

        if ($this->editId) {
            Tenant::findOrFail($this->editId)->update([
                'name'      => $data['name'],
                'slug'      => $data['slug'],
                'email'     => $data['email'],
                'phone'     => $data['phone'],
                'is_active' => $data['is_active'],
            ]);
            session()->flash('success', 'Empresa atualizada com sucesso.');
            $this->resetForm();

            return;
        }

        // Criação: empresa + primeiro admin, atômico.
        DB::transaction(function () use ($data) {
            $tenant = Tenant::create([
                'name'      => $data['name'],
                'slug'      => $data['slug'],
                'email'     => $data['email'],
                'phone'     => $data['phone'],
                'is_active' => $data['is_active'],
            ]);

            $admin = User::create([
                'name'     => $data['admin_name'],
                'email'    => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'active'   => true,
            ]);

            $adminPermissionId = DB::table('permissions')->where('name', 'admin')->value('id');

            // Papel 'admin' DESTA empresa (sistema de permissões por tenant).
            DB::table('user_permissions')->insertOrIgnore([
                'tenant_id'       => $tenant->id,
                'user_id'         => $admin->id,
                'code_permission' => $adminPermissionId,
            ]);

            // Vínculo empresa ↔ usuário.
            $tenant->users()->syncWithoutDetaching([
                $admin->id => ['role' => 'admin', 'is_active' => true],
            ]);
        });

        session()->flash('success', 'Empresa e administrador cadastrados com sucesso.');
        $this->resetForm();
    }

    public function openEdit(int $id): void
    {
        $this->authorizePlatform();

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
        $this->authorizePlatform();
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id): void
    {
        $this->authorizePlatform();

        Tenant::findOrFail($id)->delete();
        $this->confirmingDeleteId = null;
        session()->flash('success', 'Empresa removida com sucesso.');
    }

    private function resetForm(): void
    {
        $this->reset([
            'name', 'slug', 'email', 'phone',
            'admin_name', 'admin_email', 'admin_password',
            'editId', 'confirmingDeleteId',
        ]);
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.tenant.form-create-tenant', [
            'tenants' => Tenant::latest()->get(),
        ]);
    }
}
