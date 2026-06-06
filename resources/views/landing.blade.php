<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Salão Fácil — Sistema de agendamento online para barbearias e salões de beleza. Seu cliente agenda pelo celular, você só atende.">
    <title>Salão Fácil — Agendamento Online para Salões e Barbearias</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
        :root {
            --brand:      #6C63FF;
            --brand-dark: #5A52D5;
            --accent:     #FF6584;
            --dark:       #1a1a2e;
            --text:       #4a4a68;
            --light:      #f8f8ff;
            --white:      #ffffff;
            --radius:     14px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background: var(--white);
            line-height: 1.6;
        }

        /* ── NAVBAR ── */
        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(108,99,255,.1);
            padding: 0 5%;
            display: flex; align-items: center; justify-content: space-between;
            height: 68px;
        }
        .navbar-brand {
            font-size: 1.4rem; font-weight: 800;
            color: var(--brand); text-decoration: none;
            display: flex; align-items: center; gap: 8px;
        }
        .navbar-brand .dot { color: var(--accent); }
        .navbar-cta {
            background: var(--brand); color: #fff;
            padding: 10px 24px; border-radius: 50px;
            font-weight: 600; font-size: .9rem;
            text-decoration: none;
            transition: background .2s, transform .15s;
            box-shadow: 0 4px 14px rgba(108,99,255,.35);
        }
        .navbar-cta:hover { background: var(--brand-dark); transform: translateY(-1px); }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(135deg, #f0efff 0%, #fff5f7 100%);
            padding: 80px 5% 90px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(108,99,255,.1); color: var(--brand);
            padding: 6px 16px; border-radius: 50px;
            font-size: .8rem; font-weight: 600;
            margin-bottom: 20px;
        }
        .hero-badge .dot { width: 7px; height: 7px; background: var(--accent); border-radius: 50%; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.6;transform:scale(1.4)} }

        .hero h1 {
            font-size: 2.8rem; font-weight: 800;
            color: var(--dark); line-height: 1.2;
            margin-bottom: 20px;
        }
        .hero h1 span { color: var(--brand); }
        .hero h1 .accent { color: var(--accent); }

        .hero-sub {
            font-size: 1.05rem; color: var(--text);
            margin-bottom: 32px; max-width: 480px;
        }

        .hero-stats {
            display: flex; gap: 28px; margin-top: 36px;
        }
        .stat { text-align: center; }
        .stat strong { display: block; font-size: 1.6rem; font-weight: 800; color: var(--brand); }
        .stat span { font-size: .78rem; color: #888; }

        /* ── FORM CARD ── */
        .form-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: 0 20px 60px rgba(108,99,255,.12), 0 4px 16px rgba(0,0,0,.06);
            padding: 36px 32px;
        }
        .form-card h3 {
            font-size: 1.25rem; font-weight: 700;
            color: var(--dark); margin-bottom: 6px;
        }
        .form-card p { font-size: .85rem; color: #888; margin-bottom: 24px; }

        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block; font-size: .82rem;
            font-weight: 600; color: var(--dark);
            margin-bottom: 6px;
        }
        .form-group input,
        .form-group select {
            width: 100%; padding: 12px 16px;
            border: 1.5px solid #e8e8f0;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: .9rem; color: var(--dark);
            background: #fafafa;
            transition: border .2s, box-shadow .2s;
            outline: none;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(108,99,255,.1);
            background: #fff;
        }
        .form-group .error { color: var(--accent); font-size: .78rem; margin-top: 4px; }

        .btn-submit {
            width: 100%; padding: 14px;
            background: var(--brand); color: #fff;
            border: none; border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem; font-weight: 700;
            cursor: pointer;
            transition: background .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 6px 20px rgba(108,99,255,.35);
            margin-top: 8px;
        }
        .btn-submit:hover { background: var(--brand-dark); transform: translateY(-1px); box-shadow: 0 8px 24px rgba(108,99,255,.45); }

        .form-guarantee {
            text-align: center; margin-top: 14px;
            font-size: .75rem; color: #aaa;
        }
        .form-guarantee i { color: var(--brand); margin-right: 4px; }

        .success-box {
            text-align: center; padding: 20px 0;
        }
        .success-box .check {
            font-size: 3rem; margin-bottom: 12px;
        }
        .success-box h4 { font-size: 1.2rem; color: var(--dark); margin-bottom: 8px; }
        .success-box p { font-size: .9rem; color: #888; }

        /* ── SECTION BASE ── */
        section { padding: 80px 5%; }
        .section-label {
            text-align: center;
            font-size: .8rem; font-weight: 600;
            color: var(--brand); letter-spacing: 2px;
            text-transform: uppercase; margin-bottom: 12px;
        }
        .section-title {
            text-align: center;
            font-size: 2rem; font-weight: 800;
            color: var(--dark); margin-bottom: 12px;
        }
        .section-title span { color: var(--brand); }
        .section-sub {
            text-align: center; color: #888;
            max-width: 520px; margin: 0 auto 48px;
            font-size: .95rem;
        }

        /* ── COMO FUNCIONA ── */
        .steps { background: var(--light); }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px; max-width: 900px; margin: 0 auto;
            position: relative;
        }
        .steps-grid::before {
            content: '';
            position: absolute; top: 38px; left: 16%; right: 16%;
            height: 2px; background: linear-gradient(90deg, var(--brand), var(--accent));
            z-index: 0;
        }
        .step { text-align: center; position: relative; z-index: 1; }
        .step-num {
            width: 76px; height: 76px; border-radius: 50%;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            color: #fff; font-size: 1.6rem; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(108,99,255,.3);
        }
        .step h4 { font-size: 1rem; font-weight: 700; color: var(--dark); margin-bottom: 8px; }
        .step p  { font-size: .85rem; color: #888; }

        /* ── BENEFÍCIOS ── */
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px; max-width: 860px; margin: 0 auto;
        }
        .benefit-card {
            background: var(--white);
            border: 1.5px solid #eeeeff;
            border-radius: var(--radius);
            padding: 28px 24px;
            display: flex; gap: 18px; align-items: flex-start;
            transition: box-shadow .2s, transform .2s, border-color .2s;
        }
        .benefit-card:hover {
            box-shadow: 0 8px 32px rgba(108,99,255,.1);
            transform: translateY(-3px);
            border-color: var(--brand);
        }
        .benefit-icon {
            width: 52px; height: 52px; border-radius: 12px;
            background: linear-gradient(135deg, rgba(108,99,255,.12), rgba(255,101,132,.08));
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 1.5rem; color: var(--brand);
        }
        .benefit-card h4 { font-size: .95rem; font-weight: 700; color: var(--dark); margin-bottom: 6px; }
        .benefit-card p  { font-size: .83rem; color: #888; line-height: 1.5; }

        /* ── PARA QUEM ── */
        .for-who { background: var(--light); }
        .who-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px; max-width: 860px; margin: 0 auto;
        }
        .who-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 32px 24px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,.05);
            transition: transform .2s, box-shadow .2s;
        }
        .who-card:hover { transform: translateY(-4px); box-shadow: 0 12px 36px rgba(108,99,255,.12); }
        .who-emoji { font-size: 2.8rem; margin-bottom: 14px; }
        .who-card h4 { font-size: 1rem; font-weight: 700; color: var(--dark); margin-bottom: 8px; }
        .who-card p  { font-size: .82rem; color: #888; }

        /* ── CTA FINAL ── */
        .cta-section {
            background: linear-gradient(135deg, var(--brand) 0%, #9b59b6 100%);
            padding: 80px 5%;
            text-align: center;
            color: #fff;
        }
        .cta-section h2 { font-size: 2.2rem; font-weight: 800; margin-bottom: 12px; }
        .cta-section p  { font-size: 1rem; opacity: .85; margin-bottom: 40px; }
        .cta-form {
            display: flex; gap: 12px;
            max-width: 640px; margin: 0 auto;
            flex-wrap: wrap; justify-content: center;
        }
        .cta-form input,
        .cta-form select {
            flex: 1; min-width: 160px; padding: 14px 18px;
            border: none; border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: .9rem; outline: none;
        }
        .cta-form .btn-white {
            background: #fff; color: var(--brand);
            padding: 14px 28px; border: none;
            border-radius: 10px; font-weight: 700;
            font-family: 'Poppins', sans-serif;
            font-size: .95rem; cursor: pointer;
            transition: transform .15s, box-shadow .2s;
            box-shadow: 0 6px 20px rgba(0,0,0,.15);
        }
        .cta-form .btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,.2); }
        .cta-note { margin-top: 16px; font-size: .78rem; opacity: .7; }

        /* ── FOOTER ── */
        .footer {
            background: var(--dark);
            color: rgba(255,255,255,.6);
            padding: 40px 5%;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 16px;
        }
        .footer-brand {
            font-size: 1.2rem; font-weight: 800;
            color: #fff; text-decoration: none;
        }
        .footer-brand .dot { color: var(--accent); }
        .footer p { font-size: .8rem; }
        .footer-links { display: flex; gap: 20px; }
        .footer-links a {
            color: rgba(255,255,255,.5);
            text-decoration: none; font-size: .82rem;
            transition: color .2s;
        }
        .footer-links a:hover { color: #fff; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .hero { grid-template-columns: 1fr; padding: 60px 5% 50px; }
            .hero h1 { font-size: 2rem; }
            .steps-grid { grid-template-columns: 1fr; }
            .steps-grid::before { display: none; }
            .benefits-grid { grid-template-columns: 1fr; }
            .who-grid { grid-template-columns: 1fr; }
            .hero-stats { justify-content: flex-start; }
        }
        @media (max-width: 560px) {
            .navbar { padding: 0 4%; }
            section { padding: 60px 4%; }
            .hero h1 { font-size: 1.7rem; }
            .section-title { font-size: 1.6rem; }
            .cta-section h2 { font-size: 1.6rem; }
            .cta-form { flex-direction: column; }
            .cta-form input, .cta-form select, .cta-form .btn-white { width: 100%; }
        }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar">
    <a href="#" class="navbar-brand">
        <i class="mdi mdi-scissors-cutting"></i> Salão<span class="dot">Fácil</span>
    </a>
    <a href="#interesse" class="navbar-cta">Quero testar grátis</a>
</nav>

{{-- HERO --}}
<section class="hero" id="inicio">
    <div class="hero-content">
        <div class="hero-badge">
            <span class="dot"></span> Novidade para salões e barbearias
        </div>
        <h1>
            Chega de agenda<br>
            pelo <span class="accent">WhatsApp</span>.<br>
            Seja <span>profissional</span>.
        </h1>
        <p class="hero-sub">
            Com o Salão Fácil, seus clientes agendam online a qualquer hora,
            você recebe tudo organizado e ainda tem tempo para focar no que faz de melhor.
        </p>
        <div class="hero-stats">
            <div class="stat">
                <strong>100%</strong>
                <span>Online</span>
            </div>
            <div class="stat">
                <strong>30 dias</strong>
                <span>Grátis</span>
            </div>
            <div class="stat">
                <strong>Sem</strong>
                <span>Complicação</span>
            </div>
        </div>
    </div>

    {{-- FORMULÁRIO HERO --}}
    <div class="form-card" id="interesse">
        @if(session('lead_success'))
            <div class="success-box">
                <div class="check">🎉</div>
                <h4>Recebemos seu interesse!</h4>
                <p>Em breve entraremos em contato pelo WhatsApp para liberar seu acesso gratuito.</p>
            </div>
        @else
            <h3>Comece grátis por 30 dias</h3>
            <p>Sem cartão de crédito. Cancele quando quiser.</p>

            @if($errors->any())
                <div style="background:#fff0f2;border-left:3px solid #FF6584;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:.82rem;color:#c0392b;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('landing.store') }}">
                @csrf
                <div class="form-group">
                    <label>Seu nome</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: João Silva" required>
                    @error('name') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>WhatsApp</label>
                    <input type="tel" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="(00) 00000-0000" required>
                    @error('whatsapp') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Tipo de negócio</label>
                    <select name="business_type" required>
                        <option value="">Selecione...</option>
                        <option value="Barbearia"       {{ old('business_type') == 'Barbearia' ? 'selected' : '' }}>✂️ Barbearia</option>
                        <option value="Salão de Beleza" {{ old('business_type') == 'Salão de Beleza' ? 'selected' : '' }}>💇 Salão de Beleza</option>
                        <option value="Esmalteria"      {{ old('business_type') == 'Esmalteria' ? 'selected' : '' }}>💅 Esmalteria</option>
                        <option value="Studio de Beleza"{{ old('business_type') == 'Studio de Beleza' ? 'selected' : '' }}>✨ Studio de Beleza</option>
                        <option value="Outro"           {{ old('business_type') == 'Outro' ? 'selected' : '' }}>🏪 Outro</option>
                    </select>
                    @error('business_type') <div class="error">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn-submit">
                    <i class="mdi mdi-rocket-launch-outline"></i> Quero testar grátis!
                </button>
            </form>
            <p class="form-guarantee">
                <i class="mdi mdi-lock-outline"></i> Seus dados estão seguros. Sem spam.
            </p>
        @endif
    </div>
</section>

{{-- COMO FUNCIONA --}}
<section class="steps">
    <p class="section-label">Como funciona</p>
    <h2 class="section-title">Simples assim, em <span>3 passos</span></h2>
    <p class="section-sub">Do cadastro ao primeiro agendamento em menos de 10 minutos.</p>

    <div class="steps-grid">
        <div class="step">
            <div class="step-num">1</div>
            <h4>Você se cadastra</h4>
            <p>Cria sua conta, cadastra seus serviços e configura os horários dos profissionais.</p>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <h4>Cliente acessa e agenda</h4>
            <p>Você compartilha o link. O cliente escolhe o serviço, profissional e horário pelo celular.</p>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <h4>Você só atende</h4>
            <p>A agenda fica organizada no painel. Sem confusão, sem esquecimento, sem furo.</p>
        </div>
    </div>
</section>

{{-- BENEFÍCIOS --}}
<section>
    <p class="section-label">Por que usar</p>
    <h2 class="section-title">Tudo que seu salão <span>precisa</span></h2>
    <p class="section-sub">Feito para quem quer crescer sem complicar.</p>

    <div class="benefits-grid">
        <div class="benefit-card">
            <div class="benefit-icon"><i class="mdi mdi-calendar-check-outline"></i></div>
            <div>
                <h4>Agendamento 24h</h4>
                <p>Seu cliente agenda a qualquer hora, mesmo quando você está dormindo ou atendendo outra pessoa.</p>
            </div>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon"><i class="mdi mdi-account-group-outline"></i></div>
            <div>
                <h4>Múltiplos profissionais</h4>
                <p>Cadastre toda a sua equipe. Cada um tem sua própria agenda e disponibilidade configurável.</p>
            </div>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon"><i class="mdi mdi-chart-bar"></i></div>
            <div>
                <h4>Relatórios simples</h4>
                <p>Veja quantos atendimentos foram feitos, cancelados e por qual profissional — tudo em um clique.</p>
            </div>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon"><i class="mdi mdi-cellphone-check"></i></div>
            <div>
                <h4>Funciona no celular</h4>
                <p>Portal do cliente 100% responsivo. Seu cliente agenda direto pelo smartphone, sem instalar nada.</p>
            </div>
        </div>
    </div>
</section>

{{-- PARA QUEM É --}}
<section class="for-who">
    <p class="section-label">Para quem é</p>
    <h2 class="section-title">Feito para o seu <span>negócio</span></h2>
    <p class="section-sub">Se você depende de horário marcado, o Salão Fácil é para você.</p>

    <div class="who-grid">
        <div class="who-card">
            <div class="who-emoji">✂️</div>
            <h4>Barbearia</h4>
            <p>Organize os horários de cada barbeiro e elimine a fila de WhatsApp de uma vez por todas.</p>
        </div>
        <div class="who-card">
            <div class="who-emoji">💇</div>
            <h4>Salão de Beleza</h4>
            <p>Controle cortes, coloração, escova e mais — cada serviço com seu profissional certo.</p>
        </div>
        <div class="who-card">
            <div class="who-emoji">💅</div>
            <h4>Esmalteria</h4>
            <p>Seus clientes escolhem o horário e serviço. Você foca no atendimento sem interrupção.</p>
        </div>
    </div>
</section>

{{-- CTA FINAL --}}
<section class="cta-section">
    <h2>Pronto para ter uma agenda<br>profissional?</h2>
    <p>Comece grátis por 30 dias. Sem cartão, sem burocracia.</p>

    @if(session('lead_success'))
        <div style="background:rgba(255,255,255,.15);border-radius:12px;padding:20px 32px;display:inline-block;margin-top:8px;">
            <p style="font-size:1rem;opacity:1;margin:0;">🎉 <strong>Recebemos seu interesse!</strong> Em breve entraremos em contato.</p>
        </div>
    @else
        <form method="POST" action="{{ route('landing.store') }}">
            @csrf
            <div class="cta-form">
                <input type="text"  name="name"          placeholder="Seu nome"          required value="{{ old('name') }}">
                <input type="tel"   name="whatsapp"       placeholder="(00) 00000-0000"   required value="{{ old('whatsapp') }}">
                <select name="business_type" required>
                    <option value="">Tipo de negócio</option>
                    <option value="Barbearia">✂️ Barbearia</option>
                    <option value="Salão de Beleza">💇 Salão de Beleza</option>
                    <option value="Esmalteria">💅 Esmalteria</option>
                    <option value="Studio de Beleza">✨ Studio de Beleza</option>
                    <option value="Outro">🏪 Outro</option>
                </select>
                <button type="submit" class="btn-white">Quero testar! 🚀</button>
            </div>
        </form>
        <p class="cta-note"><i class="mdi mdi-lock-outline"></i> Seus dados estão seguros. Sem spam.</p>
    @endif
</section>

{{-- FOOTER --}}
<footer class="footer">
    <a href="#" class="footer-brand">Salão<span class="dot">Fácil</span></a>
    <p>© {{ date('Y') }} Salão Fácil. Todos os direitos reservados.</p>
    <div class="footer-links">
        <a href="{{ route('auth-login-basic') }}">Área do Admin</a>
    </div>
</footer>

</body>
</html>
