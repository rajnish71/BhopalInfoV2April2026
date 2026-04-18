<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'area_id',
        'ward_id',
        'event_category_id',
        'organizer_id',
        'title',
        'slug',
        'summary',
        'description',
        'venue_name',
        'venue_address',
        'start_datetime',
        'end_datetime',
        'event_type',
        'verification_status',
        'publish_status',
        'commission_percentage',
        'featured_image',
        'created_by',
        'approved_by',
        'view_count'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function organizer()
    {
        return $this->belongsTo(EventOrganizer::class);
    }

    public function tickets()
    {
        return $this->hasMany(EventTicketCategory::class);
    }

    public function attendees()
    {
        return $this->hasMany(EventAttendee::class);
    }

    public function notifications()
    {
        return $this->hasMany(EventNotification::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(EventStatusLog::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('publish_status', 'published')
                     ->where('verification_status', 'verified');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>=', now());
    }
}