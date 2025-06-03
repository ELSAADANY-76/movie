<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::with(['showtimes' => function ($query) {
            $query->where('showtime', '>', now())
                ->orderBy('showtime');
        }])
        ->latest()
        ->paginate(10);

        return response()->json($movies);
    }

    public function show(Movie $movie)
    {
        $movie->load(['showtimes' => function ($query) {
            $query->where('showtime', '>', now())
                ->orderBy('showtime');
        }]);

        return response()->json($movie);
    }
} 