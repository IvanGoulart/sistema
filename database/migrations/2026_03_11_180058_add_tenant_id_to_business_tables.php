<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
Schema::table('services', function (Blueprint $table) {
$table->foreignId('tenant_id')
->nullable()
->after('id')
->constrained()
->nullOnDelete();

$table->index('tenant_id');
});

Schema::table('schedules', function (Blueprint $table) {
$table->foreignId('tenant_id')
->nullable()
->after('id')
->constrained()
->nullOnDelete();

$table->index('tenant_id');
});

Schema::table('available_employee_schedules', function (Blueprint $table) {
$table->foreignId('tenant_id')
->nullable()
->after('id')
->constrained()
->nullOnDelete();

$table->index('tenant_id');
});

Schema::table('user_permissions', function (Blueprint $table) {
$table->foreignId('tenant_id')
->nullable()
->after('id')
->constrained()
->nullOnDelete();

$table->index('tenant_id');
});

Schema::table('user_services', function (Blueprint $table) {
$table->foreignId('tenant_id')
->nullable()
->after('id')
->constrained()
->nullOnDelete();

$table->index('tenant_id');
});
}

public function down(): void
{
Schema::table('user_services', function (Blueprint $table) {
$table->dropConstrainedForeignId('tenant_id');
});

Schema::table('user_permissions', function (Blueprint $table) {
$table->dropConstrainedForeignId('tenant_id');
});

Schema::table('available_employee_schedules', function (Blueprint $table) {
$table->dropConstrainedForeignId('tenant_id');
});

Schema::table('schedules', function (Blueprint $table) {
$table->dropConstrainedForeignId('tenant_id');
});

Schema::table('services', function (Blueprint $table) {
$table->dropConstrainedForeignId('tenant_id');
});
}
};
