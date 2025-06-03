<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('discount_type'); // percentage, fixed, etc.
            $table->decimal('discount_value', 10, 2);
            $table->string('promo_code')->unique()->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->json('applicable_movies')->nullable(); // Array of movie IDs this offer applies to
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
}; 