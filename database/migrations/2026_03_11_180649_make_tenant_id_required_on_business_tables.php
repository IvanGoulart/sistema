<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  private array $tables = [
    'services',
    'schedules',
    'available_employee_schedules',
    'user_permissions',
    'user_services',
  ];

  public function up(): void
  {
    foreach ($this->tables as $tableName) {
      Schema::table($tableName, function (Blueprint $table) {
        $table->dropForeign(['tenant_id']);
      });

      Schema::table($tableName, function (Blueprint $table) {
        $table->unsignedBigInteger('tenant_id')->nullable(false)->change();
      });

      Schema::table($tableName, function (Blueprint $table) {
        $table->foreign('tenant_id')
          ->references('id')
          ->on('tenants')
          ->restrictOnDelete();
      });
    }
  }

  public function down(): void
  {
    foreach ($this->tables as $tableName) {
      Schema::table($tableName, function (Blueprint $table) {
        $table->dropForeign(['tenant_id']);
      });

      Schema::table($tableName, function (Blueprint $table) {
        $table->unsignedBigInteger('tenant_id')->nullable()->change();
      });

      Schema::table($tableName, function (Blueprint $table) {
        $table->foreign('tenant_id')
          ->references('id')
          ->on('tenants')
          ->nullOnDelete();
      });
    }
  }
};
