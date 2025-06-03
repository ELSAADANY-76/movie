<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            if (!Schema::hasColumn('movies', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_showing');
            }
            if (!Schema::hasColumn('movies', 'cast')) {
                $table->text('cast')->nullable()->after('director');
            }
        });
    }

    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            if (Schema::hasColumn('movies', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
            if (Schema::hasColumn('movies', 'cast')) {
                $table->dropColumn('cast');
            }
        });
    }
}; 