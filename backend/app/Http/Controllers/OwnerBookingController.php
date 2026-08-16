<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingListRequest;
use App\Http\Resources\AdminBookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class OwnerBookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    public function index(BookingListRequest $request): JsonResponse
    {
        $limit = $request->integer('limit', 50);
        $limit = max(1, min(100, $limit));
        $perPage = $limit + 1;

        $query = Booking::query()
            ->where('start_time', '>', Carbon::now())
            ->orderBy('start_time', 'asc')
            ->orderBy('id', 'asc');

        if ($request->filled('cursor')) {
            $cursor = $this->decodeCursor($request->input('cursor'));
            $query->where(function ($q) use ($cursor) {
                $q->where('start_time', '>', $cursor['start_time'])
                    ->orWhere(function ($q2) use ($cursor) {
                        $q2->where('start_time', '=', $cursor['start_time'])
                            ->where('id', '>', $cursor['id']);
                    });
            });
        }

        $items = $query->limit($perPage)->get();

        $nextCursor = null;
        if ($items->count() > $limit) {
            $items = $items->slice(0, $limit)->values();
            $last = $items->last();
            $nextCursor = base64_encode(json_encode([
                'start_time' => $last->start_time->toIso8601String(),
                'id' => $last->id,
            ]));
        }

        return response()->json([
            'items' => AdminBookingResource::collection($items),
            'next_cursor' => $nextCursor,
        ]);
    }

    public function destroy(string $id): Response
    {
        $this->bookingService->cancelBooking($id);

        return response()->noContent();
    }

    /**
     * @return array{start_time: Carbon, id: string}
     */
    private function decodeCursor(string $cursor): array
    {
        $decoded = json_decode(base64_decode($cursor), true);

        if (! is_array($decoded) || empty($decoded['start_time']) || empty($decoded['id'])) {
            throw ValidationException::withMessages(['cursor' => ['Invalid cursor.']]);
        }

        return [
            'start_time' => Carbon::parse($decoded['start_time']),
            'id' => $decoded['id'],
        ];
    }
}
