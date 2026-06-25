<?php

use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\icons\MdiIcons;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\Portal\AuthController;
use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\schedule\ScheduleController;
use App\Http\Controllers\services\ServicesController;
use App\Http\Controllers\tables\Basic as TablesBasic;
use App\Http\Controllers\Tenant\TenantController;
use App\Http\Controllers\user\UserController;
use App\Http\Controllers\user_interface\Accordion;
use App\Http\Controllers\user_interface\Alerts;
use App\Http\Controllers\user_interface\Badges;
use App\Http\Controllers\user_interface\Buttons;
use App\Http\Controllers\user_interface\Carousel;
use App\Http\Controllers\user_interface\Collapse;
use App\Http\Controllers\user_interface\Dropdowns;
use App\Http\Controllers\user_interface\Footer;
use App\Http\Controllers\user_interface\ListGroups;
use App\Http\Controllers\user_interface\Modals;
use App\Http\Controllers\user_interface\Navbar;
use App\Http\Controllers\user_interface\Offcanvas;
use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\user_interface\Progress;
use App\Http\Controllers\user_interface\Spinners;
use App\Http\Controllers\user_interface\TabsPills;
use App\Http\Controllers\user_interface\Toasts;
use App\Http\Controllers\user_interface\TooltipsPopovers;
use App\Http\Controllers\user_interface\Typography;
use Illuminate\Support\Facades\Route;

// ─── Landing page ─────────────────────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/interesse', [LandingController::class, 'store'])->name('landing.store')->middleware('throttle:3,10');

// ─── Rotas públicas (sem autenticação) ───────────────────────────────────────
Route::get('/admin', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::post('/auth/check-authenticate', [LoginBasic::class, 'checkAuthenticate'])->name('auth-check-authenticate');
Route::get('/logout', [LoginBasic::class, 'logout'])->name('auth.logout');
Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');
Route::redirect('/home', '/dashboard');

// ─── Portal do cliente (multi-empresa por slug: /portal/{tenantSlug}/...) ──────
Route::prefix('portal/{tenantSlug}')->middleware('tenant')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('portal.login');
        Route::post('/login', [AuthController::class, 'login'])->name('portal.login.post');
        Route::get('/cadastro', [AuthController::class, 'showRegister'])->name('portal.cadastro');
        Route::post('/register', [AuthController::class, 'register'])->name('portal.register');
    });

    Route::middleware(['auth', 'permission:client'])->group(function () {
        Route::get('/', [PortalController::class, 'home'])->name('portal.home');
        Route::get('/agendar', [PortalController::class, 'agendar'])->name('portal.agendar');
        Route::get('/meus-agendamentos', [PortalController::class, 'meusAgendamentos'])->name('portal.meus-agendamentos');
        Route::patch('/cancelar/{id}', [PortalController::class, 'cancelar'])->name('portal.cancelar');
        Route::post('/logout', [AuthController::class, 'logout'])->name('portal.logout');
    });
});

// ─── Painel administrativo (auth + permission:admin) ─────────────────────────
Route::middleware(['auth', 'permission:admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [Analytics::class, 'index'])->name('dashboard-analytics');

    // Usuários
    Route::get('/users/list', [UserController::class, 'index'])->name('users-list');
    Route::get('/user/delete/{id}', [UserController::class, 'destroy'])->name('user-delete');
    Route::get('/user/active/{id}', [UserController::class, 'active'])->name('user-active');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user-edit');
    Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user-update');
    Route::post('/user/create', [UserController::class, 'store'])->name('user-create');
    Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');

    // Agenda
    Route::get('/schedule/create', [ScheduleController::class, 'create'])->name('schedule-create');
    Route::get('/schedule/availability', [ScheduleController::class, 'availability'])->name('schedule.availability');
    Route::get('/schedule/admin', [ScheduleController::class, 'adminAgenda'])->name('schedule.admin');

    // Serviços
    Route::get('/services', [ServicesController::class, 'index'])->name('services.index');

    // Relatórios
    Route::get('/reports', fn () => view('content.reports.index'))->name('reports.index');

    // Empresas (tenants)
    Route::prefix('tenant')->group(function () {
        Route::get('/', [TenantController::class, 'index'])->name('tenants.index');
        Route::get('/create', [TenantController::class, 'create'])->name('tenants.create');
        Route::post('/', [TenantController::class, 'store'])->name('tenants.store');
        Route::get('/{id}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/{id}', [TenantController::class, 'update'])->name('tenants.update');
        Route::delete('/{id}', [TenantController::class, 'destroy'])->name('tenants.destroy');
    });

    // Páginas do template (mantidas atrás de auth admin)
    Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name('pages-account-settings-account');
    Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name('pages-account-settings-notifications');
    Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name('pages-account-settings-connections');
    Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
    Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name('pages-misc-under-maintenance');

    Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
    Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
    Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
    Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
    Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

    Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');

    Route::get('/ui/accordion', [Accordion::class, 'index'])->name('ui-accordion');
    Route::get('/ui/alerts', [Alerts::class, 'index'])->name('ui-alerts');
    Route::get('/ui/badges', [Badges::class, 'index'])->name('ui-badges');
    Route::get('/ui/buttons', [Buttons::class, 'index'])->name('ui-buttons');
    Route::get('/ui/carousel', [Carousel::class, 'index'])->name('ui-carousel');
    Route::get('/ui/collapse', [Collapse::class, 'index'])->name('ui-collapse');
    Route::get('/ui/dropdowns', [Dropdowns::class, 'index'])->name('ui-dropdowns');
    Route::get('/ui/footer', [Footer::class, 'index'])->name('ui-footer');
    Route::get('/ui/list-groups', [ListGroups::class, 'index'])->name('ui-list-groups');
    Route::get('/ui/modals', [Modals::class, 'index'])->name('ui-modals');
    Route::get('/ui/navbar', [Navbar::class, 'index'])->name('ui-navbar');
    Route::get('/ui/offcanvas', [Offcanvas::class, 'index'])->name('ui-offcanvas');
    Route::get('/ui/pagination-breadcrumbs', [PaginationBreadcrumbs::class, 'index'])->name('ui-pagination-breadcrumbs');
    Route::get('/ui/progress', [Progress::class, 'index'])->name('ui-progress');
    Route::get('/ui/spinners', [Spinners::class, 'index'])->name('ui-spinners');
    Route::get('/ui/tabs-pills', [TabsPills::class, 'index'])->name('ui-tabs-pills');
    Route::get('/ui/toasts', [Toasts::class, 'index'])->name('ui-toasts');
    Route::get('/ui/tooltips-popovers', [TooltipsPopovers::class, 'index'])->name('ui-tooltips-popovers');
    Route::get('/ui/typography', [Typography::class, 'index'])->name('ui-typography');

    Route::get('/extended/ui-perfect-scrollbar', [PerfectScrollbar::class, 'index'])->name('extended-ui-perfect-scrollbar');
    Route::get('/extended/ui-text-divider', [TextDivider::class, 'index'])->name('extended-ui-text-divider');

    Route::get('/icons/icons-mdi', [MdiIcons::class, 'index'])->name('icons-mdi');

    Route::get('/forms/basic-inputs', [BasicInput::class, 'index'])->name('forms-basic-inputs');
    Route::get('/forms/input-groups', [InputGroups::class, 'index'])->name('forms-input-groups');

    Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
    Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');

    Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');
});
