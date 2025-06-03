<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\User;

class MoviePolicy
{
    public function viewAny(?User $user)
    {
        return true; // Anyone can view movies
    }

    public function view(?User $user, Movie $movie)
    {
        return true; // Anyone can view a movie
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function update(User $user, Movie $movie)
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function delete(User $user, Movie $movie)
    {
        return $user->isAdmin();
    }
} 