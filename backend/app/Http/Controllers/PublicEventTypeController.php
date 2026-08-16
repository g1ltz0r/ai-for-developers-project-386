<?php

namespace App\Http\Controllers;

use App\Http\Requests\SlotRequest;
use App\Http\Resources\SlotsResponseResource;
use App\Models\EventType;
use App\Services\SlotGenerator;

class PublicEventTypeController extends Controller
{
    public function __construct(private readonly SlotGenerator $slotGenerator)
    {
    }

    public function slots(SlotRequest $request, string $id): SlotsResponseResource
    {
        $eventType = EventType::query()->findOrFail($id);

        $slots = $this->slotGenerator->generate(
            $eventType,
            $request->validated('tz'),
        );

        return new SlotsResponseResource($slots);
    }
}
