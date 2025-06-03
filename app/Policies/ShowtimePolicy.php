<?php

namespace App\Policies;

use App\Models\Showtime;
use App\Models\User;

class ShowtimePolicy
{
    public function viewAny(?User $user)
    {
        return true; // Anyone can view showtimes
    }

    public function view(?User $user, Showtime $showtime)
    {
        return true; // Anyone can view a showtime
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function update(User $user, Showtime $showtime)
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function delete(User $user, Showtime $showtime)
    {
        return $user->isAdmin() || $user->isManager();
    }
} 