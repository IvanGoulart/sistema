<?php

namespace App\Livewire\Services;

use App\Models\services\Services;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ServiceManager extends Component
{
    public $services;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $description = '';

    public ?float $price = null;

    public ?int $confirmingDeleteId = null;

    public ?int $managingServiceId = null;

    public $availableEmployees = [];

    public array $linkedEmployeeIds = [];

    // Só admin gerencia serviços; profissional vê em modo somente-leitura.
    public bool $canManage = false;

    public function mount(): void
    {
        $this->canManage = (bool) auth()->user()?->isAdmin();
        $this->loadServices();
    }

    private function tenantId(): int
    {
        return session('tenant_id') ?? 1;
    }

    /**
     * Bloqueia ações de escrita para quem não é admin (ex.: profissional).
     * Necessário pois ações Livewire chegam por /livewire/update, fora do
     * middleware de rota.
     */
    private function authorizeManage(): void
    {
        abort_unless($this->canManage, 403);
    }

    public function loadServices(): void
    {
        $tenantId = $this->tenantId();

        $this->services = Services::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get()
            ->map(function ($service) use ($tenantId) {
                $service->employee_count = DB::table('user_services')
                    ->join('user_permissions', 'user_permissions.user_id', '=', 'user_services.user_id')
                    ->join('permissions', 'permissions.id', '=', 'user_permissions.code_permission')
                    ->where('user_services.tenant_id', $tenantId)
                    ->where('user_services.service_id', $service->id)
                    ->where('permissions.name', 'employee')
                    ->count();

                return $service;
            });
    }

    public function openCreate(): void
    {
        $this->authorizeManage();
        $this->editingId = null;
        $this->name = '';
        $this->description = '';
        $this->price = null;
        $this->managingServiceId = null;
        $this->showForm = true;
        $this->resetValidation();
    }

    public function openEdit(int $id): void
    {
        $this->authorizeManage();
        $service = Services::where('tenant_id', $this->tenantId())->findOrFail($id);
        $this->editingId = $id;
        $this->name = $service->name;
        $this->description = $service->description ?? '';
        $this->price = $service->price;
        $this->managingServiceId = null;
        $this->showForm = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->authorizeManage();
        $this->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
        ], [
            'name.required' => 'O nome do serviço é obrigatório.',
            'name.max' => 'O nome deve ter no máximo 100 caracteres.',
            'price.numeric' => 'O preço deve ser um número válido.',
        ]);

        if ($this->editingId) {
            Services::where('id', $this->editingId)
                ->where('tenant_id', $this->tenantId())
                ->update([
                    'name' => trim($this->name),
                    'description' => trim($this->description) ?: null,
                    'price' => $this->price,
                ]);
            session()->flash('message', 'Serviço atualizado com sucesso!');
        } else {
            Services::create([
                'tenant_id' => $this->tenantId(),
                'name' => trim($this->name),
                'description' => trim($this->description) ?: null,
                'price' => $this->price,
            ]);
            session()->flash('message', 'Serviço criado com sucesso!');
        }

        $this->cancelForm();
        $this->loadServices();
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->name = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorizeManage();
        $this->confirmingDeleteId = $id;
    }

    public function dismissDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id): void
    {
        $this->authorizeManage();
        Services::where('id', $id)
            ->where('tenant_id', $this->tenantId())
            ->delete();

        session()->flash('message', 'Serviço excluído com sucesso.');
        $this->confirmingDeleteId = null;

        if ($this->managingServiceId === $id) {
            $this->managingServiceId = null;
        }

        $this->loadServices();
    }

    public function openEmployees(int $serviceId): void
    {
        $this->authorizeManage();
        if ($this->managingServiceId === $serviceId) {
            $this->managingServiceId = null;

            return;
        }

        $this->managingServiceId = $serviceId;
        $this->showForm = false;

        $this->availableEmployees = DB::table('users')
            ->join('user_permissions', 'users.id', '=', 'user_permissions.user_id')
            ->join('permissions', 'permissions.id', '=', 'user_permissions.code_permission')
            ->where('user_permissions.tenant_id', $this->tenantId())
            ->where('permissions.name', 'employee')
            ->select('users.id', 'users.name', 'users.email')
            ->orderBy('users.name')
            ->get();

        $this->linkedEmployeeIds = DB::table('user_services')
            ->where('service_id', $serviceId)
            ->where('tenant_id', $this->tenantId())
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    public function toggleEmployee(int $userId): void
    {
        $this->authorizeManage();
        $tenantId = $this->tenantId();
        $serviceId = $this->managingServiceId;

        $exists = DB::table('user_services')
            ->where('tenant_id', $tenantId)
            ->where('service_id', $serviceId)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            DB::table('user_services')
                ->where('tenant_id', $tenantId)
                ->where('service_id', $serviceId)
                ->where('user_id', $userId)
                ->delete();
        } else {
            DB::table('user_services')->insert([
                'tenant_id' => $tenantId,
                'service_id' => $serviceId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->linkedEmployeeIds = DB::table('user_services')
            ->where('service_id', $serviceId)
            ->where('tenant_id', $tenantId)
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $this->loadServices();
    }

    public function render()
    {
        return view('livewire.services.service-manager');
    }
}
