# Sistema de Agendamento SaaS — Documentação de Contexto

> Gerado em: Junho/2026 | Autor: IvanGoulart | Stack: Laravel 10 + Livewire 3

---

## 1. Visão Geral do Projeto

Sistema SaaS de agendamento de serviços com dois painéis distintos:

- **Painel Administrativo** — para admins e funcionários gerenciarem agenda, serviços, usuários e disponibilidade
- **Portal do Cliente** — para clientes se cadastrarem, agendarem serviços e acompanharem atendimentos

---

## 2. Stack e Infraestrutura

| Item | Tecnologia |
|---|---|
| Backend | Laravel 10 |
| Frontend Reativo | Livewire 3 |
| Banco de Dados | MySQL 8.4 |
| Infraestrutura | Docker via Laravel Sail |
| CSS Admin | Bootstrap 5 + Materio Template |
| CSS Portal | Bootstrap 5 via CDN |
| Ícones | Material Design Icons (MDI) via CDN |
| Autenticação | Laravel Auth nativo (sessão) |

### Docker
```bash
# Subir containers
./vendor/bin/sail up -d

# Acessar container
docker compose exec laravel.test bash

# Rodar migrations
php artisan migrate

# Rodar seeders
php artisan db:seed
```

---

## 3. Arquitetura Multi-Tenant

- Cada empresa (tenant) tem seu próprio `tenant_id`
- Isolamento via `session('tenant_id') ?? 1`
- Tabela `tenants`: `id`, `name`, `slug`, `email`, `phone`, `is_active`
- Tabela `tenant_user`: pivot entre `tenants` e `users` com `role` e `is_active`
- Todos os dados de negócio (serviços, agendamentos, disponibilidade) são filtrados por `tenant_id`

---

## 4. Sistema de Permissões

### Tabelas
```
permissions: id, name (admin | employee | client)
user_permissions: id, tenant_id, user_id, code_permission (FK → permissions.id)
```

### Middleware
- `CheckPermission` — verifica `permission:admin` ou `permission:client` via `User::hasPermission()`
- Registrado como alias `permission` em `app/Http/Kernel.php`

### Relação no Model User
```php
// User::permissions() — belongsToMany
public function permissions() {
    return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'code_permission');
}

// Verificar permissão
public function hasPermission(string $permissionName): bool {
    return $this->permissions()->where('name', $permissionName)->exists();
}

// CORRETO — para acessar permissão do usuário na view:
$user->permissions->first()?->name

// ERRADO — relação que NÃO existe no model:
$user->userPermission->permission->name  // ← bug conhecido, já corrigido
```

---

## 5. Estrutura do Banco de Dados

```
users
  id, name, email, password, active, created_at, updated_at

permissions
  id, name (admin | employee | client)

user_permissions
  id, tenant_id, user_id, code_permission, created_at, updated_at

tenants
  id, name, slug, email, phone, is_active, created_at, updated_at

tenant_user
  tenant_id, user_id, role, is_active

services
  id, tenant_id, name, description, created_at, updated_at

user_services (pivot employees ↔ services)
  user_id, service_id

schedules
  id, employee_id, client_id, service_id, day (date), hour (time), cancel (boolean)

employee_weekly_schedules
  id, tenant_id, employee_id, day_of_week (0=Dom…6=Sab), start_time, end_time
  UNIQUE(tenant_id, employee_id, day_of_week)
```

---

## 6. Mapa de Rotas

### Públicas (sem autenticação)
| Método | URI | Nome | Descrição |
|---|---|---|---|
| GET | `/` | `auth-login-basic` | Login admin |
| POST | `/auth/check-authenticate` | `auth-check-authenticate` | Processar login admin |
| GET | `/logout` | `auth.logout` | Logout admin |

### Portal do Cliente
| Método | URI | Nome | Middleware |
|---|---|---|---|
| GET | `/portal/login` | `portal.login` | guest |
| POST | `/portal/login` | `portal.login.post` | guest |
| GET | `/portal/cadastro` | `portal.cadastro` | guest |
| POST | `/portal/register` | `portal.register` | guest |
| GET | `/portal` | `portal.home` | auth + permission:client |
| GET | `/portal/agendar` | `portal.agendar` | auth + permission:client |
| GET | `/portal/meus-agendamentos` | `portal.meus-agendamentos` | auth + permission:client |
| PATCH | `/portal/cancelar/{id}` | `portal.cancelar` | auth + permission:client |
| POST | `/portal/logout` | `portal.logout` | auth + permission:client |

### Painel Admin (todas requerem auth + permission:admin)
| Método | URI | Nome | Descrição |
|---|---|---|---|
| GET | `/dashboard` | `dashboard-analytics` | Dashboard |
| GET | `/users/list` | `users-list` | Lista de usuários |
| GET | `/user/edit/{id}` | `user-edit` | Editar usuário |
| PUT | `/user/update/{id}` | `user-update` | Atualizar usuário |
| POST | `/user/create` | `user-create` | Criar usuário |
| GET | `/auth/register-basic` | `auth-register-basic` | Form novo usuário |
| GET | `/services` | `services.index` | Gestão de serviços |
| GET | `/schedule/admin` | `schedule.admin` | Agenda admin (semanal) |
| GET | `/schedule/availability` | `schedule.availability` | Disponibilidade funcionários |
| GET | `/reports` | `reports.index` | Relatório de agendamentos |
| GET | `/tenant/create` | `tenants.create` | Cadastro de empresa |

---

## 7. Componentes Livewire

| Componente | Arquivo PHP | Descrição |
|---|---|---|
| `services.service-manager` | `app/Livewire/Services/ServiceManager.php` | CRUD de serviços + vínculo com funcionários |
| `schedule.admin-agenda` | `app/Livewire/Schedule/AdminAgenda.php` | Agenda semanal admin com filtros |
| `schedule.employee-availability` | `app/Livewire/Schedule/EmployeeAvailability.php` | Disponibilidade semanal por funcionário |
| `form-create-agenda` | `app/Livewire/FormCreateAgenda.php` | Agendamento pelo cliente (admin e portal) |
| `tenant.form-create-tenant` | `app/Livewire/Tenant/FormCreateTenant.php` | CRUD de empresas inline |
| `reports.agenda-report` | `app/Livewire/Reports/AgendaReport.php` | Relatório com filtros reativos |

---

## 8. Funcionalidades Implementadas

### 8.1 Gestão de Serviços
- CRUD completo via Livewire (sem reload de página)
- Vínculo de funcionários por serviço (tabela `user_services`)
- Exclusão com confirmação inline (Sim/Não)
- Contagem de funcionários por serviço

### 8.2 Disponibilidade Semanal de Funcionários
- Grade de 7 dias (Dom–Sab) com toggle on/off por dia
- Campos de horário início/fim por dia
- Salva na tabela `employee_weekly_schedules`
- Lógica: apaga todos os registros do funcionário e reinsere apenas os dias ativos

### 8.3 Agenda (Admin)
- Visão semanal (Seg–Dom)
- Navegação por semana (anterior / próxima / hoje)
- Filtro por funcionário
- Cards por dia com tabela de agendamentos
- Cancelamento com confirmação inline
- Dia atual destacado em azul, dias passados em cinza

### 8.4 Agenda (Formulário — Admin e Portal)
- Seleção de data (mínimo: hoje)
- Carrega funcionários disponíveis baseado no `day_of_week` da data selecionada
- Exibe horários disponíveis como pills clicáveis
- Card de resumo antes de confirmar
- Reset completo após agendamento
- Exibe próximos e histórico de agendamentos do usuário logado

### 8.5 Portal do Cliente
- **Login** — com validação de `permission:client` (admin não pode entrar)
- **Cadastro** — cria usuário + tenant + permissão `client` em transação
- **Agendar** — usa o mesmo componente Livewire `form-create-agenda`
- **Meus Agendamentos** — lista próximos e histórico, com cancelamento via POST

### 8.6 Relatório de Agendamentos
- Filtros: período (data início/fim), funcionário, serviço
- Cards de resumo: total, realizados, cancelados, taxa de cancelamento
- Tabela detalhada com todos os dados
- Botão imprimir → `window.print()` com CSS que oculta navbar/menu

### 8.7 Melhorias de Layout
- Lista de usuários com cards de resumo (total, admins, funcionários, clientes)
- Avatar com inicial do nome em vez de imagem estática
- Badges de permissão coloridos: admin=vermelho, employee=amarelo, client=azul
- Cadastro de empresa com CRUD inline (editar/excluir sem sair da página)
- Formulário de usuário integrado ao layout do admin

---

## 9. Segurança

### Correções aplicadas
- `/dashboard` e todas as rotas do painel admin agora exigem `auth + permission:admin`
- `/auth/register-basic` e `/user/create` (POST) também protegidos
- Todas as rotas de template do Materio (`/ui/*`, `/forms/*`, etc.) protegidas
- `Authenticate` middleware redireciona rotas `/portal/*` para `portal.login` (não para o login admin)
- `RedirectIfAuthenticated` redireciona usuários autenticados no portal para `portal.home`

### Fluxo de acesso
```
Visitante → GET /              → Login admin
Visitante → GET /portal/login  → Login portal

Cliente logado → GET /dashboard     → 403 Sem Permissão
Admin logado   → GET /portal/agendar → 403 Sem Permissão

Cliente logado → GET /portal/agendar → ✓ OK
Admin logado   → GET /dashboard       → ✓ OK
```

---

## 10. Credenciais de Desenvolvimento

| Perfil | URL de Acesso | E-mail | Senha |
|---|---|---|---|
| Admin | `http://localhost` | `admin@sistema.test` | `password` |
| Cliente | `http://localhost/portal/login` | `cliente@teste.com` | `password` |

---

## 11. Layouts e Views

```
resources/views/
├── layouts/
│   ├── contentNavbarLayout.blade.php  ← layout do painel admin
│   └── portal.blade.php               ← layout do portal do cliente
├── content/
│   ├── dashboard/dashboards-analytics.blade.php
│   ├── user/users-list.blade.php
│   ├── authentications/auth-register-basic.blade.php  ← criar/editar usuário
│   ├── services/index.blade.php
│   ├── schedule/
│   │   ├── admin-agenda.blade.php
│   │   ├── employee-availability.blade.php
│   │   └── schedule-create.blade.php
│   ├── tenant/tenant-create.blade.php
│   └── reports/index.blade.php
├── livewire/
│   ├── services/service-manager.blade.php
│   ├── schedule/
│   │   ├── admin-agenda.blade.php
│   │   └── employee-availability.blade.php
│   ├── form-create-agenda.blade.php
│   ├── tenant/form-create-tenant.blade.php
│   └── reports/agenda-report.blade.php
└── portal/
    ├── login.blade.php
    ├── register.blade.php
    ├── agendar.blade.php
    └── meus-agendamentos.blade.php
```

---

## 12. Padrões e Convenções

### Badges de cor no portal
O portal usa Bootstrap 5 via CDN **sem** as classes customizadas do Materio. Por isso usar `bg-label-*` não funciona. Sempre usar `rgba()` inline:
```html
<!-- Portal: usar rgba inline -->
<span style="background:rgba(255,171,0,.15);color:#a07800;">Serviço</span>

<!-- Admin (Materio): pode usar bg-label-* -->
<span class="badge bg-label-warning">Serviço</span>
```

### Confirmação de ações destrutivas
Sempre usar confirmação inline (Sim/Não) via Livewire, nunca `confirm()` do browser:
```php
// No componente Livewire
public ?int $confirmingDeleteId = null;
public function confirmDelete(int $id): void { $this->confirmingDeleteId = $id; }
public function delete(int $id): void { /* faz a ação */ $this->confirmingDeleteId = null; }
```

### Isolamento por tenant
```php
$tenantId = session('tenant_id') ?? 1;
// Sempre filtrar queries por tenant_id
DB::table('services')->where('tenant_id', $tenantId)->get();
```

---

## 13. Próximas Implementações Sugeridas

### Alta prioridade
- [ ] **Preço nos serviços** — adicionar coluna `price` na tabela `services` e exibir no relatório
- [ ] **Dashboard com métricas reais** — substituir o dashboard do template por cards com dados do banco (agendamentos hoje, esta semana, cancelamentos)
- [ ] **Notificação por e-mail** — enviar e-mail ao cliente quando o agendamento for confirmado ou cancelado
- [ ] **Permissão `employee`** — funcionário deve conseguir ver sua própria agenda (filtrada por ele) no painel admin

### Média prioridade
- [ ] **Paginação na lista de usuários e relatório** — para quando houver muitos registros
- [ ] **Perfil do cliente** — editar nome, e-mail e senha no portal
- [ ] **Reagendamento** — permitir ao cliente remarcar em vez de cancelar
- [ ] **Intervalo entre atendimentos** — configurar duração de cada serviço para evitar conflitos de horário

### Baixa prioridade
- [ ] **Multi-tenant completo** — tela de seleção de tenant no login admin
- [ ] **Exportar relatório para Excel** — usando `maatwebsite/excel`
- [ ] **PWA no portal** — para o cliente usar como app no celular

---

## 14. Comandos Úteis

```bash
# Tinker — criar usuário cliente manualmente
php artisan tinker --execute="
\$user = App\Models\User::create(['name'=>'Nome','email'=>'e@mail.com','password'=>bcrypt('password')]);
\$permId = DB::table('permissions')->where('name','client')->value('id');
DB::table('user_permissions')->insert(['tenant_id'=>1,'user_id'=>\$user->id,'code_permission'=>\$permId,'created_at'=>now(),'updated_at'=>now()]);
echo 'OK';
"

# Ver rotas do portal
php artisan route:list --path=portal -v

# Limpar caches
php artisan route:clear && php artisan config:clear && php artisan view:clear

# Rodar migrations novas
php artisan migrate

# Push via SSH
git add -A && git commit -m "mensagem" && git push origin main
```

---

*Documentação gerada ao final da sessão de desenvolvimento — Junho/2026*
