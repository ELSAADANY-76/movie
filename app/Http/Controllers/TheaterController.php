<?php

namespace App\Http\Controllers;

use App\Models\Theater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TheaterController extends Controller
{
    public function index()
    {
        $theaters = Theater::with('seats')->get();
        return response()->json(['theaters' => $theaters]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'screen_type' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $theater = Theater::create($request->all());
        return response()->json(['theater' => $theater], 201);
    }

    public function show(Theater $theater)
    {
        return response()->json(['theater' => $theater->load('seats')]);
    }

    public function update(Request $request, Theater $theater)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'capacity' => 'integer|min:1',
            'screen_type' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $theater->update($request->all());
        return response()->json(['theater' => $theater]);
    }

    public function destroy(Theater $theater)
    {
        $theater->delete();
        return response()->json(null, 204);
    }
} 