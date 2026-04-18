<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // 1. Rename columns if they exist
            if (Schema::hasColumn('events', 'event_category_id')) {
                $table->renameColumn('event_category_id', 'category_id');
            }
            if (Schema::hasColumn('events', 'venue_name')) {
                $table->renameColumn('venue_name', 'venue');
            }
            if (Schema::hasColumn('events', 'venue_address')) {
                $table->renameColumn('venue_address', 'address');
            }
            if (Schema::hasColumn('events', 'featured_image')) {
                $table->renameColumn('featured_image', 'cover_image');
            }

            // 2. Add missing fields
            if (!Schema::hasColumn('events', 'map_url')) {
                $table->string('map_url')->nullable()->after('address');
            }
            
            // approved_by already exists based on research, but double check
            if (!Schema::hasColumn('events', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('created_by');
            }

            // 3. Enable soft deletes
            if (!Schema::hasColumn('events', 'deleted_at')) {
                $table->softDeletes();
            }

            // 4. Add composite index
            // We use a safe check by dropping if exists or catching error if we want, 
            // but Schema doesn't have hasIndex easily in all Laravel versions.
            // Based on research, it does not exist.
        });

        Schema::table('events', function (Blueprint $table) {
            $table->index(['publish_status', 'verification_status', 'start_datetime'], 'events_public_visibility_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_public_visibility_index');
            $table->dropSoftDeletes();
            $table->dropColumn(['map_url']);
            
            $table->renameColumn('category_id', 'event_category_id');
            $table->renameColumn('venue', 'venue_name');
            $table->renameColumn('address', 'venue_address');
            $table->renameColumn('cover_image', 'featured_image');
        });
    }
};
