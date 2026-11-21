<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::orderBy('start_time', 'desc')->get();
        $selectedEventId = $request->input('event_id', $events->first()?->id);

        $selectedEvent = null;
        $attendances = [];
        $stats = [
            'total' => 0,
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0,
        ];

        if ($selectedEventId) {
            $selectedEvent = Event::find($selectedEventId);

            if ($selectedEvent) {
                if (!$selectedEvent->qr_token) {
                    $selectedEvent->update(['qr_token' => Str::random(32)]);
                }

                $query = Attendance::with('user')
                    ->where('event_id', $selectedEvent->id)
                    ->orderBy('status', 'asc');

                // Anggota hanya bisa lihat presensi dirinya sendiri
                if (auth()->user()->role === 'anggota') {
                    $query->where('user_id', auth()->id());
                }

                $attendances = $query->get();

                $stats['total'] = $attendances->count();
                $stats['hadir'] = $attendances->where('status', 'Hadir')->count();
                $stats['izin'] = $attendances->where('status', 'Izin')->count();
                $stats['sakit'] = $attendances->where('status', 'Sakit')->count();
                $stats['alpha'] = $attendances->where('status', 'Alpha')->count();
            }
        }

        // Anggota hanya bisa lihat data dirinya sendiri untuk input manual
        $members = [];
        if (in_array(auth()->user()->role, ['ketua_umum', 'pengurus'])) {
            $members = User::where('status', 'Aktif')->select('id', 'name', 'nim', 'division')->get();
        }

        return Inertia::render('Attendances/Index', [
            'events' => $events,
            'selectedEvent' => $selectedEvent,
            'attendances' => $attendances,
            'stats' => $stats,
            'members' => $members,
            'userRole' => auth()->user()->role,
        ]);
    }

    public function generateToken(Event $event)
    {
        $event->update(['qr_token' => Str::random(32)]);

        return redirect()->back()->with('success', 'QR Token berhasil diperbarui.');
    }

    public function populate(Event $event)
    {
        $activeMembers = User::where('status', 'Aktif')->get();

        foreach ($activeMembers as $member) {
            Attendance::firstOrCreate(
                ['event_id' => $event->id, 'user_id' => $member->id],
                ['status' => 'Alpha']
            );
        }

        return redirect()->back()->with('success', 'Daftar presensi anggota berhasil diinisialisasi.');
    }

    public function eventAttendances(Event $event)
    {
        // Pastikan qr_token selalu ada untuk event ini
        if (!$event->qr_token) {
            $event->update(['qr_token' => Str::random(32)]);
        }

        $query = Attendance::with('user')
            ->where('event_id', $event->id)
            ->orderBy('status', 'asc');

        // Anggota hanya bisa lihat presensi dirinya sendiri
        if (auth()->user()->role === 'anggota') {
            $query->where('user_id', auth()->id());
        }

        $attendances = $query->get();

        $stats = [
            'total' => $attendances->count(),
            'hadir' => $attendances->where('status', 'Hadir')->count(),
            'izin' => $attendances->where('status', 'Izin')->count(),
            'sakit' => $attendances->where('status', 'Sakit')->count(),
            'terlambat' => $attendances->where('status', 'Terlambat')->count(),
            'alpha' => $attendances->where('status', 'Alpha')->count(),
        ];

        return response()->json([
            'event' => $event,
            'attendances' => $attendances,
            'stats' => $stats,
            'userRole' => auth()->user()->role,
        ]);
    }

    /**
     * Tandai otomatis anggota yang belum absen sebagai Alpha setelah waktu latihan berakhir.
     */
    public function markAutoAlpha(Event $event)
    {
        if ($event->end_time > now()) {
            return response()->json([
                'message' => 'Waktu latihan belum berakhir. Auto Alpha belum dapat dijalankan.'
            ], 400);
        }

        $attendedUserIds = Attendance::where('event_id', $event->id)
            ->pluck('user_id');

        // Ambil semua anggota aktif yang belum absen pada event ini
        $activeMembers = User::where('status', 'Aktif')
            ->whereNotIn('id', $attendedUserIds)
            ->get();

        foreach ($activeMembers as $member) {
            Attendance::create([
                'event_id' => $event->id,
                'user_id' => $member->id,
                'status'   => 'Alpha',
                'scan_time'=> null,
                'keterangan' => 'Auto Alpha (belum absen setelah waktu berakhir)',
            ]);
        }

        // Refresh data
        $attendances = Attendance::with('user')
            ->where('event_id', $event->id)
            ->orderBy('status', 'asc')
            ->get();

        $stats = [
            'total' => $attendances->count(),
            'hadir' => $attendances->where('status', 'Hadir')->count(),
            'izin'  => $attendances->where('status', 'Izin')->count(),
            'sakit' => $attendances->where('status', 'Sakit')->count(),
            'terlambat' => $attendances->where('status', 'Terlambat')->count(),
            'alpha' => $attendances->where('status', 'Alpha')->count(),
        ];

        return response()->json([
            'message' => 'Auto Alpha berhasil dilakukan. ' . count($activeMembers) . ' anggota ditandai Alpha.',
            'attendances' => $attendances,
            'stats' => $stats,
        ]);
    }

    /**
     * Presensi sendiri dari halaman detail event (self-attendance).
     */
    public function selfAttend(Request $request, Event $event)
    {
        // Cek apakah event sudah kadaluarsa
        if ($event->expired_at && now() > $event->expired_at) {
            return response()->json([
                'message' => 'Event sudah kadaluarsa. Tidak dapat melakukan presensi lagi.'
            ], 403);
        }

        // Pastikan qr_token ada
        if (!$event->qr_token) {
            $event->update(['qr_token' => Str::random(32)]);
        }

        $validated = $request->validate([
            'status'      => 'required|in:Hadir,Izin,Terlambat',
            'qr_token'    => 'nullable|string',
            'keterangan'  => 'nullable|string|max:255',
        ]);

        $user = auth()->user();

        // Cek apakah sudah absen sebelumnya
        $attendance = Attendance::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        $data = [
            'status'    => $validated['status'],
            'scan_time' => null,
            'keterangan'=> $validated['keterangan'] ?? null,
        ];

        if ($validated['status'] === 'Hadir') {
            // Validasi QR token untuk Hadir
            if (empty($validated['qr_token'])) {
                return response()->json([
                    'message' => 'QR Token wajib diisi untuk status Hadir.'
                ], 422);
            }

            if ($validated['qr_token'] !== $event->qr_token) {
                return response()->json([
                    'message' => 'QR Token tidak valid atau tidak cocok dengan event ini.'
                ], 422);
            }

            $data['scan_time'] = now();
        }

        if ($attendance) {
            $attendance->update($data);
        } else {
            $data['event_id'] = $event->id;
            $data['user_id']  = $user->id;
            $attendance = Attendance::create($data);
        }

        return response()->json([
            'message' => 'Presensi berhasil dicatat sebagai ' . $validated['status'] . '.',
            'attendance' => $attendance->load('user'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'user_id'  => 'required|exists:users,id',
            'status'   => 'required|in:Hadir,Izin,Sakit,Alpha',
        ]);

        Attendance::updateOrCreate(
            ['event_id' => $validated['event_id'], 'user_id' => $validated['user_id']],
            [
                'status'    => $validated['status'],
                'scan_time' => $validated['status'] === 'Hadir' ? now() : null,
            ]
        );

        return redirect()->back()->with('success', 'Presensi berhasil dicatat.');
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'qr_token' => 'required|string',
        ]);

        $event = Event::where('qr_token', $validated['qr_token'])->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code presensi tidak valid atau sudah kedaluwarsa.'
            ], 422);
        }

        Attendance::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => auth()->id()],
            [
                'status'    => 'Hadir',
                'scan_time' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Presensi Anda berhasil dicatat sebagai Hadir!',
            'event' => $event->title,
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'status' => 'required|in:Hadir,Izin,Sakit,Alpha',
        ]);

        $attendance->update([
            'status'    => $validated['status'],
            'scan_time' => $validated['status'] === 'Hadir' ? ($attendance->scan_time ?? now()) : null,
        ]);

        return redirect()->back()->with('success', 'Status presensi berhasil diubah.');
    }
}
