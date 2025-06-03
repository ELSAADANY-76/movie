<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Showtime;
use App\Notifications\BookingConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['showtime.movie'])
            ->latest()
            ->paginate(10);

        return response()->json($bookings);
    }

    public function store(Request $request, Showtime $showtime)
    {
        $validated = $request->validate([
            'seats' => 'required|array',
            'seats.*' => 'required|string|exists:seats,seat_number',
        ]);

        $user = $request->user();
        $totalAmount = count($validated['seats']) * $showtime->price_per_ticket;

        if ($user->credit < $totalAmount) {
            throw ValidationException::withMessages([
                'credit' => 'Insufficient credit balance.',
            ]);
        }

        if (count($validated['seats']) > $showtime->available_seats) {
            throw ValidationException::withMessages([
                'seats' => 'Not enough seats available.',
            ]);
        }

        try {
            DB::beginTransaction();

            $booking = $user->bookings()->create([
                'showtime_id' => $showtime->id,
                'seats' => $validated['seats'],
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
            ]);

            $user->decrement('credit', $totalAmount);
            $showtime->decrement('available_seats', count($validated['seats']));

            $user->notify(new BookingConfirmation($booking));

            DB::commit();

            return response()->json([
                'message' => 'Booking confirmed successfully',
                'booking' => $booking->load(['showtime.movie']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        return response()->json($booking->load(['showtime.movie']));
    }

    public function destroy(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($booking->status !== 'confirmed') {
            throw ValidationException::withMessages([
                'status' => 'Cannot cancel this booking.',
            ]);
        }

        try {
            DB::beginTransaction();

            $booking->user->increment('credit', $booking->total_amount);
            $booking->showtime->increment('available_seats', count($booking->seats));
            $booking->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'message' => 'Booking cancelled successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
} 