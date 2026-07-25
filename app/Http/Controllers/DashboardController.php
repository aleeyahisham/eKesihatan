<?php
 
namespace App\Http\Controllers;
 
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\HealthService;
use App\Models\MedicalDocument;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
 
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $initials = collect(preg_split('/\s+/', trim($user->name)))
            ->filter()
            ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        $profileInitials = Str::substr($initials, 0, 2);
 
        if ($user->isAdmin()) {
            $attendancePeriod = $request->query('attendance_period', 'weekly');
            if (!in_array($attendancePeriod, ['weekly', 'monthly'], true)) {
                $attendancePeriod = 'weekly';
            }

            [$attendanceWindowStart, $attendanceWindowEnd] = $this->resolveAttendanceWindow($attendancePeriod);
            $attendanceAppointments = Appointment::with(['patient', 'doctor', 'service'])
                ->whereBetween('scheduled_at', [$attendanceWindowStart, $attendanceWindowEnd])
                ->orderBy('scheduled_at')
                ->get();

            $attendanceSummary = $this->buildAttendanceSummary($attendanceAppointments);
            $attendanceBuckets = $this->buildAttendanceBuckets(
                $attendanceAppointments,
                $attendancePeriod,
                $attendanceWindowStart,
                $attendanceWindowEnd
            );

            $missedAppointments = Appointment::with(['patient', 'doctor', 'service'])
                ->whereIn('status', ['no-show', 'cancelled'])
                ->whereBetween('scheduled_at', [$attendanceWindowStart, $attendanceWindowEnd])
                ->orderByDesc('scheduled_at')
                ->limit(8)
                ->get();

            $followUpAppointments = Appointment::with(['patient', 'doctor', 'service'])
                ->whereIn('status', ['pending', 'approved', 'rescheduled', 'checked-in'])
                ->where('scheduled_at', '<', now())
                ->orderBy('scheduled_at')
                ->limit(8)
                ->get();

            $calendarDays = Collection::times(7, function ($index) {
                return now()->startOfDay()->addDays($index - 1);
            });

            $calendarStart = $calendarDays->first();
            $calendarEnd = $calendarDays->last();

            $calendarSlots = AppointmentSlot::with('doctor')
                ->whereDate('slot_date', '>=', $calendarStart->toDateString())
                ->whereDate('slot_date', '<=', $calendarEnd->toDateString())
                ->orderBy('slot_date')
                ->orderBy('start_time')
                ->get();

            $slotMap = $calendarSlots->groupBy(function ($slot) {
                return $slot->doctor_id . '|' . $slot->slot_date->format('Y-m-d');
            });

            return view('dashboard.admin', [
                'pendingAppointments' => Appointment::where('status', 'pending')->count(),
                'todayAppointments' => Appointment::whereDate('scheduled_at', now()->toDateString())->count(),
                'servicesCount' => HealthService::count(),
                'doctorsCount' => User::where('role', User::ROLE_DOCTOR)->count(),
                'calendarDays' => $calendarDays,
                'calendarDoctors' => User::where('role', User::ROLE_DOCTOR)->orderBy('name')->get(),
                'slotMap' => $slotMap,
                'attendancePeriod' => $attendancePeriod,
                'attendanceWindowStart' => $attendanceWindowStart,
                'attendanceWindowEnd' => $attendanceWindowEnd,
                'attendanceSummary' => $attendanceSummary,
                'attendanceBuckets' => $attendanceBuckets,
                'missedAppointments' => $missedAppointments,
                'followUpAppointments' => $followUpAppointments,
                'profileInitials' => $profileInitials,
            ]);
        }
 
        if ($user->isDoctor()) {
            $todayAppointments = Appointment::with(['patient', 'service'])
                ->where('doctor_id', $user->id)
                ->whereDate('scheduled_at', now()->toDateString())
                ->orderBy('scheduled_at')
                ->get();
 
            return view('dashboard.doctor', [
                'appointments' => $todayAppointments,
                'profileInitials' => $profileInitials,
            ]);
        }
 
        $upcomingAppointments = Appointment::with(['doctor', 'service'])
            ->where('patient_id', $user->id)
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->get();
 
        $pastAppointments = Appointment::with(['doctor', 'service'])
            ->where('patient_id', $user->id)
            ->where('scheduled_at', '<', now())
            ->orderByDesc('scheduled_at')
            ->limit(6)
            ->get();

        $documents = MedicalDocument::whereHas('appointment', function ($query) use ($user) {
            $query->where('patient_id', $user->id);
        })
            ->orderByDesc('uploaded_at')
            ->limit(6)
            ->get();

        return view('dashboard.patient', [
            'patient' => $user,
            'profileInitials' => $profileInitials,
            'appointments' => $upcomingAppointments,
            'pastAppointments' => $pastAppointments,
            'documents' => $documents,
        ]);
    }

    private function resolveAttendanceWindow(string $attendancePeriod): array
    {
        if ($attendancePeriod === 'monthly') {
            return [now()->startOfMonth(), now()->endOfMonth()];
        }

        return [now()->startOfWeek(), now()->endOfWeek()];
    }

    private function buildAttendanceSummary(Collection $appointments): array
    {
        $totalScheduled = $appointments->count();
        $attendedCount = $appointments->whereIn('status', ['checked-in', 'completed'])->count();
        $missedCount = $appointments->whereIn('status', ['no-show', 'cancelled'])->count();
        $pendingCount = max($totalScheduled - $attendedCount - $missedCount, 0);

        return [
            'totalScheduled' => $totalScheduled,
            'attendedCount' => $attendedCount,
            'missedCount' => $missedCount,
            'pendingCount' => $pendingCount,
            'attendanceRate' => $totalScheduled > 0 ? (int) round(($attendedCount / $totalScheduled) * 100) : 0,
            'missedRate' => $totalScheduled > 0 ? (int) round(($missedCount / $totalScheduled) * 100) : 0,
        ];
    }

    private function buildAttendanceBuckets(
        Collection $appointments,
        string $attendancePeriod,
        Carbon $attendanceWindowStart,
        Carbon $attendanceWindowEnd
    ): Collection {
        if ($attendancePeriod === 'monthly') {
            return $this->buildMonthlyAttendanceBuckets($appointments, $attendanceWindowStart, $attendanceWindowEnd);
        }

        return $this->buildWeeklyAttendanceBuckets($appointments, $attendanceWindowStart, $attendanceWindowEnd);
    }

    private function buildWeeklyAttendanceBuckets(
        Collection $appointments,
        Carbon $attendanceWindowStart,
        Carbon $attendanceWindowEnd
    ): Collection {
        $totalDays = $attendanceWindowStart->diffInDays($attendanceWindowEnd) + 1;

        return Collection::times($totalDays, function ($dayOffset) use ($appointments, $attendanceWindowStart) {
            $day = $attendanceWindowStart->copy()->addDays($dayOffset - 1);
            $dailyAppointments = $appointments->filter(function (Appointment $appointment) use ($day) {
                return $appointment->scheduled_at->isSameDay($day);
            });

            $scheduledCount = $dailyAppointments->count();
            $attendedCount = $dailyAppointments->whereIn('status', ['checked-in', 'completed'])->count();
            $missedCount = $dailyAppointments->whereIn('status', ['no-show', 'cancelled'])->count();

            return [
                'label' => $day->format('D, d M'),
                'range' => $day->format('d M Y'),
                'scheduledCount' => $scheduledCount,
                'attendedCount' => $attendedCount,
                'missedCount' => $missedCount,
                'attendanceRate' => $scheduledCount > 0 ? (int) round(($attendedCount / $scheduledCount) * 100) : 0,
            ];
        });
    }

    private function buildMonthlyAttendanceBuckets(
        Collection $appointments,
        Carbon $attendanceWindowStart,
        Carbon $attendanceWindowEnd
    ): Collection {
        $buckets = collect();
        $weekCursor = $attendanceWindowStart->copy()->startOfWeek();
        $weekNumber = 1;

        while ($weekCursor->lte($attendanceWindowEnd)) {
            $bucketStart = $weekCursor->copy()->lt($attendanceWindowStart)
                ? $attendanceWindowStart->copy()
                : $weekCursor->copy();
            $bucketEnd = $weekCursor->copy()->endOfWeek()->gt($attendanceWindowEnd)
                ? $attendanceWindowEnd->copy()
                : $weekCursor->copy()->endOfWeek();

            $weeklyAppointments = $appointments->filter(function (Appointment $appointment) use ($bucketStart, $bucketEnd) {
                return $appointment->scheduled_at->gte($bucketStart) && $appointment->scheduled_at->lte($bucketEnd);
            });

            $scheduledCount = $weeklyAppointments->count();
            $attendedCount = $weeklyAppointments->whereIn('status', ['checked-in', 'completed'])->count();
            $missedCount = $weeklyAppointments->whereIn('status', ['no-show', 'cancelled'])->count();

            $buckets->push([
                'label' => 'Week ' . $weekNumber,
                'range' => $bucketStart->format('d M') . ' - ' . $bucketEnd->format('d M'),
                'scheduledCount' => $scheduledCount,
                'attendedCount' => $attendedCount,
                'missedCount' => $missedCount,
                'attendanceRate' => $scheduledCount > 0 ? (int) round(($attendedCount / $scheduledCount) * 100) : 0,
            ]);

            $weekCursor->addWeek();
            $weekNumber++;
        }

        return $buckets;
    }
}