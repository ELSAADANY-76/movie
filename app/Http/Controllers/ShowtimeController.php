<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    public function create(Movie $movie)
    {
        $this->authorize('create', Showtime::class);
        return view('showtimes.create', compact('movie'));
    }

    public function store(Request $request, Movie $movie)
    {
        $this->authorize('create', Showtime::class);

        $validated = $request->validate([
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'price' => 'required|numeric|min:0',
            'total_seats' => 'required|integer|min:1',
            'hall_number' => 'required|string'
        ]);

        $validated['movie_id'] = $movie->id;
        $validated['available_seats'] = $validated['total_seats'];

        Showtime::create($validated);

        return redirect()->route('movies.show', $movie)
            ->with('success', 'Showtime added successfully');
    }

    public function edit(Showtime $showtime)
    {
        $this->authorize('update', $showtime);
        return view('showtimes.edit', compact('showtime'));
    }

    public function update(Request $request, Showtime $showtime)
    {
        $this->authorize('update', $showtime);

        $validated = $request->validate([
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'price' => 'required|numeric|min:0',
            'total_seats' => 'required|integer|min:1',
            'hall_number' => 'required|string'
        ]);

        // Ensure we don't reduce total seats below booked seats
        $bookedSeats = $showtime->total_seats - $showtime->available_seats;
        if ($validated['total_seats'] < $bookedSeats) {
            return back()->withErrors(['total_seats' => 'Cannot reduce total seats below booked seats']);
        }

        $validated['available_seats'] = $validated['total_seats'] - $bookedSeats;

        $showtime->update($validated);

        return redirect()->route('movies.show', $showtime->movie)
            ->with('success', 'Showtime updated successfully');
    }

    public function destroy(Showtime $showtime)
    {
        $this->authorize('delete', $showtime);
        
        // Check if there are any bookings
        if ($showtime->bookings()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete showtime with existing bookings']);
        }

        $movie = $showtime->movie;
        $showtime->delete();

        return redirect()->route('movies.show', $movie)
            ->with('success', 'Showtime deleted successfully');
    }
} 