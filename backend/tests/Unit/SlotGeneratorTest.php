<?php

use App\Models\Booking;
use App\Models\EventType;
use App\Services\SlotGenerator;
use Carbon\Carbon;
it('generates 15-minute slots for the requested timezone', function () {
    $eventType = EventType::create(['title' => 'Test', 'duration_minutes' => 30]);
    $now = Carbon::parse('2026-08-15T10:00:00+00:00');

    $result = app(SlotGenerator::class)->generate($eventType, 'UTC', $now);

    expect($result['window']['start'])->toBe('2026-08-15T00:00:00+00:00')
        ->and($result['window']['end'])->toBe('2026-08-29T00:00:00+00:00')
        ->and(count($result['slots']))->toBeGreaterThan(0)
        ->and($result['slots'][0]['duration_minutes'])->toBe(30)
        ->and(Carbon::parse($result['slots'][0]['start'])->minute % 15)->toBe(0);
});

it('excludes occupied and past slots', function () {
    $eventType = EventType::create(['title' => 'Test', 'duration_minutes' => 30]);
    $now = Carbon::parse('2026-08-15T10:00:00+00:00');

    // Occupy the 10:00 slot.
    $eventType->bookings()->create([
        'start_time' => '2026-08-15T10:00:00+00:00',
        'end_time' => '2026-08-15T10:30:00+00:00',
        'duration_minutes' => 30,
        'guest_name' => 'Guest',
        'guest_email' => 'guest@example.com',
        'event_type_title' => $eventType->title,
    ]);

    $result = app(SlotGenerator::class)->generate($eventType, 'UTC', $now);
    $starts = array_map(fn ($slot) => $slot['start'], $result['slots']);

    expect(in_array('2026-08-15T10:00:00+00:00', $starts))->toBeFalse()
        ->and(in_array('2026-08-15T09:45:00+00:00', $starts))->toBeFalse()
        ->and(in_array('2026-08-15T10:30:00+00:00', $starts))->toBeTrue();
});
