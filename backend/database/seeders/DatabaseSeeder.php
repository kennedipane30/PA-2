<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\Category;
use App\Models\CourseClass; // Pastikan model ini mengarah ke tabel 'programs' atau 'classes'
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Role (PERBAIKAN: Ganti 'name' menjadi 'nama_role')
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

        // 4. Buat Kategori (Cek apakah kolomnya 'name' atau 'nama_kategori' di migrasi Anda)
        $kategori = Category::create([
            'name' => 'Program Utama',
            'slug' => 'program-utama'
        ]);

        // 5. Masukkan Data ke Tabel Program Kelas
        // Sesuaikan nama kolom (title, description, price) dengan file migrasi 'programs' Anda
        $programs = [
            [
                'title' => 'CALON ABDI NEGARA',
                'description' => 'Bimbingan Belajar TNI - POLRI - SEKDIN',
                'price' => 900000,
                // 'teachers_id' => $teacherData->teacher_id, // Opsional, sesuaikan kolom di DB
                // 'category_id' => $kategori->id,           // Opsional
            ],
            [
                'title' => 'PTN & UNHAN',
                'description' => 'Persiapan Masuk Kampus Impian',
                'price' => 850000,
            ],
            [
                'title' => 'SMA & SMP REGULER',
                'description' => 'Bimbingan harian untuk siswa SMP dan SMA.',
                'price' => 500000,
            ],
            [
                'title' => 'SMA FAVORIT',
                'description' => 'Persiapan masuk SMA Unggulan dan Favorit.',
                'price' => 600000,
            ]
        ];

        foreach ($programs as $program) {
            // Jika Anda mengganti nama tabel menjadi 'programs', pastikan model CourseClass
            // memiliki properti: protected $table = 'programs';
            CourseClass::create($program);
        }

        echo "Seeding Berhasil! Semua data awal termasuk 4 Program Kelas sudah masuk.\n";
    }
}