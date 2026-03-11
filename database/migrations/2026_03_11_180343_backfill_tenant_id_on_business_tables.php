<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
public function up(): void
{
$tenantId = DB::table('tenants')->where('slug', 'empresa-padrao')->value('id');

if (! $tenantId) {
$tenantId = DB::table('tenants')->insertGetId([
'name' => 'Empresa Padrão',
'slug' => 'empresa-padrao',
'email' => null,
'phone' => null,
'is_active' => true,
'created_at' => now(),
'updated_at' => now(),
]);
}

DB::table('services')
->whereNull('tenant_id')
->update(['tenant_id' => $tenantId]);

DB::table('schedules')
->whereNull('tenant_id')
->update(['tenant_id' => $tenantId]);

DB::table('available_employee_schedules')
->whereNull('tenant_id')
->update(['tenant_id' => $tenantId]);

DB::table('user_permissions')
->whereNull('tenant_id')
->update(['tenant_id' => $tenantId]);

DB::table('user_services')
->whereNull('tenant_id')
->update(['tenant_id' => $tenantId]);
}

public function down(): void
{
DB::table('services')
->whereNotNull('tenant_id')
->update(['tenant_id' => null]);

DB::table('schedules')
->whereNotNull('tenant_id')
->update(['tenant_id' => null]);

DB::table('available_employee_schedules')
->whereNotNull('tenant_id')
->update(['tenant_id' => null]);

DB::table('user_permissions')
->whereNotNull('tenant_id')
->update(['tenant_id' => null]);

DB::table('user_services')
->whereNotNull('tenant_id')
->update(['tenant_id' => null]);
}
};
