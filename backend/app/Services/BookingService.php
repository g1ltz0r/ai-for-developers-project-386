<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\EventType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class BookingService
{
    private const WINDOW_DAYS = 14;

    private const GRID_MINUTES = 15;

    /**
     * Create a booking for a requested slot.
     *
     * The start time must be inside the 14-day booking window, on the 15-minute
     * grid and not in the past. If it overlaps with an existing booking, a 409
     * conflict is raised.
     *
     * @throws ConflictHttpException if the slot is already occupied.
     * @throws InvalidArgumentException if the requested slot is not available.
     * @throws ModelNotFoundException if the event type does not exist.
     */
    public function createBooking(
        string $eventTypeId,
        string $guestName,
        string $guestEmail,
        string $startTime,
    ): Booking {
        $eventType = EventType::query()->findOrFail($eventTypeId);

        $start = Carbon::parse($startTime);
        $now = Carbon::now();

        if ($start < $now) {
            throw new InvalidArgumentException('The selected slot is in the past.');
        }

        $startInTz = $start->copy()->setTimezone($start->timezone->getName());
        $windowStart = $startInTz->copy()->startOfDay();
        $windowEnd = $windowStart->copy()->addDays(self::WINDOW_DAYS);

        if ($startInTz < $windowStart || $startInTz >= $windowEnd) {
            throw new InvalidArgumentException('The selected start time is outside the booking window.');
        }

        if ($startInTz->minute % self::GRID_MINUTES !== 0 || $startInTz->second !== 0) {
            throw new InvalidArgumentException('The selected start time is not a valid slot.');
        }

        $end = $start->copy()->addMinutes($eventType->duration_minutes);

        return DB::transaction(function () use ($eventType, $start, $end, $guestName, $guestEmail) {
            $overlapping = Booking::query()
                ->where('event_type_id', $eventType->id)
                ->where('start_time', '<', $end)
                ->where('end_time', '>', $start)
                ->lockForUpdate()
                ->first();

            if ($overlapping !== null) {
                throw new ConflictHttpException('The selected slot is already occupied.');
            }

            return Booking::create([
                'event_type_id' => $eventType->id,
                'start_time' => $start,
                'end_time' => $end,
                'duration_minutes' => $eventType->duration_minutes,
                'guest_name' => $guestName,
                'guest_email' => $guestEmail,
                'event_type_title' => $eventType->title,
            ]);
        });
    }

    /**
     * Cancel a booking by id.
     *
     * @throws ModelNotFoundException if the booking does not exist.
     */
    public function cancelBooking(string $id): void
    {
        $booking = Booking::query()->findOrFail($id);
        $booking->delete();
    }

    /**
     * Determine whether an event type has any bookings.
     */
    public function hasBookingsForEventType(string $eventTypeId): bool
    {
        return Booking::query()->where('event_type_id', $eventTypeId)->exists();
    }
}
