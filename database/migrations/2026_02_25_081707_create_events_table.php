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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('city_id')
                  ->constrained('cities')
                  ->cascadeOnDelete();

            $table->foreignId('area_id')
                  ->nullable()
                  ->constrained('areas')
                  ->nullOnDelete();

            $table->foreignId('ward_id')
                  ->nullable()
                  ->constrained('wards')
                  ->nullOnDelete();

            $table->foreignId('event_category_id')
                  ->constrained('event_categories')
                  ->restrictOnDelete();

            $table->foreignId('organizer_id')
                  ->constrained('event_organizers')
                  ->cascadeOnDelete();

            $table->string('title', 255);
            $table->string('slug')->unique();
            $table->string('summary', 300)->nullable();
            $table->longText('description');

            $table->string('venue_name');
            $table->text('venue_address')->nullable();

            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime')->nullable();

            $table->enum('event_type', ['free', 'paid', 'public'])
                  ->default('public');

            $table->enum('verification_status', ['pending', 'verified', 'rejected'])
                  ->default('pending');

            $table->enum('publish_status', ['draft', 'review', 'published', 'archived'])
                  ->default('draft');

            $table->decimal('commission_percentage', 5, 2)->nullable();
            $table->string('featured_image')->nullable();

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->unsignedBigInteger('view_count')->default(0);

            $table->timestamps();

            $table->index(['city_id', 'area_id', 'publish_status']);
            $table->index(['start_datetime', 'publish_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
