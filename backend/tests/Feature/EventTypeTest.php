<?php

use App\Models\EventType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('event types', function () {
    it('lists empty event types', function () {
        $this->getJson('/api/event-types')
            ->assertStatus(200)
            ->assertExactJson([]);
    });

    it('creates an event type', function () {
        $this->postJson('/api/event-types', [
            'title' => 'Consultation',
            'duration_minutes' => 30,
        ])
            ->assertStatus(201)
            ->assertJsonPath('title', 'Consultation')
            ->assertJsonPath('duration_minutes', 30)
            ->assertJsonPath('description', null);
    });

    it('lists event types ordered by created_at', function () {
        EventType::create(['title' => 'First', 'duration_minutes' => 15]);
        EventType::create(['title' => 'Second', 'duration_minutes' => 30]);

        $response = $this->getJson('/api/event-types');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonPath('0.title', 'First')
            ->assertJsonPath('1.title', 'Second');
    });

    it('updates an event type', function () {
        $eventType = EventType::create(['title' => 'Old', 'duration_minutes' => 15]);

        $this->patchJson('/api/event-types/'.$eventType->id, [
            'title' => 'New',
            'duration_minutes' => 45,
        ])
            ->assertStatus(200)
            ->assertJsonPath('title', 'New')
            ->assertJsonPath('duration_minutes', 45);
    });

    it('deletes an event type', function () {
        $eventType = EventType::create(['title' => 'To delete', 'duration_minutes' => 15]);

        $this->deleteJson('/api/event-types/'.$eventType->id)
            ->assertStatus(204);

        expect(EventType::count())->toBe(0);
    });

    it('cannot delete an event type that has bookings', function () {
        $eventType = EventType::create(['title' => 'Booked', 'duration_minutes' => 30]);
        $eventType->bookings()->create([
            'start_time' => now()->addDay()->startOfDay()->addHours(10),
            'end_time' => now()->addDay()->startOfDay()->addHours(10)->addMinutes(30),
            'duration_minutes' => 30,
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'event_type_title' => $eventType->title,
        ]);

        $this->deleteJson('/api/event-types/'.$eventType->id)
            ->assertStatus(409)
            ->assertJsonPath('message', 'Cannot delete event type with existing bookings.');
    });
});
