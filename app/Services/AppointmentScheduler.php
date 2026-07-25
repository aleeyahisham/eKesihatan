<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\HealthService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AppointmentScheduler
{
    public function findBestSlot(
        HealthService $service,
        Carbon $preferredDate,
        ?int $doctorId = null,
        ?string $preferredTime = null,
        array $excludedDoctorIds = []
    ): ?AppointmentSlot
    {
        $candidates = $this->collectCandidates($service, $preferredDate, $doctorId, $preferredTime, $excludedDoctorIds);

        return $candidates->first()['slot'] ?? null;
    }

    public function recommendSlots(
        HealthService $service,
        Carbon $preferredDate,
        ?int $doctorId = null,
        ?string $preferredTime = null,
        int $limit = 3,
        array $excludedDoctorIds = []
    ): array
    {
        $candidates = $this->collectCandidates($service, $preferredDate, $doctorId, $preferredTime, $excludedDoctorIds)
            ->take(max(1, $limit));

        return $candidates->map(function (array $candidate) {
            /** @var AppointmentSlot $slot */
            $slot = $candidate['slot'];
            /** @var Carbon $start */
            $start = $candidate['start'];

            return [
                'slot_id' => $slot->id,
                'doctor_id' => $slot->doctor_id,
                'doctor_name' => $slot->doctor?->name,
                'date' => $slot->slot_date->format('Y-m-d'),
                'time' => $start->format('H:i'),
                'display' => $start->format('D, d M Y h:i A'),
                'remaining_capacity' => max(0, (int) $slot->capacity - (int) $candidate['active_count']),
                'reason' => 'Earliest valid slot with strong capacity utilization and balanced doctor load.',
            ];
        })->values()->all();
    }

    private function collectCandidates(
        HealthService $service,
        Carbon $preferredDate,
        ?int $doctorId,
        ?string $preferredTime,
        array $excludedDoctorIds
    ): Collection {
        $preferredDate = $preferredDate->copy()->startOfDay();
        $minimumSchedulableTime = now()->addMinutes(85)->startOfMinute();

        $preferredStartDateTime = $preferredDate->copy();
        if (is_string($preferredTime) && preg_match('/^\d{2}:\d{2}$/', $preferredTime) === 1) {
            $preferredStartDateTime = Carbon::parse($preferredDate->format('Y-m-d') . ' ' . $preferredTime);
        }

        $earliestAllowedStart = $preferredStartDateTime->greaterThan($minimumSchedulableTime)
            ? $preferredStartDateTime
            : $minimumSchedulableTime;

        $eligibleDoctorIds = $doctorId
            ? collect([$doctorId])
            : User::doctorsForService($service->name)->pluck('id');

        if (!empty($excludedDoctorIds)) {
            $eligibleDoctorIds = $eligibleDoctorIds
                ->reject(fn ($id) => in_array((int) $id, array_map('intval', $excludedDoctorIds), true))
                ->values();
        }

        if ($eligibleDoctorIds->isEmpty()) {
            return collect();
        }

        $slots = AppointmentSlot::query()
            ->with('doctor')
            ->where('is_active', true)
            ->whereDate('slot_date', '>=', $preferredDate->toDateString())
            ->whereIn('doctor_id', $eligibleDoctorIds)
            ->orderBy('slot_date')
            ->orderBy('start_time')
            ->get();

        if ($slots->isEmpty()) {
            return collect();
        }

        $slotIds = $slots->pluck('id');
        $activeCounts = Appointment::whereIn('appointment_slot_id', $slotIds)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->selectRaw('appointment_slot_id, count(*) as total')
            ->groupBy('appointment_slot_id')
            ->pluck('total', 'appointment_slot_id');

        $workloadMap = $this->buildWorkloadMap($slots, $preferredDate);

        return $slots->map(function (AppointmentSlot $slot) use ($service, $earliestAllowedStart, $activeCounts, $workloadMap) {
            $start = Carbon::parse($slot->slot_date->format('Y-m-d') . ' ' . $slot->start_time);
            $end = Carbon::parse($slot->slot_date->format('Y-m-d') . ' ' . $slot->end_time);

            if ($end->lessThanOrEqualTo($start)) {
                return null;
            }

            $slotDuration = $start->diffInMinutes($end);
            if ($service->duration_minutes && $slotDuration < $service->duration_minutes) {
                return null;
            }

            if ($start->lessThan($earliestAllowedStart)) {
                return null;
            }

            $activeCount = (int) ($activeCounts[$slot->id] ?? 0);
            if ($activeCount >= $slot->capacity) {
                return null;
            }

            $fillRatio = $slot->capacity > 0 ? $activeCount / $slot->capacity : 0;
            $dayKey = $slot->slot_date->format('Y-m-d');
            $workloadKey = $slot->doctor_id . '|' . $dayKey;
            $workload = (int) ($workloadMap[$workloadKey] ?? 0);

            return [
                'slot' => $slot,
                'start' => $start,
                'active_count' => $activeCount,
                'fill_ratio' => $fillRatio,
                'workload' => $workload,
            ];
        })->filter()->sort(function (array $a, array $b) {
            if ($a['start']->ne($b['start'])) {
                return $a['start']->lt($b['start']) ? -1 : 1;
            }

            if ($a['fill_ratio'] !== $b['fill_ratio']) {
                return $a['fill_ratio'] > $b['fill_ratio'] ? -1 : 1;
            }

            if ($a['workload'] !== $b['workload']) {
                return $a['workload'] < $b['workload'] ? -1 : 1;
            }

            return 0;
        })->values();
    }

    private function buildWorkloadMap(Collection $slots, Carbon $preferredDate): Collection
    {
        $doctorIds = $slots->pluck('doctor_id')->unique();

        return Appointment::whereIn('doctor_id', $doctorIds)
            ->whereDate('scheduled_at', '>=', $preferredDate->toDateString())
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->selectRaw('doctor_id, DATE(scheduled_at) as day, count(*) as total')
            ->groupBy('doctor_id', 'day')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$row->doctor_id . '|' . $row->day => $row->total];
            });
    }
}
