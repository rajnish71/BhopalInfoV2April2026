<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Area extends Model {
    protected $fillable = ['city_id', 'name', 'slug'];
    public function city() { return $this->belongsTo(City::class); }
    public function wards() { return $this->hasMany(Ward::class); }
}