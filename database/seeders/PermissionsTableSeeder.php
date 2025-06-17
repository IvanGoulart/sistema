<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'Admin'],
            ['name' => 'Profissional']
        ];

        foreach ($permissions as &$permission) {
            $permission['created_at'] = Carbon::now();
            $permission['updated_at'] = Carbon::now();
        }

        DB::table('permissions')->insert($permissions);
    }
}
