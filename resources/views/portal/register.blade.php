@extends('layouts.portal')

@section('title', 'Criar Conta — Portal do Cliente')

@section('content')
<div class="auth-card">

    <div class="auth-brand">
        <i class="mdi mdi-calendar-check-outline"></i> Agenda
    </div>

    <h4 class="auth-title">Crie sua conta</h4>
    <p class="auth-subtitle">Cadastre-se para começar a agendar seus serviços.</p>

    @if($errors->any())
        <div class="alert alert-danger d-flex align-items-start gap-2 py-2 mb-3">
            <i class="mdi mdi-alert-circle-outline fs-5 mt-1 flex-shrink-0"></i>
            <ul class="mb-0 ps-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('portal.register') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Nome completo</label>
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="mdi mdi-account-outline text-muted"></i>
                </span>
                <input type="text"
                       name="name"
                       class="form-control border-start-0 @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       placeholder="Seu nome"
                       autofocus>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">E-mail</label>
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="mdi mdi-email-outline text-muted"></i>
                </span>
                <input type="email"
                       name="email"
                       class="form-control border-start-0 @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="seu@email.com">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Senha</label>
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="mdi mdi-lock-outline text-muted"></i>
                </span>
                <input type="password"
                       name="password"
                       id="password"
                       class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror"
                       placeholder="Mínimo 6 caracteres">
                <button type="button" class="input-group-text bg-white" onclick="togglePass('password','eye1')">
                    <i class="mdi mdi-eye-outline text-muted" id="eye1"></i>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Confirmar senha</label>
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="mdi mdi-lock-check-outline text-muted"></i>
                </span>
                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       class="form-control border-start-0 border-end-0"
                       placeholder="Repita a senha">
                <button type="button" class="input-group-text bg-white" onclick="togglePass('password_confirmation','eye2')">
                    <i class="mdi mdi-eye-outline text-muted" id="eye2"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-brand w-100 py-2 mb-3">
            <i class="mdi mdi-account-plus-outline me-1"></i> Criar Conta
        </button>

        <p class="text-center text-muted small mb-0">
            Já tem conta?
            <a href="{{ route('portal.login') }}" class="fw-semibold" style="color: var(--brand);">
                Entrar
            </a>
        </p>
    </form>

</div>

<script>
function togglePass(fieldId, iconId) {
    const field = document.getElementById(fieldId);
    const icon  = document.getElementById(iconId);
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('mdi-eye-outline', 'mdi-eye-off-outline');
    } else {
        field.type = 'password';
        icon.classList.replace('mdi-eye-off-outline', 'mdi-eye-outline');
    }
}
</script>
@endsection
