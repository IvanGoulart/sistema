<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('user_permissions', function (Blueprint $table) {
      $table->unique(['tenant_id', 'user_id', 'code_permission'], 'user_permissions_unique');
    });
  }

  public function down(): void
  {
    Schema::table('user_permissions', function (Blueprint $table) {
      $table->dropUnique('user_permissions_unique');
    });
  }
};
