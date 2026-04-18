<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_seat_locks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->dateTime('locked_until');
            $table->enum('status', ['active', 'expired', 'converted'])->default('active');
            $table->timestamps();
            $table->index(['event_id', 'locked_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_seat_locks');
    }
};
