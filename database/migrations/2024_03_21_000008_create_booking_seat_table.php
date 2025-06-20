<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_seat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('seat_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 8, 2);
            $table->timestamps();

            // Ensure we don't have duplicate seat bookings
            $table->unique(['booking_id', 'seat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_seat');
    }
}; 