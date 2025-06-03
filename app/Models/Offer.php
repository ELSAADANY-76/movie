<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Offer extends Model
{
    protected $fillable = [
        'title',
        'description',
        'discount_type',
        'discount_value',
        'promo_code',
        'start_date',
        'end_date',
        'is_active',
        'image',
        'terms_conditions',
        'applicable_movies',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'applicable_movies' => 'array',
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'offer_movie')
            ->withTimestamps();
    }

    public function isActive(): bool
    {
        return $this->is_active && 
               now()->between($this->start_date, $this->end_date);
    }
} 