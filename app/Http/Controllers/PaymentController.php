<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer']);
    }

    public function processPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,credit_balance',
            'card_number' => 'required_if:payment_method,credit_card|nullable|string|size:16',
            'expiry_date' => 'required_if:payment_method,credit_card|nullable|string|size:5',
            'cvv' => 'required_if:payment_method,credit_card|nullable|string|size:3',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $totalAmount = $booking->seats->count() * $booking->showtime->price;

            if ($request->payment_method === 'credit_balance') {
                if ($user->credit < $totalAmount) {
                    return response()->json([
                        'message' => 'Insufficient credit balance'
                    ], 422);
                }

                // Deduct from user's credit
                $user->update([
                    'credit' => $user->credit - $totalAmount
                ]);
            } else {
                // Process credit card payment (implement your payment gateway logic here)
                // This is a placeholder for actual payment processing
                $this->processCreditCardPayment($request);
            }

            // Update booking status
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method,
                'amount_paid' => $totalAmount
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Payment processed successfully',
                'booking' => $booking->load(['showtime.movie', 'seats'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Payment processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addCredit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'card_number' => 'required|string|size:16',
            'expiry_date' => 'required|string|size:5',
            'cvv' => 'required|string|size:3',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            // Process credit card payment (implement your payment gateway logic here)
            // This is a placeholder for actual payment processing
            $this->processCreditCardPayment($request);

            // Add credit to user's balance
            $user->update([
                'credit' => $user->credit + $request->amount
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Credit added successfully',
                'new_balance' => $user->credit
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to add credit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function processCreditCardPayment(Request $request)
    {
        // Implement your actual payment gateway logic here
        // This is just a placeholder
        return true;
    }

    public function Payments(Showtime $showtime)
    {
        // if (!auth()->user()->hasRole('customer')) {
        //     abort(403, 'Unauthorized action.');
        // }

        return view('users.add-credit', compact('showtime'));
    }




} 