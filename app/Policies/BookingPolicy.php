<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user)
    {
        return true; // Any authenticated user can view their bookings
    }

    public function view(User $user, Booking $booking)
    {
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

    public function create(User $user)
    {
        return $user->isCustomer(); // Only customers can create bookings
    }

    public function update(User $user, Booking $booking)
    {
        return $user->isAdmin() || $user->isStaff() || 
            ($user->isCustomer() && $user->id === $booking->user_id);
    }

    public function delete(User $user, Booking $booking)
    {
        return $user->isAdmin() || $user->isStaff() || 
            ($user->isCustomer() && $user->id === $booking->user_id);
    }
} 