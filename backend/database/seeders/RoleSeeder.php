<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nama_role' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'teacher', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'student', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('roles')->insert($roles);
    }
}
