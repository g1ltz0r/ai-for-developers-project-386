<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventTypeCreateRequest;
use App\Http\Requests\EventTypeUpdateRequest;
use App\Http\Resources\EventTypeResource;
use App\Models\EventType;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class EventTypeController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    public function index(): JsonResponse
    {
        $eventTypes = EventType::query()->orderBy('created_at', 'asc')->get();

        return EventTypeResource::collection($eventTypes)->response();
    }

    public function store(EventTypeCreateRequest $request): JsonResponse
    {
        $eventType = EventType::create($request->validated());

        return (new EventTypeResource($eventType))
            ->response()
            ->setStatusCode(201);
    }

    public function update(EventTypeUpdateRequest $request, string $id): JsonResponse
    {
        $eventType = EventType::query()->findOrFail($id);
        $eventType->update($request->validated());
        $eventType->refresh();

        return (new EventTypeResource($eventType))->response();
    }

    public function destroy(string $id): Response|JsonResponse
    {
        $eventType = EventType::query()->findOrFail($id);

        if ($this->bookingService->hasBookingsForEventType($id)) {
            return response()->json(['message' => 'Cannot delete event type with existing bookings.'], 409);
        }

        $eventType->delete();

        return response()->noContent();
    }
}
