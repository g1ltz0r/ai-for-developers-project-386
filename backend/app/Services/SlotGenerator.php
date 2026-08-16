<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\EventType;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class SlotGenerator
{
    private const GRID_MINUTES = 15;

    private const WINDOW_DAYS = 14;

    /**
     * Generate free slots for an event type in the requested timezone.
     *
     * @return array{window: array{start: string, end: string}, slots: array<int, array{start: string, duration_minutes: int}>}
     */
    public function generate(EventType $eventType, string $timezone, ?Carbon $now = null): array
    {
        try {
            new DateTimeZone($timezone);
        } catch (\Exception) {
            throw new InvalidArgumentException('Invalid timezone.');
        }

        $now = $now ? Carbon::instance($now) : Carbon::now();
        $nowInTz = $now->copy()->setTimezone($timezone);

        $windowStart = $nowInTz->copy()->startOfDay();
        $windowEnd = $windowStart->copy()->addDays(self::WINDOW_DAYS);

        $occupiedRanges = $this->occupiedRanges($eventType, $windowStart, $windowEnd);

        $slots = [];
        $current = $windowStart->copy();
        $slotDuration = $eventType->duration_minutes;

        while ($current < $windowEnd) {
            $slotEnd = $current->copy()->addMinutes($slotDuration);

            if ($current >= $nowInTz && ! $this->isOccupied($current, $slotEnd, $occupiedRanges)) {
                $slots[] = [
                    'start' => $current->toIso8601String(),
                    'duration_minutes' => $slotDuration,
                ];
            }

            $current->addMinutes(self::GRID_MINUTES);
        }

        return [
            'window' => [
                'start' => $windowStart->toIso8601String(),
                'end' => $windowEnd->toIso8601String(),
            ],
            'slots' => $slots,
        ];
    }

    /**
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    private function occupiedRanges(EventType $eventType, Carbon $windowStart, Carbon $windowEnd): array
    {
        $utcStart = $windowStart->copy()->utc();
        $utcEnd = $windowEnd->copy()->utc();

        $bookings = Booking::query()
            ->where('event_type_id', $eventType->id)
            ->where(function (Builder $query) use ($utcStart, $utcEnd) {
                $query->where('start_time', '<', $utcEnd)
                    ->where('end_time', '>', $utcStart);
            })
            ->get(['start_time', 'end_time']);

        return $bookings->map(fn (Booking $booking) => [
            'start' => Carbon::instance($booking->start_time),
            'end' => Carbon::instance($booking->end_time),
        ])->all();
    }

    private function isOccupied(Carbon $slotStart, Carbon $slotEnd, array $occupiedRanges): bool
    {
        foreach ($occupiedRanges as $range) {
            if ($slotStart < $range['end'] && $slotEnd > $range['start']) {
                return true;
            }
        }

        return false;
    }
}
