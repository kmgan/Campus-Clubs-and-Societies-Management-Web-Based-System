<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    // Specify the table name explicitly since it's singular
    protected $table = 'event';

    // The attributes that are mass assignable
    protected $fillable = [
        'name',
        'poster',
        'description',
        'location',
        'date',
        'start_time',
        'end_time',
        'club_id'
    ];

    // Specify any custom attributes that shouldn't be cast to default types
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    // Define the relationship to the club
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function participant()
    {
        return $this->belongsToMany(User::class, 'event_participant');
    }
}
