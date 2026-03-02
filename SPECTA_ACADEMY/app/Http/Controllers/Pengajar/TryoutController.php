<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Tryout;
use App\Models\Question;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // WAJIB ADA AGAR DB TIDAK MERAH
use Illuminate\Support\Facades\Validator;

class TryoutController extends Controller
{
    /**
     * Menampilkan form buat soal
     */
    public function buatSoal(Request $request)
    {
        $classId = $request->query('class_id');
        $class = ClassModel::findOrFail($classId);
        return view('pengajar.tryout.create', compact('class'));
    }

    /**
     * Memproses Import Soal dari CSV
     */
    public function importSoal(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'title'    => 'required|string|max:255',
            'file_csv' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $tryout = Tryout::create([
                'class_id' => $request->class_id,
                'title'    => $request->title,
                'duration' => $request->duration ?? 60,
            ]);

            $file = fopen($request->file('file_csv')->getRealPath(), 'r');
            fgetcsv($file); // Melewati baris header (No, Pertanyaan, dll)

            $count = 0;
            // PERBAIKAN: Gunakan ";" sebagai separator karena Excel Indonesia pakai titik koma
            while (($row = fgetcsv($file, 2000, ";")) !== FALSE) {

                // Cek apakah kolomnya lengkap (Minimal ada Pertanyaan sampai Kunci)
                if (count($row) < 7) {
                    // Jika gagal pakai ";", kita coba pakai "," (cadangan)
                    $row = str_getcsv($row[0], ",");
                    if (count($row) < 7) continue; // Lewati jika baris kosong atau rusak
                }

                Question::create([
                    'tryout_id'      => $tryout->tryoutsID,
                    'question'       => $row[1], // Kolom B (Pertanyaan)
                    'option_a'       => $row[2], // Kolom C
                    'option_b'       => $row[3], // Kolom D
                    'option_c'       => $row[4], // Kolom E
                    'option_d'       => $row[5], // Kolom F
                    'correct_answer' => trim(strtoupper($row[6])), // Kolom G (Kunci A/B/C/D)
                    'explanation'    => $row[7] ?? null, // Kolom H (Pembahasan)
                ]);
                $count++;
            }
            fclose($file);

            DB::commit();
            return redirect()->back()->with('success', "Berhasil! Tryout diterbitkan dengan $count soal.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
};
