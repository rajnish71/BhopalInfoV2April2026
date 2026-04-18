<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Media extends Model
{
    public $timestamps = false;
    protected $fillable = ['file_path', 'alt_text', 'uploaded_by', 'created_at'];
    public function uploader() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}