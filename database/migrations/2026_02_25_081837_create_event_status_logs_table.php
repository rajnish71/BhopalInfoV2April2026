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
        Schema::create('event_status_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                  ->constrained('events')
                  ->cascadeOnDelete();

            $table->enum('action', [
                'created',
                'updated',
                'submitted',
                'approved',
                'published',
                'archived'
            ]);

            $table->foreignId('performed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_status_logs');
    }
};
