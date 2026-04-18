<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NewsUpdate extends Model {
    protected $fillable = ['news_post_id', 'content', 'updated_by'];
    public function post() { return $this->belongsTo(NewsPost::class, 'news_post_id'); }
}