@extends('layouts/contentNavbarLayout')

@section('title', isset($user) ? 'Editar Usuário' : 'Novo Usuário')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1">{{ isset($user) ? 'Editar Usuário' : 'Novo Usuário' }}</h4>
        <p class="text-muted mb-0">{{ isset($user) ? 'Altere os dados do usuário abaixo' : 'Preencha os dados para cadastrar um novo usuário' }}</p>
    </div>
    <a href="{{ route('users-list') }}" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-1"></i> Voltar
    </a>
</div>

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

<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-3 pb-3">
                <div class="d-flex align-items-center justify-content-center text-white"
                     style="width:38px;height:38px;border-radius:8px;background:#696cff;">
                    <i class="mdi mdi-{{ isset($user) ? 'account-edit-outline' : 'account-plus-outline' }}"></i>
                </div>
                <h5 class="card-title mb-0">{{ isset($user) ? 'Dados do usuário' : 'Cadastrar usuário' }}</h5>
            </div>

            <div class="card-body">
                @if(isset($user))
                    <form method="POST" action="{{ route('user-update', $user->id) }}">
                    @method('PUT')
                @else
                    <form method="POST" action="{{ route('user-create') }}">
                @endif
                @csrf

                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label fw-semibold">Nome completo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-account-outline text-muted"></i></span>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name ?? '') }}"
                                   placeholder="Nome do usuário"
                                   autofocus>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-email-outline text-muted"></i></span>
                            <input type="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email ?? '') }}"
                                   placeholder="email@exemplo.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Permissão <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-shield-account-outline text-muted"></i></span>
                            <select name="permission"
                                    class="form-select @error('permission') is-invalid @enderror">
                                <option value="">Selecione uma permissão</option>
                                @foreach($permissions as $permission)
                                    <option value="{{ $permission->id }}"
                                        {{ (isset($selectedPermissionId) && $selectedPermissionId == $permission->id) || old('permission') == $permission->id ? 'selected' : '' }}>
                                        {{ ucfirst($permission->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('permission') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Senha {{ isset($user) ? '(deixe em branco para manter)' : '' }}
                            @if(!isset($user)) <span class="text-danger">*</span> @endif
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-lock-outline text-muted"></i></span>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control border-end-0 @error('password') is-invalid @enderror"
                                   placeholder="{{ isset($user) ? 'Nova senha (opcional)' : 'Mínimo 8 caracteres' }}">
                            <button type="button" class="input-group-text bg-white" onclick="togglePass()">
                                <i class="mdi mdi-eye-outline text-muted" id="eye-icon"></i>
                            </button>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="mdi mdi-content-save-outline me-1"></i>
                        {{ isset($user) ? 'Salvar alterações' : 'Cadastrar usuário' }}
                    </button>
                    <a href="{{ route('users-list') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePass() {
    const f = document.getElementById('password');
    const i = document.getElementById('eye-icon');
    f.type = f.type === 'password' ? 'text' : 'password';
    i.classList.toggle('mdi-eye-outline');
    i.classList.toggle('mdi-eye-off-outline');
}
</script>

@endsection
