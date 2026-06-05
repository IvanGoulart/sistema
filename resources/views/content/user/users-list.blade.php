@extends('layouts/contentNavbarLayout')

@section('title', 'Lista de Usuários')

@php
$statusMap = [
    0 => ['label' => 'Inativo',  'label_class' => 'bg-label-danger'],
    1 => ['label' => 'Ativo',    'label_class' => 'bg-label-success'],
    2 => ['label' => 'Pendente', 'label_class' => 'bg-label-warning'],
];

$permLabel = [
    'admin'    => ['text' => 'Admin',       'class' => 'bg-label-danger'],
    'employee' => ['text' => 'Funcionário', 'class' => 'bg-label-warning'],
    'client'   => ['text' => 'Cliente',     'class' => 'bg-label-info'],
];

$totalAdmin    = collect($users)->filter(fn($u) => $u->permissions->first()?->name === 'admin')->count();
$totalEmployee = collect($users)->filter(fn($u) => $u->permissions->first()?->name === 'employee')->count();
$totalClient   = collect($users)->filter(fn($u) => $u->permissions->first()?->name === 'client')->count();
@endphp

@section('content')

{{-- Cabeçalho --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1">Usuários</h4>
        <p class="text-muted mb-0">Gerencie os usuários do sistema</p>
    </div>
    <a href="{{ route('auth-register-basic') }}" class="btn btn-primary">
        <i class="mdi mdi-account-plus-outline me-1"></i> Novo Usuário
    </a>
</div>

{{-- Alertas --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Cards de resumo --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-circle text-white"
                     style="width:46px;height:46px;background:#696cff;flex-shrink:0;">
                    <i class="mdi mdi-account-group-outline mdi-24px"></i>
                </div>
                <div>
                    <div class="text-muted small">Total</div>
                    <h4 class="mb-0 fw-bold">{{ count($users) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-circle"
                     style="width:46px;height:46px;flex-shrink:0;background:rgba(255,62,29,.15);color:#ff3e1d;">
                    <i class="mdi mdi-shield-account-outline mdi-24px"></i>
                </div>
                <div>
                    <div class="text-muted small">Admins</div>
                    <h4 class="mb-0 fw-bold">{{ $totalAdmin }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-circle"
                     style="width:46px;height:46px;flex-shrink:0;background:rgba(255,171,0,.15);color:#ffab00;">
                    <i class="mdi mdi-account-tie-outline mdi-24px"></i>
                </div>
                <div>
                    <div class="text-muted small">Funcionários</div>
                    <h4 class="mb-0 fw-bold">{{ $totalEmployee }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-circle"
                     style="width:46px;height:46px;flex-shrink:0;background:rgba(3,195,236,.15);color:#03c3ec;">
                    <i class="mdi mdi-account-outline mdi-24px"></i>
                </div>
                <div>
                    <div class="text-muted small">Clientes</div>
                    <h4 class="mb-0 fw-bold">{{ $totalClient }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabela --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Usuário</th>
                        <th>E-mail</th>
                        <th>Permissão</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $perm    = $user->permissions->first()?->name ?? null;
                            $pl      = $permLabel[$perm] ?? null;
                            $initial = strtoupper(substr($user->name, 0, 1));
                            $status  = $statusMap[$user->active] ?? $statusMap[0];
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex align-items-center justify-content-center text-white fw-bold"
                                         style="width:36px;height:36px;border-radius:50%;background:#696cff;font-size:.85rem;flex-shrink:0;">
                                        {{ $initial }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <small class="text-muted">ID #{{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                @if($pl)
                                    <span class="badge {{ $pl['class'] }} rounded-pill">{{ $pl['text'] }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $status['label_class'] }} rounded-pill">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('user-edit', $user->id) }}"
                                   class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                    <i class="mdi mdi-pencil-outline"></i>
                                </a>
                                @if($user->active == 1)
                                    <a href="{{ route('user-delete', $user->id) }}"
                                       class="btn btn-sm btn-outline-danger" title="Inativar"
                                       onclick="return confirm('Deseja inativar este usuário?')">
                                        <i class="mdi mdi-account-off-outline"></i>
                                    </a>
                                @else
                                    <a href="{{ route('user-active', $user->id) }}"
                                       class="btn btn-sm btn-outline-success" title="Ativar">
                                        <i class="mdi mdi-account-check-outline"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="mdi mdi-account-search-outline text-muted" style="font-size:2.5rem;opacity:.4"></i>
                                <p class="text-muted mt-2 mb-0">Nenhum usuário encontrado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
