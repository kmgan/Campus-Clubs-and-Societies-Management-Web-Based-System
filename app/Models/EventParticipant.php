<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    use HasFactory;

    protected $table = 'event_participant';

    protected $fillable = [
        'user_id',
        'event_id',
        'isPresent',
    ];

    protected $casts = [
        'isPresent' => 'boolean'
    ];
}
