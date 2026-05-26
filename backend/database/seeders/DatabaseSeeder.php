<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\Category;
use App\Models\Program; // GANTI DISINI (Hapus CourseClass)
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Role
        $adminRole = Role::create(['nama_role' => 'admin']);
        $guruRole  = Role::create(['nama_role' => 'pengajar']);
        $siswaRole = Role::create(['nama_role' => 'siswa']);

        // 2. Buat User Admin
        User::create([
            'name' => 'Admin Spekta',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->role_id,
            'phone' => '08123456789',
            'is_verified' => 1,
        ]);

        // 3. Buat User Pengajar
        $userGuru = User::create([
            'name' => 'Pak Guru Spekta',
            'email' => 'guru@gmail.com',
            'password' => Hash::make('password123'),
            'role_id' => $guruRole->role_id,
            'phone' => '08123456788',
            'is_verified' => 1,
        ]);

        $teacherData = Teacher::create([
            'user_id' => $userGuru->user_id,
            'specialization' => 'TPA & Kesamaptaan'
        ]);

        // 4. Buat Kategori
        $kategori = Category::create([
            'name' => 'Program Utama',
            'slug' => 'program-utama'
        ]);

        // 5. Masukkan Data ke Tabel Program
        $programs = [
            [
                'title' => 'CALON ABDI NEGARA',
                'description' => 'Bimbingan Belajar TNI - POLRI - SEKDIN',
                'price' => 900000,
                'teachers_id' => $teacherData->teacher_id,
                'category_id' => $kategori->category_id,
            ],
            [
                'title' => 'PTN & UNHAN',
                'description' => 'Persiapan Masuk Kampus Impian',
                'price' => 850000,
                'teachers_id' => $teacherData->teacher_id,
                'category_id' => $kategori->category_id,
            ],
            [
                'title' => 'SMA & SMP REGULER',
                'description' => 'Bimbingan harian untuk siswa SMP dan SMA.',
                'price' => 500000,
                'teachers_id' => $teacherData->teacher_id,
                'category_id' => $kategori->category_id,
            ],
            [
                'title' => 'SMA FAVORIT',
                'description' => 'Persiapan masuk SMA Unggulan dan Favorit.',
                'price' => 600000,
                'teachers_id' => $teacherData->teacher_id,
                'category_id' => $kategori->category_id,
            ]
        ];

        foreach ($programs as $p) {
            // GUNAKAN MODEL Program AGAR MEMBACA program_id
            Program::create($p);
        }

        echo "Seeding Berhasil! Semua data sudah masuk.\n";
    }
}