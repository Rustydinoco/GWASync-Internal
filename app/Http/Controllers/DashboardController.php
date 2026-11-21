<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Borrowing;
use App\Models\Event;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\FundingRequest;
use App\Models\Inventory;
use App\Models\Letter;
use App\Models\Program;
use App\Models\Recruitment;
use App\Models\RecruitmentApplicant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role;
        $isManagement = in_array($userRole, ['ketua_umum', 'pengurus']);

        // Time period selector: harian, mingguan, bulanan, tahunan
        $period = $request->input('period', 'bulanan');

        // Base queries based on role (never mutate directly; always clone)
        $memberQuery = User::where('role', 'anggota');
        $attendanceQuery = Attendance::query();
        $borrowingQuery = Borrowing::query();

        if (!$isManagement) {
            $attendanceQuery->where('user_id', $user->id);
            $borrowingQuery->where('user_id', $user->id);
        }

        // Core operational stats
        $totalMembers = (clone $memberQuery)->count();
        $activeMembers = (clone $memberQuery)->where('status', 'Aktif')->count();

        $totalEvents = Event::count();
        $upcomingEvents = Event::where('start_time', '>', now())->count();

        $totalBorrowings = (clone $borrowingQuery)->count();
        $activeBorrowings = Inventory::where('status', 'Dipinjam')->count();
        $pendingBorrowings = $isManagement
            ? Borrowing::where('status', 'Pending')->count()
            : (clone $borrowingQuery)->where('status', 'Pending')->count();

        $damagedInventories = Inventory::whereIn('condition', ['Rusak Ringan', 'Rusak Berat'])->count();

        // Attendance stats for current period
        $thisPeriodAttendance = (clone $attendanceQuery)
            ->where('status', 'Hadir')
            ->when($period === 'harian', fn ($q) => $q->whereDate('scan_time', today()))
            ->when($period === 'mingguan', fn ($q) => $q->whereBetween('scan_time', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($period === 'bulanan', fn ($q) => $q->whereMonth('scan_time', now()->month)->whereYear('scan_time', now()->year))
            ->when($period === 'tahunan', fn ($q) => $q->whereYear('scan_time', now()->year))
            ->count();

        $attendanceStats = [
            'hadir' => (clone $attendanceQuery)->where('status', 'Hadir')->count(),
            'izin' => (clone $attendanceQuery)->where('status', 'Izin')->count(),
            'sakit' => (clone $attendanceQuery)->where('status', 'Sakit')->count(),
            'alpha' => (clone $attendanceQuery)->where('status', 'Alpha')->count(),
        ];

        // Module summary cards (management only)
        $moduleSummary = null;
        if ($isManagement) {
            $openingBalance = (float) FinancialAccount::where('is_active', true)->sum('opening_balance');
            $approvedIncome = (float) FinancialTransaction::where('status', 'Disetujui')
                ->where('transaction_type', 'Pemasukan')
                ->sum('amount');
            $approvedExpense = (float) FinancialTransaction::where('status', 'Disetujui')
                ->where('transaction_type', 'Pengeluaran')
                ->sum('amount');

            $moduleSummary = [
                'financialBalance' => $openingBalance + $approvedIncome - $approvedExpense,
                'pendingFundingRequests' => FundingRequest::whereIn('status', ['Draft', 'Diajuan', 'Diajukan', 'Pending'])->count(),
                'totalLetters' => Letter::count(),
                'pendingLetters' => Letter::whereIn('status', ['Draft', 'Diajuan', 'Diajukan', 'Pending'])->count(),
                'totalPrograms' => Program::count(),
                'activePrograms' => Program::whereIn('status', ['Disetujui', 'Diajuan'])->count(),
                'openRecruitments' => Recruitment::where('status', 'Buka')->count(),
                'pendingApplicants' => RecruitmentApplicant::whereIn('status', ['Diajukan', 'Direview'])->count(),
            ];
        }

        // Pending borrowings for management
        $pendingBorrowingsList = $isManagement
            ? Borrowing::with('user', 'inventory')
                ->where('status', 'Pending')
                ->latest()
                ->limit(5)
                ->get()
            : [];

        // Recent activities
        $recentBorrowings = (clone $borrowingQuery)
            ->with('user', 'inventory')
            ->latest()
            ->limit(5)
            ->get();

        $recentAttendances = (clone $attendanceQuery)
            ->with('user', 'event')
            ->latest('scan_time')
            ->limit(5)
            ->get();

        // Attendance trend based on period
        $trendData = $this->getTrendData($attendanceQuery, $period);

        return Inertia::render('Dashboards/Index', [
            'stats' => [
                'totalMembers' => $totalMembers,
                'activeMembers' => $activeMembers,
                'totalEvents' => $totalEvents,
                'upcomingEvents' => $upcomingEvents,
                'totalBorrowings' => $totalBorrowings,
                'activeBorrowings' => $activeBorrowings,
                'pendingBorrowings' => $pendingBorrowings,
                'damagedInventories' => $damagedInventories,
                'thisPeriodAttendance' => $thisPeriodAttendance,
                'attendanceStats' => $attendanceStats,
            ],
            'moduleSummary' => $moduleSummary,
            'pendingBorrowings' => $pendingBorrowingsList,
            'recentBorrowings' => $recentBorrowings,
            'recentAttendances' => $recentAttendances,
            'trendData' => $trendData,
            'selectedPeriod' => $period,
            'userRole' => $userRole,
            'user' => $user,
        ]);
    }

    public function trend(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role;
        $period = $request->input('period', 'bulanan');

        $attendanceQuery = Attendance::query();

        if (!in_array($userRole, ['ketua_umum', 'pengurus'])) {
            $attendanceQuery->where('user_id', $user->id);
        }

        $trendData = $this->getTrendData($attendanceQuery, $period);

        return response()->json([
            'trendData' => $trendData,
            'selectedPeriod' => $period,
        ]);
    }

    private function getTrendData($attendanceQuery, $period)
    {
        $now = now();

        switch ($period) {
            case 'harian':
                $data = [];
                for ($i = 6; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i);
                    $count = (clone $attendanceQuery)
                        ->whereDate('scan_time', $date)
                        ->where('status', 'Hadir')
                        ->count();
                    $data[] = [
                        'label' => $date->format('d M'),
                        'count' => $count,
                    ];
                }
                return $data;

            case 'mingguan':
                $data = [];
                for ($i = 7; $i >= 0; $i--) {
                    $weekStart = $now->copy()->startOfWeek()->subWeeks($i);
                    $weekEnd = $weekStart->copy()->endOfWeek();
                    $count = (clone $attendanceQuery)
                        ->whereBetween('scan_time', [$weekStart, $weekEnd])
                        ->where('status', 'Hadir')
                        ->count();
                    $data[] = [
                        'label' => $weekStart->format('d M') . ' - ' . $weekEnd->format('d M'),
                        'count' => $count,
                    ];
                }
                return $data;

            case 'tahunan':
                $data = [];
                for ($i = 11; $i >= 0; $i--) {
                    $month = $now->copy()->subMonths($i);
                    $count = (clone $attendanceQuery)
                        ->whereMonth('scan_time', $month->month)
                        ->whereYear('scan_time', $month->year)
                        ->where('status', 'Hadir')
                        ->count();
                    $data[] = [
                        'label' => $month->format('M Y'),
                        'count' => $count,
                    ];
                }
                return $data;

            case 'bulanan':
            default:
                $data = [];
                for ($i = 5; $i >= 0; $i--) {
                    $month = $now->copy()->subMonths($i);
                    $count = (clone $attendanceQuery)
                        ->whereMonth('scan_time', $month->month)
                        ->whereYear('scan_time', $month->year)
                        ->where('status', 'Hadir')
                        ->count();
                    $data[] = [
                        'label' => $month->format('M Y'),
                        'count' => $count,
                    ];
                }
                return $data;
        }
    }
}
