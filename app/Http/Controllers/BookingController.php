<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Showtime;
use App\Models\Seat;
use App\Notifications\BookingConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookingController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(['auth', 'role:customer']);
    }

    public function create(Showtime $showtime)
    {
        if (!auth()->user()->hasRole('customer')) {
            abort(403, 'Unauthorized action.');
        }

        return view('bookings.create', compact('showtime'));
    }

    public function store(Request $request, Showtime $showtime)
    {
        $request->validate([
            'seats' => 'required|array',
            'seats.*' => 'required|integer|exists:seats,id',
        ]);

        try {
            DB::beginTransaction();

            // Check if seats are available
            foreach ($request->seats as $seatId) {
                if ($showtime->bookings()->whereHas('seats', function($query) use ($seatId) {
                    $query->where('seats.id', $seatId);
                })->exists()) {
                    return response()->json([
                        'message' => 'One or more selected seats are already booked'
                    ], 422);
                }
            }

            // Create booking
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'showtime_id' => $showtime->id,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Attach seats to booking
            $booking->seats()->attach($request->seats);

            DB::commit();

            return response()->json([
                'message' => 'Booking created successfully',
                'booking' => $booking->load(['showtime.movie', 'seats'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($booking->load(['showtime.movie', 'seats']));
    }

    public function index()
    {
        $bookings = Auth::user()->bookings()
            ->with(['showtime.movie', 'seats'])
            ->latest()
            ->paginate(10);

        return response()->json($bookings);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        if (!auth()->user()->hasRole(['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        $booking->update(['status' => $validated['status']]);

        return back()->with('success', 'Booking status updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($booking->status === 'confirmed') {
            return response()->json([
                'message' => 'Cannot cancel a confirmed booking'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // If payment was made using credit balance, refund it
            if ($booking->payment_status === 'paid' && $booking->payment_method === 'credit_balance') {
                $user = Auth::user();
                $user->update([
                    'credit' => $user->credit + $booking->amount_paid
                ]);
            }

            $booking->delete();

            DB::commit();

            return response()->json([
                'message' => 'Booking cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to cancel booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Add a method to handle booking cancellation if needed
    public function cancel(Booking $booking)
    {
        // Add authorization check if needed
        // if (Auth::id() !== $booking->user_id) { ... }

        // Only allow cancelling pending bookings
        if ($booking->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending bookings can be cancelled.']);
        }

        DB::transaction(function () use ($booking) {
            // Increase available seats for the showtime
            $booking->showtime->increment('available_seats', $booking->seats);

            // Refund credit to the user
            $booking->user->increment('credit', $booking->total_amount);

            // Update booking status
            $booking->status = 'cancelled';
            $booking->save();
        });

         return back()->with('success', 'Booking cancelled successfully.');
    }

    public function getAvailableSeats(Showtime $showtime)
    {
        $bookedSeats = $showtime->bookings()
            ->where('status', '!=', 'cancelled')
            ->pluck('seats.id')
            ->toArray();

        $availableSeats = Seat::whereNotIn('id', $bookedSeats)->get();

        return response()->json($availableSeats);
    }
} 