<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
public function run(): void
{
    // Buat Role
    $adminRole = \App\Models\Role::create(['nama_role' => 'admin']);
    $guruRole = \App\Models\Role::create(['nama_role' => 'pengajar']);
    $siswaRole = \App\Models\Role::create(['nama_role' => 'siswa']);

    // Buat User Admin Default
    \App\Models\User::create([
        'name' => 'Admin Spekta',
        'email' => 'admin@gmail.com',
        'password' => bcrypt('password123'),
        'role_id' => $adminRole->id
    ]);

    // Buat User Pengajar Default
    \App\Models\User::create([
        'name' => 'Pak Guru Spekta',
        'email' => 'guru@gmail.com',
        'password' => bcrypt('password123'),
        'role_id' => $guruRole->id
    ]);
}
}
