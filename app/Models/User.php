<?php

namespace App\Models;

use App\Models\services\Services;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'is_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_super_admin' => 'boolean',
    ];

    public function services()
    {
        return $this->belongsToMany(
            Services::class,
            'user_services',
            'user_id',
            'service_id'
        );
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'user_permissions',
            'user_id',
            'code_permission'
        );
    }

    /**
     * Verifica se o usuário tem um papel DENTRO de uma empresa (tenant).
     * Se $tenantId for omitido, usa a empresa ativa da sessão.
     * Papéis são por empresa: user_permissions(tenant_id, user_id, code_permission).
     */
    public function hasPermission(string $permissionName, ?int $tenantId = null): bool
    {
        $tenantId ??= session('tenant_id');

        $query = DB::table('user_permissions')
            ->join('permissions', 'permissions.id', '=', 'user_permissions.code_permission')
            ->where('user_permissions.user_id', $this->id)
            ->where('permissions.name', $permissionName);

        if ($tenantId) {
            $query->where('user_permissions.tenant_id', $tenantId);
        }

        return $query->exists();
    }

    /**
     * Papel do usuário numa empresa: 'admin' | 'employee' | 'client' | null.
     */
    public function role(?int $tenantId = null): ?string
    {
        $tenantId ??= session('tenant_id');

        return DB::table('user_permissions')
            ->join('permissions', 'permissions.id', '=', 'user_permissions.code_permission')
            ->where('user_permissions.user_id', $this->id)
            ->when($tenantId, fn ($q) => $q->where('user_permissions.tenant_id', $tenantId))
            ->value('permissions.name');
    }

    /**
     * IDs das empresas em que o usuário tem algum dos papéis informados.
     * Usado no login para descobrir a empresa ativa (antes da sessão existir).
     *
     * @param  array<int,string>  $permissionNames
     * @return array<int,int>
     */
    public function tenantIdsWithAnyRole(array $permissionNames): array
    {
        return DB::table('user_permissions')
            ->join('permissions', 'permissions.id', '=', 'user_permissions.code_permission')
            ->where('user_permissions.user_id', $this->id)
            ->whereIn('permissions.name', $permissionNames)
            ->pluck('user_permissions.tenant_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Super-admin = dono da plataforma (você). Papel GLOBAL, fora do sistema de
     * permissões por empresa. Quem é super-admin gerencia o cadastro de empresas.
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function isAdmin(?int $tenantId = null): bool
    {
        return $this->hasPermission('admin', $tenantId);
    }

    public function isProfessional(?int $tenantId = null): bool
    {
        return $this->hasPermission('employee', $tenantId);
    }

    public function isClient(?int $tenantId = null): bool
    {
        return $this->hasPermission('client', $tenantId);
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class)
            ->withPivot(['role', 'is_active'])
            ->withTimestamps();
    }
}
