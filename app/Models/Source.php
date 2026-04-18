<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Source extends Model {
    protected $fillable = ['name', 'type', 'website', 'contact_person'];
}