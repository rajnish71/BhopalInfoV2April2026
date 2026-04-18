<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('news_posts', function (Blueprint $table) {
            // Index for city-wide feeds
            $table->index(['city_id', 'publish_status', 'published_at'], 'news_city_publish_idx');
            
            // Index for area-specific feeds
            $table->index(['area_id', 'publish_status', 'published_at'], 'news_area_publish_idx');
            
            // Index for critical alerts
            $table->index(['urgency_level', 'publish_status'], 'news_urgency_publish_idx');
        });
    }
    public function down(): void {
        Schema::table('news_posts', function (Blueprint $table) {
            $table->dropIndex('news_city_publish_idx');
            $table->dropIndex('news_area_publish_idx');
            $table->dropIndex('news_urgency_publish_idx');
        });
    }
};