<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_movie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Ensure each offer-movie combination is unique
            $table->unique(['offer_id', 'movie_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_movie');
    }
}; 