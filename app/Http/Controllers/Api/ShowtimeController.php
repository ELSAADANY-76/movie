<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    public function index(Movie $movie)
    {
        $showtimes = $movie->showtimes()
            ->where('showtime', '>', now())
            ->orderBy('showtime')
            ->paginate(10);

        return response()->json($showtimes);
    }

    public function show(Showtime $showtime)
    {
        $showtime->load(['movie', 'bookings' => function ($query) {
            $query->where('status', 'confirmed');
        }]);

        return response()->json($showtime);
    }
} 