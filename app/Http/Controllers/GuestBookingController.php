<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestBookingController extends Controller
{
    public function store(Request $request, Showtime $showtime)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'seats' => 'required|array',
            'seats.*' => 'required|integer|exists:seats,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if seats are available
        foreach ($request->seats as $seatId) {
            if ($showtime->bookings()->where('seat_id', $seatId)->exists()) {
                return response()->json([
                    'message' => 'One or more selected seats are already booked'
                ], 422);
            }
        }

        // Create guest booking
        $booking = Booking::create([
            'showtime_id' => $showtime->id,
            'name' => $request->name,
            'email' => $request->email,
            'status' => 'pending',
            'is_guest' => true,
        ]);

        // Attach seats to booking
        $booking->seats()->attach($request->seats);

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking->load('seats')
        ], 201);
    }
} 