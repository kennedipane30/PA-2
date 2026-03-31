<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\Category;
use App\Models\CourseClass; // <--- GUNAKAN INI
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Role (Sesuai Migration 0000)
        $adminRole = Role::create(['name' => 'admin']);
        $guruRole  = Role::create(['name' => 'pengajar']);
        $siswaRole = Role::create(['name' => 'siswa']);

        // 2. Buat User Admin
        User::create([
            'name' => 'Admin Spekta',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->role_id,
            'phone' => '08123456789',
            'is_verified' => 1,
        ]);

        // 3. Buat User Pengajar & Data Teacher
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

        // 5. Masukkan Data ke Tabel 'classes' menggunakan Model 'CourseClass'
        $programs = [
            [
                'title' => 'CALON ABDI NEGARA',
                'description' => 'Bimbingan Belajar TNI - POLRI - SEKDIN',
                'price' => 900000,
                'teachers_id' => $teacherData->teacher_id,
                'category_id' => $kategori->id,
                'start_date' => '2026-04-01',
                'end_date' => '2026-12-31',
            ],
            [
                'title' => 'PTN & UNHAN',
                'description' => 'Persiapan Masuk Kampus Impian',
                'price' => 850000,
                'teachers_id' => $teacherData->teacher_id,
                'category_id' => $kategori->id,
                'start_date' => '2026-04-01',
                'end_date' => '2026-12-31',
            ]
        ];

        foreach ($programs as $program) {
            CourseClass::create($program);
        }

        echo "Seeding Berhasil! Tabel 'classes' sudah terisi sesuai CDM.\n";
    }
}