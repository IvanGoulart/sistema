<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('user_services', function (Blueprint $table) {
      $table->unique(['tenant_id', 'user_id', 'service_id'], 'user_services_unique');
    });
  }

  public function down(): void
  {
    Schema::table('user_services', function (Blueprint $table) {
      $table->dropUnique('user_services_unique');
    });
  }
};
