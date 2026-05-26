<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Get admin role ID
        $adminRoleId = DB::table('roles')->where('nama_role', 'admin')->first()->id;

        // Create admin user
        $userId = DB::table('users')->insertGetId([
            'role_id' => $adminRoleId,
            'name' => 'Admin Spekta',
            'email' => 'admin@spekta.com',
            'password' => Hash::make('Admin@123'),
            'is_active' => true,
            'email_verified' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create admin profile
        DB::table('profiles')->insert([
            'user_id' => $userId,
            'nomor_wa' => '081234567890',
            'alamat' => 'Jl. Admin Spekta No. 1',
            'tanggal_lahir' => '1990-01-01',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
