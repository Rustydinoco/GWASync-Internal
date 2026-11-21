<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SessionController extends Controller
{
    public function index()
    {
        return Inertia::render('Attendance/Sessions/Index', [
            'sessions' => AttendanceSession::with('event')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'schedule_id' => 'nullable|exists:activity_schedules,id',
            'name' => 'required|string|max:150',
            'open_at' => 'required|date',
            'close_at' => 'required|date',
            'late_tolerance_minute' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
            'qr_token' => 'nullable|string|max:100|unique:attendance_sessions,qr_token',
            'latitude' => 'nullable|decimal',
            'longitude' => 'nullable|decimal',
            'radius_meter' => 'nullable|integer',
            'status' => 'required|in:Aktif,Ditutup,Dibatalkan',
            'cancel_reason' => 'nullable|string|max:255',
        ]);
        AttendanceSession::create($validated);
        return redirect()->back()->with('success', 'Sesi presensi berhasil ditambahkan.');
    }

    public function update(Request $request, AttendanceSession $session)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'schedule_id' => 'nullable|exists:activity_schedules,id',
            'name' => 'required|string|max:150',
            'open_at' => 'required|date',
            'close_at' => 'required|date',
            'late_tolerance_minute' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
            'qr_token' => 'nullable|string|max:100|unique:attendance_sessions,qr_token,' . $session->id,
            'latitude' => 'nullable|decimal',
            'longitude' => 'nullable|decimal',
            'radius_meter' => 'nullable|integer',
            'status' => 'required|in:Aktif,Ditutup,Dibatalkan',
            'cancel_reason' => 'nullable|string|max:255',
        ]);
        $session->update($validated);
        return redirect()->back()->with('success', 'Sesi presensi berhasil diperbarui.');
    }

    public function destroy(AttendanceSession $session)
    {
        $session->delete();
        return redirect()->back()->with('success', 'Sesi presensi berhasil dihapus.');
    }
}
