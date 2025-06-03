<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Only add columns that don't exist
            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('bookings', 'is_guest')) {
                $table->boolean('is_guest')->default(false);
            }
            if (!Schema::hasColumn('bookings', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'email')) {
                $table->string('email')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'amount_paid',
                'is_guest',
                'name',
                'email'
            ]);
        });
    }
}; 