<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                // Check if index already exists
                $indexExists = collect(DB::select("SHOW INDEX FROM events"))
                    ->pluck('Key_name')
                    ->contains('events_public_visibility_index');

                if (!$indexExists) {
                    $table->index(['publish_status', 'verification_status', 'start_datetime'], 'events_public_visibility_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                $indexExists = collect(DB::select("SHOW INDEX FROM events"))
                    ->pluck('Key_name')
                    ->contains('events_public_visibility_index');

                if ($indexExists) {
                    $table->dropIndex('events_public_visibility_index');
                }
            });
        }
    }
};
