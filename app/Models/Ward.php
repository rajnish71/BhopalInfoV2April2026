<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model {
    protected $fillable = ['area_id', 'number', 'name'];
    public function area() { return $this->belongsTo(Area::class); }
}