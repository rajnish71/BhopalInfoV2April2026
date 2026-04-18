<?php
namespace App\Events;
use App\Models\NewsPost;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewsPublished {
    use Dispatchable, SerializesModels;
    public $post;
    public function __construct(NewsPost $post) { $this->post = $post; }
}