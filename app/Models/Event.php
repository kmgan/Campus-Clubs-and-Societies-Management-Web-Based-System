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
        'club_id',
        'theme',
        'background_color',
        'text_color',
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

    public function participants()
    {
        return $this->hasMany(EventParticipant::class, 'event_id');
    }

    public function isUserRegistered($user)
    {
        return $this->participants()
            ->where('user_id', $user->id)
            ->whereIn('isApproved', [0, 1]) // You can check both pending (0) and approved (1) status
            ->exists();
    }
}
