<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubActivity extends Model
{
    // Specify the table name since it's singular
    protected $table = 'club_activity';

    // The attributes that are mass assignable
    protected $fillable = [
        'name',
        'activity_img_url',
        'description',
        'club_id'
    ];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id', 'id');
    }

}