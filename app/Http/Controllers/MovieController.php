<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::where('is_active', true)->latest()->paginate(12);
        return view('movies.index', compact('movies'));
    }

    public function show(Movie $movie)
    {
        $showtimes = $movie->showtimes()
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();
        return view('movies.show', compact('movie', 'showtimes'));
    }

    public function create()
    {
        $this->authorize('create', Movie::class);
        return view('movies.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Movie::class);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'poster_path' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'duration' => 'required|string',
            'genre' => 'required|string',
            'language' => 'required|string',
        ]);

        if ($request->hasFile('poster_path')) {
            $path = $request->file('poster_path')->store('posters', 'public');
            $validated['poster_path'] = $path;
        }

        Movie::create($validated);

        return redirect()->route('movies.index')
            ->with('success', 'Movie added successfully');
    }

    public function edit(Movie $movie)
    {
        $this->authorize('update', $movie);
        return view('movies.edit', compact('movie'));
    }

    public function update(Request $request, Movie $movie)
    {
        $this->authorize('update', $movie);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'poster_path' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'duration' => 'required|string',
            'genre' => 'required|string',
            'language' => 'required|string',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('poster_path')) {
            $path = $request->file('poster_path')->store('posters', 'public');
            $validated['poster_path'] = $path;
        }

        $movie->update($validated);

        return redirect()->route('movies.index')
            ->with('success', 'Movie updated successfully');
    }

    public function destroy(Movie $movie)
    {
        $this->authorize('delete', $movie);
        $movie->delete();
        return redirect()->route('movies.index')
            ->with('success', 'Movie deleted successfully');
    }
} 