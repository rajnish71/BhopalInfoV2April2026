<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE event_organizers MODIFY COLUMN trust_level ENUM('new','verified','trusted','restricted') NOT NULL DEFAULT 'new'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE event_organizers MODIFY COLUMN trust_level ENUM('new','verified','trusted') NOT NULL DEFAULT 'new'");
    }
};
