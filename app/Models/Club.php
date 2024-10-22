<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    // Specify the table name since it's singular
    protected $table = 'club';

    // The attributes that are mass assignable
    protected $fillable = [
        'name', 
        'logo', 
        'category'
    ];

    // Define the relationship to events (A club can have many events)
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}

