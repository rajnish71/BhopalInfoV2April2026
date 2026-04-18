<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model {
    public $timestamps = false;
    protected $fillable = ['name', 'slug', 'type', 'status', 'pillar_type'];
    protected static function booted() {
        static::saving(function ($category) {
            if (empty($category->slug)) $category->slug = Str::slug($category->name);
        });
    }
}
