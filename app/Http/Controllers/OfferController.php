<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::with('movies')->get();
        return response()->json(['offers' => $offers]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'promo_code' => 'nullable|string|unique:offers',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'image' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'applicable_movies' => 'nullable|array',
            'movie_ids' => 'nullable|array|exists:movies,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $offer = Offer::create($request->except('movie_ids'));
        
        if ($request->has('movie_ids')) {
            $offer->movies()->attach($request->movie_ids);
        }

        return response()->json(['offer' => $offer->load('movies')], 201);
    }

    public function show(Offer $offer)
    {
        return response()->json(['offer' => $offer->load('movies')]);
    }

    public function update(Request $request, Offer $offer)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'discount_type' => 'in:percentage,fixed',
            'discount_value' => 'numeric|min:0',
            'promo_code' => 'nullable|string|unique:offers,promo_code,' . $offer->id,
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'is_active' => 'boolean',
            'image' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'applicable_movies' => 'nullable|array',
            'movie_ids' => 'nullable|array|exists:movies,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $offer->update($request->except('movie_ids'));
        
        if ($request->has('movie_ids')) {
            $offer->movies()->sync($request->movie_ids);
        }

        return response()->json(['offer' => $offer->load('movies')]);
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return response()->json(null, 204);
    }

    public function validatePromoCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'promo_code' => 'required|string',
            'movie_id' => 'required|exists:movies,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $offer = Offer::where('promo_code', $request->promo_code)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$offer) {
            return response()->json(['message' => 'Invalid or expired promo code'], 404);
        }

        if ($offer->applicable_movies && !in_array($request->movie_id, $offer->applicable_movies)) {
            return response()->json(['message' => 'This promo code is not applicable for this movie'], 400);
        }

        return response()->json([
            'offer' => $offer,
            'discount_amount' => $this->calculateDiscount($offer, $request->price ?? 0)
        ]);
    }

    private function calculateDiscount(Offer $offer, float $price): float
    {
        if ($offer->discount_type === 'percentage') {
            return ($price * $offer->discount_value) / 100;
        }
        return min($offer->discount_value, $price);
    }
} 