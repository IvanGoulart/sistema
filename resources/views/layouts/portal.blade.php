<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal do Cliente')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">

    @livewireStyles

    <style>
        :root { --brand: #696cff; --brand-dark: #5558e3; }

        body { background-color: #f4f5fb; font-family: 'Public Sans', sans-serif; }

        /* Navbar */
        .portal-nav {
            background: #fff;
            border-bottom: 1px solid #e4e6ef;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
        }
        .portal-nav .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--brand) !important;
            letter-spacing: -.3px;
        }
        .portal-nav .nav-link {
            color: #566a7f;
            font-weight: 500;
            padding: .4rem .85rem;
            border-radius: 6px;
            transition: background .15s, color .15s;
        }
        .portal-nav .nav-link:hover { background: #f0f1ff; color: var(--brand); }
        .portal-nav .nav-link.active { background: #f0f1ff; color: var(--brand); font-weight: 600; }

        /* Avatar inicial */
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--brand);
            color: #fff;
            font-size: .8rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        /* Conteúdo */
        .portal-content { min-height: calc(100vh - 130px); padding: 2rem 0; }

        /* Páginas de auth (login/register) */
        .auth-wrapper {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 2rem 1rem;
        }
        .auth-card {
            width: 100%; max-width: 440px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(105,108,255,.08), 0 2px 8px rgba(0,0,0,.06);
            padding: 2.5rem 2.5rem;
        }
        .auth-brand {
            font-size: 1.4rem; font-weight: 700;
            color: var(--brand);
            display: flex; align-items: center; gap: .5rem;
            margin-bottom: 1.75rem;
        }
        .auth-title { font-size: 1.35rem; font-weight: 700; color: #343a40; margin-bottom: .25rem; }
        .auth-subtitle { color: #8592a3; font-size: .9rem; margin-bottom: 1.75rem; }
        .btn-brand {
            background: var(--brand); border-color: var(--brand); color: #fff; font-weight: 600;
        }
        .btn-brand:hover { background: var(--brand-dark); border-color: var(--brand-dark); color: #fff; }

        /* Footer */
        .portal-footer {
            background: #fff; border-top: 1px solid #e4e6ef;
            padding: .9rem 0; text-align: center;
            color: #8592a3; font-size: .82rem;
        }
    </style>
</head>
<body>

@auth
{{-- Navbar autenticado --}}
<nav class="navbar portal-nav px-0 py-0">
    <div class="container d-flex align-items-center gap-3 py-2">

        <a class="navbar-brand me-3" href="{{ route('portal.home') }}">
            <i class="mdi mdi-calendar-check-outline me-1"></i> Agenda
        </a>

        <div class="d-flex gap-1">
            <a href="{{ route('portal.agendar') }}"
               class="nav-link {{ request()->routeIs('portal.agendar') ? 'active' : '' }}">
                <i class="mdi mdi-calendar-plus-outline me-1"></i>
                <span class="d-none d-sm-inline">Novo Agendamento</span>
                <span class="d-inline d-sm-none">Agendar</span>
            </a>
            <a href="{{ route('portal.meus-agendamentos') }}"
               class="nav-link {{ request()->routeIs('portal.meus-agendamentos') ? 'active' : '' }}">
                <i class="mdi mdi-calendar-clock-outline me-1"></i>
                <span class="d-none d-sm-inline">Meus Agendamentos</span>
                <span class="d-inline d-sm-none">Agenda</span>
            </a>
        </div>

        <div class="ms-auto d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="text-muted small d-none d-md-inline">{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('portal.logout') }}" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="mdi mdi-logout me-1"></i>
                    <span class="d-none d-sm-inline">Sair</span>
                </button>
            </form>
        </div>

    </div>
</nav>

<div class="portal-content">
    <div class="container">
        @yield('content')
    </div>
</div>

<footer class="portal-footer">
    © {{ date('Y') }} Sistema de Agendamento — Todos os direitos reservados
</footer>

@else
{{-- Páginas de guest (login/register) sem navbar --}}
<div class="auth-wrapper">
    @yield('content')
</div>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@livewireScripts
</body>
</html>
