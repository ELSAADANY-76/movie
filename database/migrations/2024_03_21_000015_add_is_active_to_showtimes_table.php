<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            if (!Schema::hasColumn('showtimes', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            if (Schema::hasColumn('showtimes', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
}; 