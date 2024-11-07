<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubCategory extends Model
{
    // Specify the table name since it's singular
    protected $table = 'club_category';

    // The attributes that are mass assignable
    protected $fillable = [
        'name'
    ];

}

