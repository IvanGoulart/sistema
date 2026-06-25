<div>

    {{-- Flash --}}
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Cabeçalho da página --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Serviços</h4>
            <p class="text-muted mb-0">Gerencie os serviços oferecidos e os profissionais vinculados</p>
        </div>
        @if($canManage && !$showForm)
            <button class="btn btn-primary" wire:click="openCreate">
                <i class="mdi mdi-plus me-1"></i> Novo Serviço
            </button>
        @endif
    </div>

    {{-- Formulário criar / editar --}}
    @if($showForm)
        <div class="card mb-4 border border-primary">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="mdi mdi-{{ $editingId ? 'pencil-outline' : 'plus-circle-outline' }} text-primary fs-5"></i>
                <h6 class="mb-0">{{ $editingId ? 'Editar Serviço' : 'Novo Serviço' }}</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">
                            Nome do Serviço <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               wire:model="name"
                               placeholder="Ex: Corte de Cabelo"
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Descrição <span class="text-muted small">(opcional)</span></label>
                        <input type="text"
                               class="form-control"
                               wire:model="description"
                               placeholder="Breve descrição do serviço">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Valor <span class="text-muted small">(opcional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number"
                                   class="form-control @error('price') is-invalid @enderror"
                                   wire:model="price"
                                   placeholder="0,00"
                                   step="0.01"
                                   min="0">
                        </div>
                        @error('price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary"
                            wire:click="save"
                            wire:loading.attr="disabled"
                            wire:target="save">
                        <span wire:loading.remove wire:target="save">
                            <i class="mdi mdi-content-save me-1"></i>
                            {{ $editingId ? 'Salvar Alterações' : 'Criar Serviço' }}
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-1"></span> Salvando...
                        </span>
                    </button>
                    <button class="btn btn-outline-secondary" wire:click="cancelForm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabela de serviços --}}
    <div class="card">
        <div class="card-header d-flex align-items-center pb-3">
            <div class="avatar avatar-sm me-3 bg-label-warning rounded">
                <i class="mdi mdi-briefcase-outline mdi-24px"></i>
            </div>
            <div>
                <h5 class="card-title mb-0">Lista de Serviços</h5>
                @if($services && $services->count() > 0)
                    <small class="text-muted">{{ $services->count() }} {{ $services->count() === 1 ? 'serviço cadastrado' : 'serviços cadastrados' }}</small>
                @endif
            </div>
        </div>

        <div class="card-body p-0">

            @if(!$services || $services->isEmpty())
                <div class="text-center py-5">
                    <i class="mdi mdi-briefcase-remove-outline text-muted" style="font-size: 3.5rem; opacity: .4;"></i>
                    <p class="text-muted mt-2 mb-1">Nenhum serviço cadastrado.</p>
                    <small class="text-muted">Clique em "Novo Serviço" para começar.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nome</th>
                                <th>Descrição</th>
                                <th class="text-center">Valor</th>
                                <th class="text-center">Funcionários</th>
                                <th class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                <tr wire:key="service-{{ $service->id }}">
                                    <td class="ps-4 fw-semibold">{{ $service->name }}</td>
                                    <td class="text-muted">{{ $service->description ?: '—' }}</td>
                                    <td class="text-center">
                                        @if($service->price)
                                            <span class="badge bg-label-success">R$ {{ number_format($service->price, 2, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-label-{{ $service->employee_count > 0 ? 'success' : 'secondary' }}">
                                            {{ $service->employee_count }}
                                            {{ $service->employee_count === 1 ? 'profissional' : 'profissionais' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        @unless($canManage)
                                            <span class="text-muted">—</span>
                                        @else
                                        @if($confirmingDeleteId === $service->id)
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <small class="text-danger fw-semibold">Excluir serviço?</small>
                                                <button class="btn btn-danger btn-sm"
                                                        wire:click="delete({{ $service->id }})">
                                                    <i class="mdi mdi-check me-1"></i>Sim
                                                </button>
                                                <button class="btn btn-outline-secondary btn-sm"
                                                        wire:click="dismissDelete">
                                                    Não
                                                </button>
                                            </div>
                                        @else
                                            <div class="d-flex gap-1 justify-content-end">
                                                <button class="btn btn-icon btn-sm btn-outline-primary"
                                                        wire:click="openEdit({{ $service->id }})"
                                                        title="Editar">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </button>
                                                <button class="btn btn-sm {{ $managingServiceId === $service->id ? 'btn-info' : 'btn-outline-info' }}"
                                                        wire:click="openEmployees({{ $service->id }})"
                                                        title="Gerenciar Profissionais">
                                                    <i class="mdi mdi-account-group-outline me-1"></i>
                                                    Profissionais
                                                </button>
                                                <button class="btn btn-icon btn-sm btn-outline-danger"
                                                        wire:click="confirmDelete({{ $service->id }})"
                                                        title="Excluir">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </div>
                                        @endif
                                        @endunless
                                    </td>
                                </tr>

                                {{-- Painel de gerenciamento de profissionais --}}
                                @if($managingServiceId === $service->id)
                                    <tr wire:key="employees-panel-{{ $service->id }}">
                                        <td colspan="4" class="p-0">
                                            <div class="bg-light border-top border-bottom px-4 py-4">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <h6 class="mb-0 d-flex align-items-center gap-2">
                                                        <i class="mdi mdi-account-group-outline text-info fs-5"></i>
                                                        Profissionais vinculados a
                                                        <strong>{{ $service->name }}</strong>
                                                    </h6>
                                                    <button class="btn btn-sm btn-outline-secondary"
                                                            wire:click="openEmployees({{ $service->id }})">
                                                        <i class="mdi mdi-close"></i>
                                                    </button>
                                                </div>

                                                @if($availableEmployees->isEmpty())
                                                    <div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-0">
                                                        <i class="mdi mdi-alert-outline fs-5"></i>
                                                        <span>
                                                            Nenhum usuário encontrado neste tenant.
                                                            Cadastre usuários primeiro em <strong>Novo Usuário</strong>.
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="row g-2 mb-2">
                                                        @foreach($availableEmployees as $employee)
                                                            @php $linked = in_array((int) $employee->id, $linkedEmployeeIds); @endphp
                                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                                <div wire:click="toggleEmployee({{ $employee->id }})"
                                                                     class="d-flex align-items-center gap-3 p-3 rounded border bg-white
                                                                            {{ $linked ? 'border-success' : 'border-light' }}"
                                                                     style="cursor: pointer; transition: border-color .15s;">
                                                                    <div class="avatar avatar-sm rounded-circle flex-shrink-0
                                                                                bg-label-{{ $linked ? 'success' : 'secondary' }}">
                                                                        <i class="mdi mdi-account-outline"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1 overflow-hidden">
                                                                        <p class="mb-0 fw-semibold text-truncate">{{ $employee->name }}</p>
                                                                        <small class="text-muted text-truncate d-block">{{ $employee->email }}</small>
                                                                    </div>
                                                                    <i class="mdi mdi-{{ $linked ? 'check-circle text-success' : 'circle-outline text-muted' }} fs-5 flex-shrink-0"></i>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <small class="text-muted d-flex align-items-center gap-1">
                                                        <i class="mdi mdi-information-outline"></i>
                                                        Clique em um profissional para vincular ou desvincular do serviço.
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
