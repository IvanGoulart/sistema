# Sistema de Agendamento SaaS — Documentação de Contexto

> Atualizado em: Junho/2026 (Fase 1 — multi-tenancy ligado) | Autor: IvanGoulart | Stack: Laravel 10 + Livewire 3

---

## 1. Visão Geral do Projeto

Sistema SaaS de agendamento de serviços para salões/negócios de beleza, com a marca **"Salão Fácil"**. Dois painéis distintos com layouts e fluxos de autenticação separados:

- **Painel Administrativo** (`/dashboard`, `/admin`) — admins e profissionais gerenciam agenda, serviços, usuários e disponibilidade
- **Portal do Cliente** (`/portal/{slug}`) — clientes se cadastram, agendam serviços e acompanham atendimentos, **por empresa (slug)**

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
./vendor/bin/sail up -d                          # Subir containers
docker compose exec laravel.test bash            # Acessar container
php artisan migrate                              # Rodar migrations
php artisan db:seed                              # Seed de dev (TestMatrixSeeder)
php artisan db:seed --class=ProductionSeeder     # Seed de produção
```

---

## 3. Arquitetura Multi-Tenant (Fase 1 — ligada)

- Cada empresa (tenant) tem seu próprio `tenant_id`.
- A empresa ativa fica na **sessão** (`session('tenant_id')`), resolvida de formas diferentes em cada painel:
  - **Portal**: o middleware `ResolveTenant` lê o **slug** da URL (`/portal/{tenantSlug}/...`), valida que a empresa existe e está ativa (404 se não), grava `tenant_id` na sessão e define o slug como default de rota (`URL::defaults`) para que todos os `route('portal.*')` continuem funcionando.
  - **Admin**: no login (`LoginBasic@checkAuthenticate`), a empresa ativa = **primeira empresa** em que o usuário tem papel `admin` ou `employee`. O seletor de empresa (switcher) virá em fase posterior.
- Tabela `tenants`: `id`, `name`, `slug` (único), `email`, `phone`, `is_active`.
- Tabela `tenant_user`: pivot entre `tenants` e `users` com `role` e `is_active`.
- **Todo dado de negócio é filtrado por `tenant_id`**. Tabelas com `tenant_id`: `services`, `schedules`, `employee_weekly_schedules`, `user_permissions`, `user_services`.

```php
$tenantId = session('tenant_id') ?? 1;
DB::table('services')->where('tenant_id', $tenantId)->get();
```

---

## 4. Sistema de Permissões — DUAS camadas distintas

> Não confundir as duas. São independentes.

### 4.1 Papéis por empresa (`admin` | `employee` | `client`)
Vivem em `user_permissions` **com `tenant_id`** — ou seja, um usuário pode ser `admin` na Empresa A e `client` na Empresa B. Verificados pelo middleware alias `permission`.

```
permissions: id, name (admin | employee | client)
user_permissions: id, tenant_id, user_id, code_permission (FK → permissions.id)
```

```php
// Rotas
Route::middleware(['auth', 'permission:admin'])->group(...)
Route::middleware(['auth', 'permission:admin,employee'])->group(...)  // QUALQUER um dos papéis
Route::middleware(['auth', 'permission:client'])->group(...)
```

`User::hasPermission(string $name, ?int $tenantId = null)` consulta `user_permissions` juntado a `permissions`, **filtrado pela empresa ativa** (`session('tenant_id')`) quando `$tenantId` é omitido.

> **`admin` aqui = dono de UM salão (tenant)** — NÃO é o dono da plataforma. Vários admins de salão coexistem, cada um escopado à própria empresa.

Helpers no model `User`: `isAdmin()`, `isProfessional()` (=`employee`), `isClient()`, `role()`, `tenantIdsWithAnyRole([...])`.

```php
// Ler o papel do usuário numa view:
$user->permissions->first()?->name      // ✓ correto
$user->userPermission->permission->name // ✗ relação que não existe
```

### 4.2 Super-admin da plataforma (dono do SaaS)
Flag **global** `users.is_super_admin` (boolean), **independente de qualquer empresa** e **fora** de `user_permissions`. Verificado por `User::isSuperAdmin()` e protegido pelo middleware alias `platform` (`App\Http\Middleware\PlatformAdmin`).

```php
Route::middleware(['auth', 'platform'])->group(...)  // só o dono da plataforma
```

A gestão de empresas (`/tenant/*` → `App\Livewire\Tenant\FormCreateTenant`) fica atrás de `platform`. **Um admin de salão nunca pode chegar ao CRUD de tenants** — isso vazaria/permitiria editar outras empresas. Como ações Livewire batem em `/livewire/update` (fora do middleware de rota), `FormCreateTenant` **re-checa a autorização** em `mount()` e em toda ação mutante (`save`/`delete`/`openEdit`/`confirmDelete`) via `abort_unless($user->isSuperAdmin(), 403)`.

Marcar super-admins via seeders (`ProductionSeeder`, `TestMatrixSeeder`). Itens de menu podem ser escondidos de não-super-admins com `"platform": true` em `resources/menu/verticalMenu.json` (filtrado em `submenu.blade.php`).

### Middlewares (aliases em `app/Http/Kernel.php`)
| Alias | Classe | Função |
|---|---|---|
| `permission` | `CheckPermission` | Papel por empresa; aceita lista (`permission:admin,employee`) |
| `platform` | `PlatformAdmin` | Só super-admin (flag global), não depende da empresa ativa |
| `tenant` | `ResolveTenant` | Resolve empresa pelo slug da URL do portal |

---

## 5. Estrutura do Banco de Dados

```
users
  id, name, email, password, active, is_super_admin (plataforma, global), timestamps

permissions
  id, name (admin | employee | client)   -- papéis por empresa, NÃO super-admin

user_permissions
  id, tenant_id, user_id, code_permission (FK → permissions.id), timestamps
  UNIQUE relacionado a (tenant_id, user_id, code_permission)

tenants
  id, name, slug (único), email, phone, is_active, timestamps

tenant_user
  tenant_id, user_id, role, is_active

services
  id, tenant_id, name, description, price (decimal 10,2, nullable), timestamps

user_services (pivot employees ↔ services)
  user_id, service_id, tenant_id

schedules
  id, tenant_id, employee_id, client_id, service_id, day (date), hour (time), cancel (bool)

employee_weekly_schedules
  id, tenant_id, employee_id, day_of_week (0=Dom…6=Sab), start_time, end_time
  UNIQUE(tenant_id, employee_id, day_of_week)

leads
  id, name, whatsapp, business_type, created_at
```

> Existem também migrations de `financial_categories` e `financial_entries` (Mar/2026), ainda não conectadas à UI — área financeira futura.

---

## 6. Mapa de Rotas

### Públicas (sem autenticação)
| Método | URI | Nome | Descrição |
|---|---|---|---|
| GET | `/` | `landing` | Landing page Salão Fácil |
| POST | `/interesse` | `landing.store` | Captura de lead (throttle 3/10min) |
| GET | `/admin` | `auth-login-basic` | Login admin |
| POST | `/auth/check-authenticate` | `auth-check-authenticate` | Processar login admin |
| GET | `/logout` | `auth.logout` | Logout admin |

### Portal do Cliente — multi-empresa por slug (`portal/{tenantSlug}`, middleware `tenant`)
| Método | URI | Nome | Middleware |
|---|---|---|---|
| GET | `/portal/{slug}/login` | `portal.login` | tenant + guest |
| POST | `/portal/{slug}/login` | `portal.login.post` | tenant + guest |
| GET | `/portal/{slug}/cadastro` | `portal.cadastro` | tenant + guest |
| POST | `/portal/{slug}/register` | `portal.register` | tenant + guest |
| GET | `/portal/{slug}` | `portal.home` | tenant + auth + permission:client |
| GET | `/portal/{slug}/agendar` | `portal.agendar` | tenant + auth + permission:client |
| GET | `/portal/{slug}/meus-agendamentos` | `portal.meus-agendamentos` | tenant + auth + permission:client |
| PATCH | `/portal/{slug}/cancelar/{id}` | `portal.cancelar` | tenant + auth + permission:client |
| POST | `/portal/{slug}/logout` | `portal.logout` | tenant + auth + permission:client |

### Painel Admin (auth + permission:admin)
| Método | URI | Nome | Descrição |
|---|---|---|---|
| GET | `/dashboard` | `dashboard-analytics` | Dashboard |
| GET | `/users/list` | `users-list` | Lista de usuários |
| GET | `/user/edit/{id}` | `user-edit` | Editar usuário |
| PUT | `/user/update/{id}` | `user-update` | Atualizar usuário |
| POST | `/user/create` | `user-create` | Criar usuário |
| GET | `/schedule/create` | `schedule-create` | Form de agendamento (admin) |

### Painel compartilhado (auth + permission:admin,employee)
> O profissional vê apenas os **próprios** dados (agenda/disponibilidade travadas nele) e serviços em modo leitura. O escopo é feito nos componentes Livewire conforme o papel na empresa ativa.

| Método | URI | Nome | Descrição |
|---|---|---|---|
| GET | `/schedule/admin` | `schedule.admin` | Agenda semanal |
| GET | `/schedule/availability` | `schedule.availability` | Disponibilidade de funcionários |
| GET | `/services` | `services.index` | Gestão de serviços |
| GET | `/reports` | `reports.index` | Relatório de agendamentos |

### Painel da plataforma (auth + platform — só super-admin)
| Método | URI | Nome | Descrição |
|---|---|---|---|
| GET | `/tenant` | `tenants.index` | Gestão de empresas |
| GET | `/tenant/create` | `tenants.create` | Cadastro de empresa |

---

## 7. Componentes Livewire

| Componente | Arquivo PHP | Descrição |
|---|---|---|
| `services.service-manager` | `app/Livewire/Services/ServiceManager.php` | CRUD de serviços + vínculo com funcionários (leitura p/ employee) |
| `schedule.admin-agenda` | `app/Livewire/Schedule/AdminAgenda.php` | Agenda semanal com filtros (escopada ao employee) |
| `schedule.employee-availability` | `app/Livewire/Schedule/EmployeeAvailability.php` | Disponibilidade semanal por funcionário |
| `form-create-agenda` | `app/Livewire/FormCreateAgenda.php` | Agendamento (admin e portal) |
| `tenant.form-create-tenant` | `app/Livewire/Tenant/FormCreateTenant.php` | CRUD de empresas inline (re-checa super-admin) |
| `reports.agenda-report` | `app/Livewire/Reports/AgendaReport.php` | Relatório com filtros reativos |

---

## 8. Funcionalidades Implementadas

### 8.1 Gestão de Serviços
- CRUD completo via Livewire, com **preço** (`price`).
- Vínculo de funcionários por serviço (`user_services`), escopado por tenant.
- Exclusão com confirmação inline; profissional vê em modo somente-leitura.

### 8.2 Disponibilidade Semanal de Funcionários
- Grade de 7 dias (Dom–Sab) com toggle on/off e horário início/fim por dia.
- Salva em `employee_weekly_schedules`; profissional só edita a própria.

### 8.3 Agenda (Admin/Profissional)
- Visão semanal com navegação, filtro por funcionário (escopado ao employee), cancelamento com confirmação inline.

### 8.4 Agenda (Formulário — Admin e Portal)
- Seleção de data, funcionários disponíveis por `day_of_week`, horários como pills, resumo e reset pós-agendamento.

### 8.5 Portal do Cliente (multi-empresa por slug)
- **Login slug-based**: cliente só entra no slug da própria empresa.
- **Cadastro**: cria usuário + permissão `client` no tenant resolvido pelo slug, em transação.
- **Agendar** / **Meus Agendamentos** com cancelamento.

### 8.6 Relatório de Agendamentos
- Filtros (período/funcionário/serviço), cards de resumo, tabela detalhada, imprimir.

### 8.7 Landing Page / Captura de Leads
- `GET /` → `LandingController@index`. Form `POST /interesse` (throttle 3/10min/IP) salva em `leads` e envia `NewLeadMail`. Falha de e-mail é capturada e logada — não quebra o form.

### 8.8 Multi-tenancy + Super-admin (Fase 1)
- Isolamento por empresa em todos os dados de negócio.
- Separação clara entre **super-admin da plataforma** (flag global) e **admin de salão** (papel por empresa).
- Profissional (`employee`) com acesso ao painel restrito aos próprios dados.

---

## 9. Segurança

### Separação de papéis (Fase 1)
- Super-admin da plataforma (`is_super_admin`) é **global** e separado do admin de salão.
- Gestão de empresas (`/tenant/*`) só para super-admin (middleware `platform` + re-check no Livewire).
- Login admin barra clientes (só entram quem tem `admin`/`employee` em alguma empresa); clientes usam o portal do slug.

### Fluxo de acesso
```
Visitante → GET /                       → Landing page
Visitante → GET /admin                  → Login admin
Visitante → GET /portal/{slug}/login    → Login do portal da empresa

Cliente logado     → GET /dashboard         → 403
Admin de salão     → GET /tenant            → 403 (não é super-admin)
Profissional       → GET /dashboard         → 403 (vai p/ /schedule/admin)
Super-admin        → GET /tenant            → ✓ OK
```

---

## 10. Credenciais de Desenvolvimento

`DatabaseSeeder` roda `TestMatrixSeeder`, que monta uma matriz com **duas empresas**:
**Empresa Padrão** (slug `empresa-padrao`) e **Salão Modelo** (slug `salao-modelo`).
Todas as senhas são `password`.

| Perfil | URL | E-mail | Empresa |
|---|---|---|---|
| Super-admin (plataforma) + admin de salão | `/admin` | `admin@sistema.test` | A |
| Admin de salão (NÃO super-admin) | `/admin` | `gerente@sistema.test` | A |
| Profissional (employee) | `/admin` | `profissional@sistema.test` | A |
| Cliente | `/portal/empresa-padrao/login` | `user1@sistema.test` | A |
| Admin de salão (NÃO super-admin) | `/admin` | `admin-b@sistema.test` | B |
| Cliente | `/portal/salao-modelo/login` | `cliente-b@sistema.test` | B |

Só `admin@sistema.test` tem `is_super_admin = true` (único que chega em `/tenant`).
`gerente@sistema.test` e `admin-b@sistema.test` são admins de salão → 403 em `/tenant`, cada um vê só a própria empresa.

Produção (`ProductionSeeder`, idempotente): `admin@salaofacil.digital` / `admin@2026` (também super-admin).

---

## 11. Layouts e Views

- **Admin**: `layouts/contentNavbarLayout.blade.php` (classes Materio)
- **Portal**: `layouts/portal.blade.php` (Bootstrap 5 via CDN, sem Materio)
- **Blank**: `layouts/blankLayout.blade.php` (páginas de auth)
- Menu lateral: `resources/menu/verticalMenu.json`, compartilhado por `MenuServiceProvider`; itens `"platform": true` só aparecem para super-admin.

---

## 12. Padrões e Convenções

### Badges de cor: Admin vs Portal
O portal não tem as classes Materio (`bg-label-*`). Usar `rgba()` inline no portal; no admin pode usar `bg-label-*`.
```html
<!-- Portal -->  <span style="background:rgba(255,171,0,.15);color:#a07800;">Serviço</span>
<!-- Admin  -->  <span class="badge bg-label-warning">Serviço</span>
```

### Confirmação de ações destrutivas
Nunca usar `confirm()` do browser. Sempre confirmação inline via Livewire (`confirmingDeleteId`, `confirmDelete`, `delete`, `dismissDelete`).

### Isolamento por tenant
```php
$tenantId = session('tenant_id') ?? 1;
DB::table('services')->where('tenant_id', $tenantId)->get();
```

### Padrão Repository
Lógica de negócio atrás de interfaces (`RepositoryServiceProvider`): `UserRepositoryInterface`, `PermissionRepositoryInterface`, `ScheduleRepositoryInterface`. Injetar interfaces, não classes concretas.

---

## 13. Próximas Implementações

### Concluído na Fase 1 ✅
- [x] Multi-tenancy ligado por slug e papel por empresa
- [x] Super-admin da plataforma separado do admin de salão
- [x] Isolamento de usuários/dados por empresa
- [x] Profissional (`employee`) com painel escopado aos próprios dados
- [x] Preço nos serviços + notificação por e-mail ao cliente
- [x] Matriz de credenciais de teste (2 empresas)

### Fase 2 — sugestões de alta prioridade
- [ ] **Seletor de empresa (switcher)** no login/painel admin para quem tem papel em mais de uma empresa (hoje cai na primeira)
- [ ] **Dashboard com métricas reais** (agendamentos hoje/semana, cancelamentos) escopadas por tenant
- [ ] **Testes de isolamento cruzado** (A↔B) nos componentes Livewire

### Média prioridade
- [ ] Paginação em listas/relatórios
- [ ] Perfil do cliente (editar nome/e-mail/senha no portal)
- [ ] Reagendamento (remarcar em vez de cancelar)
- [ ] Duração por serviço para evitar conflito de horário

### Baixa prioridade
- [ ] Exportar relatório para Excel (`maatwebsite/excel`)
- [ ] PWA no portal
- [ ] Área financeira (tabelas `financial_*` já existem, falta UI)

---

## 14. Comandos Úteis

```bash
# Ver rotas do portal
php artisan route:list --path=portal -v

# Limpar caches
php artisan route:clear && php artisan config:clear && php artisan view:clear

# Migrations / seed
php artisan migrate
php artisan db:seed                            # TestMatrixSeeder (dev, 2 empresas)
php artisan db:seed --class=ProductionSeeder   # produção

# Style
./vendor/bin/pint

# Testes
php artisan test
```

---

*Documentação atualizada ao final da Fase 1 (multi-tenancy) — Junho/2026*
