<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('tenants', function (Blueprint $table) {
      $table->id();
      $table->string('name'); // Nome da empresa
      $table->string('slug')->unique(); // Identificador amigável (ex: empresa-x)
      $table->string('email')->nullable();
      $table->string('phone')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->index('slug');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('tenants');
  }
};
