<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NewsNotification extends Model {
    protected $fillable = ['news_post_id', 'type', 'status', 'sent_at'];
}