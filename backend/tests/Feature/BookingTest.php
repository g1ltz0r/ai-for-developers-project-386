<?php

use App\Models\Booking;
use App\Models\EventType;
use App\Services\SlotGenerator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function firstSlot(EventType $eventType, string $tz = 'UTC'): string
{
    $slots = app(SlotGenerator::class)->generate($eventType, $tz);

    return $slots['slots'][0]['start'];
}

describe('slots', function () {
    it('generates free slots for an event type', function () {
        $eventType = EventType::create(['title' => 'Test', 'duration_minutes' => 30]);

        $this->getJson('/api/event-types/'.$eventType->id.'/slots?tz=UTC')
            ->assertStatus(200)
            ->assertJsonStructure(['window' => ['start', 'end'], 'slots'])
            ->assertJsonPath('slots.0.duration_minutes', 30);
    });
});

describe('booking', function () {
    it('books a free slot', function () {
        $eventType = EventType::create(['title' => 'Test', 'duration_minutes' => 30]);
        $start = firstSlot($eventType, 'UTC');

        $this->postJson('/api/event-types/'.$eventType->id.'/book', [
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'start_time' => $start,
        ])
            ->assertStatus(201)
            ->assertJsonPath('guest_name', 'Guest')
            ->assertJsonPath('duration_minutes', 30)
            ->assertJsonPath('event_type_title', 'Test')
            ->assertJsonPath('event_type_id', $eventType->id);
    });

    it('returns 409 when booking an already occupied slot', function () {
        $eventType = EventType::create(['title' => 'Test', 'duration_minutes' => 30]);
        $start = firstSlot($eventType, 'UTC');

        $this->postJson('/api/event-types/'.$eventType->id.'/book', [
            'guest_name' => 'First',
            'guest_email' => 'first@example.com',
            'start_time' => $start,
        ])->assertStatus(201);

        $this->postJson('/api/event-types/'.$eventType->id.'/book', [
            'guest_name' => 'Second',
            'guest_email' => 'second@example.com',
            'start_time' => $start,
        ])
            ->assertStatus(409)
            ->assertJsonPath('message', 'The selected slot is already occupied.');
    });

    it('cancels a booking and allows rebooking the slot', function () {
        $eventType = EventType::create(['title' => 'Test', 'duration_minutes' => 30]);
        $start = firstSlot($eventType, 'UTC');

        $booked = $this->postJson('/api/event-types/'.$eventType->id.'/book', [
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'start_time' => $start,
        ]);
        $booked->assertStatus(201);

        $this->deleteJson('/api/admin/bookings/'.$booked->json('id'))
            ->assertStatus(204);

        $this->postJson('/api/event-types/'.$eventType->id.'/book', [
            'guest_name' => 'Rebooked',
            'guest_email' => 'rebooked@example.com',
            'start_time' => $start,
        ])->assertStatus(201);
    });
});

describe('owner bookings', function () {
    it('paginates upcoming bookings', function () {
        $eventType = EventType::create(['title' => 'Test', 'duration_minutes' => 30]);
        $base = Carbon::now('UTC')->addDay()->startOfDay()->addHours(9);

        for ($i = 0; $i < 3; $i++) {
            Booking::create([
                'event_type_id' => $eventType->id,
                'start_time' => $base->copy()->addMinutes($i * 30),
                'end_time' => $base->copy()->addMinutes($i * 30 + 30),
                'duration_minutes' => 30,
                'guest_name' => 'Guest '.$i,
                'guest_email' => 'guest'.$i.'@example.com',
                'event_type_title' => $eventType->title,
            ]);
        }

        $first = $this->getJson('/api/admin/bookings?limit=2');
        $first->assertStatus(200)
            ->assertJsonCount(2, 'items')
            ->assertJsonPath('items.0.guest_name', 'Guest 0')
            ->assertJsonPath('items.1.guest_name', 'Guest 1')
            ->assertJsonPath('next_cursor', fn (string $cursor) => ! empty($cursor));

        $second = $this->getJson('/api/admin/bookings?limit=2&cursor='.$first->json('next_cursor'));
        $second->assertStatus(200)
            ->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.guest_name', 'Guest 2')
            ->assertJsonPath('next_cursor', null);
    });
});
