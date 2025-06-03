<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'description',
        'poster_path',
        'duration',
        'genre',
        'language',
        'rating',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'decimal:1'
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
} 