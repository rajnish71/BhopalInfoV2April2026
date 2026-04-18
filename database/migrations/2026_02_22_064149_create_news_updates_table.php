<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('news_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_post_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('news_updates');
    }
};