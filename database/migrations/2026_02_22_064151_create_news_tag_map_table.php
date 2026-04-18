<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('news_tag_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('news_tag_map');
    }
};