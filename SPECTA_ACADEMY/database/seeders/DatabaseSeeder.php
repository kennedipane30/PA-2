<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\ClassModel;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Role
        $adminRole = Role::create(['nama_role' => 'admin']);
        $guruRole  = Role::create(['nama_role' => 'pengajar']);
        $siswaRole = Role::create(['nama_role' => 'siswa']);

        // 2. Buat User Admin Default
        User::create([
            'name' => 'Admin Spekta',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password123'),
            'role_id' => $adminRole->id
        ]);

        // 3. Buat User Pengajar Default
        User::create([
            'name' => 'Pak Guru Spekta',
            'email' => 'guru@gmail.com',
            'password' => bcrypt('password123'),
            'role_id' => $guruRole->id
        ]);

        // 4. Isi data 4 Program Utama (Gunakan create agar timestamps terisi)
        $programs = [
            ['nama_program' => 'CALON ABDI NEGARA', 'gambar' => 'abdi_negara.png'],
            ['nama_program' => 'PTN & UNHAN', 'gambar' => 'ptn.png'],
            ['nama_program' => 'SMA & SMP REGULER', 'gambar' => 'reguler.png'],
            ['nama_program' => 'SMA FAVORIT', 'gambar' => 'favorit.png'],
        ];

        foreach ($programs as $program) {
            ClassModel::create($program);
        }
    }
}
