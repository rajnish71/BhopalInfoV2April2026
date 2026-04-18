<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('news_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->string('featured_image')->nullable();
            $table->json('content_blocks'); // For structured content
            
            $table->string('news_type')->default('Routine Civic Update');
            
            $table->foreignId('city_id')->constrained();
            $table->foreignId('area_id')->nullable()->constrained();
            $table->foreignId('ward_id')->nullable()->constrained();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('source_id')->constrained();
            
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            
            $table->enum('publish_status', ['draft', 'review', 'approved', 'published', 'archived'])->default('draft');
            $table->enum('verification_status', ['unverified', 'pending', 'verified', 'rejected'])->default('unverified');
            $table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->default('low');
            
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['city_id', 'area_id', 'publish_status', 'urgency_level'], 'news_composite_lookup');
        });
    }
    public function down(): void {
        Schema::dropIfExists('news_posts');
    }
};