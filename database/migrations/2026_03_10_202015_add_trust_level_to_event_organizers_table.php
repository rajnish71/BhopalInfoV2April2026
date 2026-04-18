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
        Schema::table('event_organizers', function (Blueprint $table) {
            if (!Schema::hasColumn('event_organizers', 'trust_level')) {
                $table->enum('trust_level', ['new', 'verified', 'trusted'])->default('new');
                $table->index('trust_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_organizers', function (Blueprint $table) {
            if (Schema::hasColumn('event_organizers', 'trust_level')) {
                $table->dropIndex(['trust_level']);
                $table->dropColumn('trust_level');
            }
        });
    }
};
