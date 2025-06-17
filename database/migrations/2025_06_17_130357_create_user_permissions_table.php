<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id(); // Cria a chave primária 'id'
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Chave estrangeira para 'users'
            $table->foreignId('code_permission')->constrained('permissions')->onDelete('cascade'); // Chave estrangeira para 'permissions'
            $table->timestamps(); // Cria os campos 'created_at' e 'updated_at'
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
}
