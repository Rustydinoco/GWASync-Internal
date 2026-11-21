<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class KtaController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $selectedUserId = $request->input('user_id', $currentUser->id);

        // Non-management tidak boleh melihat KTA anggota lain
        if (!in_array($currentUser->role, ['ketua_umum', 'pengurus']) && $selectedUserId !== $currentUser->id) {
            $selectedUserId = $currentUser->id;
        }

        $member = User::find($selectedUserId) ?? $currentUser;

        // Pastikan KTA QR Code data terisi
        if (!$member->kta_qr_code) {
            $qrData = 'GWASYNC-KTA:' . ($member->nia ?? $member->id) . ':' . $member->email;
            $member->update(['kta_qr_code' => $qrData]);
        }

        $allMembers = [];
        if (in_array($currentUser->role, ['ketua_umum', 'pengurus'])) {
            $allMembers = User::select('id', 'name', 'nia', 'nim', 'division', 'role', 'status')
                ->orderBy('name', 'asc')
                ->get();
        }

        return Inertia::render('Kta/Index', [
            'member' => $member,
            'allMembers' => $allMembers,
            'isManagement' => in_array($currentUser->role, ['ketua_umum', 'pengurus']),
            'userRole' => $currentUser->role,
        ]);
    }
}
