<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingCreateRequest;
use App\Http\Resources\BookingConfirmationResource;
use App\Models\EventType;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class PublicBookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    public function book(BookingCreateRequest $request, string $id): JsonResponse
    {
        $eventType = EventType::query()->findOrFail($id);
        $validated = $request->validated();

        try {
            $booking = $this->bookingService->createBooking(
                $eventType->id,
                $validated['guest_name'],
                $validated['guest_email'],
                $validated['start_time'],
            );
        } catch (InvalidArgumentException $e) {
            throw ValidationException::withMessages(['start_time' => [$e->getMessage()]]);
        } catch (ConflictHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return (new BookingConfirmationResource($booking))
            ->response()
            ->setStatusCode(201);
    }
}
