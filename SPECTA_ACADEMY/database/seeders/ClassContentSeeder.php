<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material; // IMPORT INI AGAR TIDAK MERAH
use App\Models\Tryout;   // IMPORT INI AGAR TIDAK MERAH
use Illuminate\Support\Facades\DB;

class ClassContentSeeder extends Seeder
{
    public function run(): void
    {
        // Data sesuai catatan tangan kamu
        $data = [
            1 => ['TIU', 'Psikotes', 'Bahasa Inggris', 'Matematika', 'TWK', 'Tryout'], // Abdi Negara
            2 => ['TIU', 'Psikotes', 'Matematika', 'TWK', 'Tryout'], // PTN
            3 => ['Matematika', 'Bahasa Inggris', 'Fisika', 'Biologi', 'Kimia', 'Tryout'], // Reguler
            4 => ['Matematika', 'Bahasa Inggris', 'Kimia', 'Biologi', 'Fisika', 'Psikotes', 'Tryout'], // Favorit
        ];

        foreach ($data as $classId => $subjects) {
            foreach ($subjects as $s) {
                // Logika: Jika tulisannya 'Tryout', masukkan ke tabel tryouts
                if ($s == 'Tryout') {
                    Tryout::create([
                        'class_id' => $classId,
                        'title'    => 'Simulasi Tryout ' . $this->getProgramName($classId)
                    ]);
                } else {
                    // Selain itu, masukkan ke tabel materials
                    Material::create([
                        'class_id' => $classId,
                        'title'    => 'Materi ' . $s
                    ]);
                }
            }
        }
    }

    // Fungsi pembantu untuk nama tryout
    private function getProgramName($id) {
        $names = [1 => 'Abdi Negara', 2 => 'PTN', 3 => 'Reguler', 4 => 'Favorit'];
        return $names[$id];
    }
}
