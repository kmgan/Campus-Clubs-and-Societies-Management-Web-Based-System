<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubGallery extends Model
{
    // Specify the table name since it's singular
    protected $table = 'club_gallery';

    // The attributes that are mass assignable
    protected $fillable = [
        'gallery_img_url',
        'club_id'
    ];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id', 'id');
    }

}