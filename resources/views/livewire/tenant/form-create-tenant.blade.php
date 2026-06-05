<div>

{{-- Cabeçalho --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1">{{ $editId ? 'Editar Empresa' : 'Cadastro de Empresa' }}</h4>
        <p class="text-muted mb-0">Gerencie as empresas (tenants) cadastradas no sistema</p>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Formulário --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-3 pb-3">
        <div class="d-flex align-items-center justify-content-center text-white"
             style="width:38px;height:38px;border-radius:8px;background:#696cff;">
            <i class="mdi mdi-{{ $editId ? 'pencil-outline' : 'domain-plus' }}"></i>
        </div>
        <h5 class="card-title mb-0">{{ $editId ? 'Editar dados da empresa' : 'Nova empresa' }}</h5>
    </div>

    <div class="card-body">
        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label fw-semibold">Nome da empresa <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="mdi mdi-domain text-muted"></i></span>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           wire:model.live="name"
                           placeholder="Ex: Clínica Vida">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="mdi mdi-link-variant text-muted"></i></span>
                    <input type="text"
                           class="form-control @error('slug') is-invalid @enderror"
                           wire:model.live="slug"
                           placeholder="Ex: clinica-vida">
                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <small class="text-muted">Identificador único, sem espaços</small>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="mdi mdi-email-outline text-muted"></i></span>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           wire:model.live="email"
                           placeholder="contato@empresa.com">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Telefone</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="mdi mdi-phone-outline text-muted"></i></span>
                    <input type="text"
                           class="form-control @error('phone') is-invalid @enderror"
                           wire:model.live="phone"
                           placeholder="(00) 00000-0000">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" wire:model.live="is_active">
                    <label class="form-check-label fw-semibold" for="is_active">Empresa ativa</label>
                </div>
            </div>

        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="button" class="btn btn-primary px-4" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
                <i class="mdi mdi-content-save-outline me-1" wire:loading.remove wire:target="save"></i>
                {{ $editId ? 'Atualizar' : 'Cadastrar' }}
            </button>
            @if($editId)
                <button type="button" class="btn btn-outline-secondary px-4" wire:click="cancelEdit">
                    <i class="mdi mdi-close me-1"></i> Cancelar
                </button>
            @endif
        </div>
    </div>
</div>

{{-- Lista --}}
<div class="card">
    <div class="card-header d-flex align-items-center gap-3 pb-3">
        <div class="d-flex align-items-center justify-content-center bg-secondary text-white"
             style="width:38px;height:38px;border-radius:8px;">
            <i class="mdi mdi-format-list-bulleted"></i>
        </div>
        <div>
            <h5 class="card-title mb-0">Empresas cadastradas</h5>
            <small class="text-muted">{{ $tenants->count() }} {{ $tenants->count() === 1 ? 'empresa' : 'empresas' }}</small>
        </div>
    </div>

    <div class="card-body {{ $tenants->isEmpty() ? '' : 'p-0' }}">
        @if($tenants->isEmpty())
            <div class="text-center py-5">
                <i class="mdi mdi-domain-off text-muted" style="font-size:3rem;opacity:.4"></i>
                <p class="text-muted mt-2 mb-0">Nenhuma empresa cadastrada ainda.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Empresa</th>
                            <th>Slug</th>
                            <th>Contato</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $tenant)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center text-white fw-bold"
                                             style="width:36px;height:36px;border-radius:8px;background:#696cff;font-size:.8rem;flex-shrink:0;">
                                            {{ strtoupper(substr($tenant->name, 0, 2)) }}
                                        </div>
                                        <span class="fw-semibold">{{ $tenant->name }}</span>
                                    </div>
                                </td>
                                <td><code class="text-muted small">{{ $tenant->slug }}</code></td>
                                <td class="text-muted small">
                                    @if($tenant->email)
                                        <div><i class="mdi mdi-email-outline me-1"></i>{{ $tenant->email }}</div>
                                    @endif
                                    @if($tenant->phone)
                                        <div><i class="mdi mdi-phone-outline me-1"></i>{{ $tenant->phone }}</div>
                                    @endif
                                    @if(!$tenant->email && !$tenant->phone)
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tenant->is_active)
                                        <span class="badge bg-success bg-opacity-15 text-success fw-semibold">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-15 text-secondary fw-semibold">Inativo</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if($confirmingDeleteId === $tenant->id)
                                        <span class="me-1 small text-muted">Confirmar exclusão?</span>
                                        <button class="btn btn-danger btn-sm me-1" wire:click="delete({{ $tenant->id }})">Sim</button>
                                        <button class="btn btn-outline-secondary btn-sm" wire:click="cancelDelete">Não</button>
                                    @else
                                        <button class="btn btn-sm btn-outline-primary me-1" wire:click="openEdit({{ $tenant->id }})" title="Editar">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $tenant->id }})" title="Excluir">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

</div>
