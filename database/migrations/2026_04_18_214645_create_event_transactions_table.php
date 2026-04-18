<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('registration_id')->constrained('event_attendees')->cascadeOnDelete();
            $table->unsignedBigInteger('organizer_id');
            $table->decimal('gross_amount', 10, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->string('payment_gateway_reference')->nullable();
            $table->enum('payment_status', ['disabled','pending','paid','failed','refunded'])->default('disabled');
            $table->enum('settlement_status', ['pending','locked','settled','adjustment_pending'])->default('pending');
            $table->dateTime('settled_at')->nullable();
            $table->timestamps();
            $table->index(['event_id', 'payment_status']);
            $table->index(['organizer_id', 'settlement_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_transactions');
    }
};
