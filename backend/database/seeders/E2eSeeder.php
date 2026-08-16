<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class E2eSeeder extends Seeder
{
    /**
     * Seed the database with predictable event types for e2e tests.
     */
    public function run(): void
    {
        EventType::create([
            'title' => 'Консультация',
            'description' => 'Короткая консультация 30 минут',
            'duration_minutes' => 30,
        ]);

        EventType::create([
            'title' => 'Собеседование',
            'description' => 'Техническое собеседование',
            'duration_minutes' => 60,
        ]);
    }
}
