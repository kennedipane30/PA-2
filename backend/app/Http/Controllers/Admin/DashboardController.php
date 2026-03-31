<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Classes;
use App\Models\Payment;
use App\Models\Enrollment;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::whereHas('role', fn($q) => $q->where('nama_role', 'student'))->count(),
            'total_teachers' => User::whereHas('role', fn($q) => $q->where('nama_role', 'teacher'))->count(),
            'total_classes' => Classes::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'total_revenue' => Payment::where('status', 'verified')->sum('total'),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
