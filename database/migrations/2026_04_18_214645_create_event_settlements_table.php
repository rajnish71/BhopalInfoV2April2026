<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizer_id');
            $table->decimal('total_gross', 12, 2)->default(0);
            $table->decimal('total_commission', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);
            $table->enum('settlement_status', [
                'pending','locked','simulated','processing','settled','failed'
            ])->default('pending');
            $table->dateTime('locked_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->dateTime('settled_at')->nullable();
            $table->string('simulation_reference')->nullable();
            $table->string('payout_reference')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['organizer_id', 'settlement_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_settlements');
    }
};
