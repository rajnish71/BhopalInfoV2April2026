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
        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                  ->constrained('events')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('ticket_category_id')
                  ->nullable()
                  ->constrained('event_ticket_categories')
                  ->nullOnDelete();

            $table->integer('quantity')->default(1);

            $table->enum('payment_status', [
                'pending',
                'paid',
                'cancelled'
            ])->default('pending');

            $table->string('transaction_reference')->nullable();

            $table->timestamps();

            $table->index(['event_id', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendees');
    }
};
