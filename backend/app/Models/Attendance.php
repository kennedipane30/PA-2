<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';

    // 1. MODIFIKASI: Beritahu Laravel nama PK sesuai migrasi
    protected $primaryKey = 'attendance_id';

    // 2. MODIFIKASI: Izinkan kolom-kolom ini diisi secara massal (PENTING!)
    protected $fillable = [
        'schedule_id',
        'user_id',
        'status',
        'date'
    ];

    /**
     * Relasi ke Jadwal
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedule_id');
    }

    /**
     * Relasi ke Siswa (User)
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
