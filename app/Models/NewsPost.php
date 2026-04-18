<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\NewsPublished;

class NewsPost extends Model {
    use SoftDeletes;
    
    // SAFE FIELDS ONLY
    protected $fillable = [
        'title', 'slug', 'summary', 'featured_image', 'content_blocks',
        'news_type', 'city_id', 'area_id', 'ward_id', 'category_id', 'source_id',
        'created_by', 'priority', 'is_alert'
    ];

    protected $casts = [
        'content_blocks' => 'array',
        'published_at' => 'datetime',
        'notifications_dispatched' => 'boolean'
    ];

    protected static function booted() {
        static::saving(function ($post) {
            if ($post->publish_status === 'published') {
                if ($post->verification_status !== 'verified') {
                    throw new \Exception("Cannot publish unverified news.");
                }
                if (empty($post->published_at)) $post->published_at = now();
            }
        });

        static::deleting(function ($post) {
            if ($post->publish_status === 'published' && !$post->isForceDeleting()) {
                throw new \Exception("Active civic records (Published posts) cannot be deleted. Change status to Archived first.");
            }
        });
    }

    // CONTROLLED ATTRIBUTE SETTERS (Bypass mass assignment)
    public function setStatus($status, $comment = '') { $this->publish_status = $status; $this->save(); }
    public function setVerification($status) { $this->verification_status = $status; $this->save(); }
    public function setUrgency($level) { $this->urgency_level = $level; $this->save(); }

    public function city() { return $this->belongsTo(City::class); }
    public function area() { return $this->belongsTo(Area::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function source() { return $this->belongsTo(Source::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function statusLogs() { return $this->hasMany(NewsStatusLog::class); }
}
