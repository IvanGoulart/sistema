<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Super-admin = dono da plataforma (você). Papel GLOBAL, não escopado por
     * empresa — diferente do papel 'admin' de cada salão (que vive em
     * user_permissions com tenant_id). Default false: ninguém vira super-admin
     * sem ação explícita.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });
    }
};
