<?php

namespace Database\Seeders;

<<<<<<< HEAD
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
=======
use Illuminate\Database\Seeder;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class ClassContentSeeder extends Seeder
{
    public function run(): void
    {
        // Data Materi Sesuai Catatan Tangan
        $data = [
            1 => ['TIU', 'Psikotes', 'Bahasa Inggris', 'Matematika', 'TWK'],
            2 => ['TIU', 'Psikotes', 'Matematika', 'TWK'],
            3 => ['Matematika', 'Bahasa Inggris', 'Fisika', 'Biologi', 'Kimia'],
            4 => ['Matematika', 'Bahasa Inggris', 'Kimia', 'Biologi', 'Fisika', 'Psikotes'],
        ];

        foreach ($data as $classId => $subjects) {
            foreach ($subjects as $s) {
                Material::create([
                    'class_id' => $classId,
                    'title'    => 'Materi ' . $s
                ]);
            }
        }
>>>>>>> 2d98343976e249d669e06417b91ac32bd818ca11
    }
}
