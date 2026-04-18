<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NewsStatusLog extends Model {
    public $timestamps = false;
    protected $fillable = ['news_post_id', 'from_status', 'to_status', 'comment', 'changed_by', 'created_at'];
}