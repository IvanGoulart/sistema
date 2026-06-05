@extends('layouts.portal')

@section('title', 'Entrar — Portal do Cliente')

@section('content')
<div class="auth-card">

    <div class="auth-brand">
        <i class="mdi mdi-calendar-check-outline"></i> Agenda
    </div>

    <h4 class="auth-title">Bem-vindo de volta!</h4>
    <p class="auth-subtitle">Acesse sua conta para gerenciar seus agendamentos.</p>

    @if($errors->any())
        <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3">
            <i class="mdi mdi-alert-circle-outline fs-5"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('portal.login.post') }}">
        @csrf

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
                       placeholder="seu@email.com"
                       autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Senha</label>
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="mdi mdi-lock-outline text-muted"></i>
                </span>
                <input type="password"
                       name="password"
                       id="password"
                       class="form-control border-start-0 border-end-0"
                       placeholder="••••••••">
                <button type="button"
                        class="input-group-text bg-white"
                        onclick="togglePassword()">
                    <i class="mdi mdi-eye-outline text-muted" id="eye-icon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-brand w-100 py-2 mb-3">
            <i class="mdi mdi-login me-1"></i> Entrar
        </button>

        <p class="text-center text-muted small mb-0">
            Não tem conta?
            <a href="{{ route('portal.cadastro') }}" class="fw-semibold" style="color: var(--brand);">
                Cadastre-se gratuitamente
            </a>
        </p>
    </form>

</div>

<script>
function togglePassword() {
    const field = document.getElementById('password');
    const icon  = document.getElementById('eye-icon');
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
