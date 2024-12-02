<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Club extends Model
{
    // Specify the table name since it's singular
    protected $table = 'club';

    // The attributes that are mass assignable
    protected $fillable = [
        'name',
        'logo',
        'description',
        'category_id',
        'about_us_img',
        'join_description',
        'membership_fee',
        'email',
        'instagram_url',
        'facebook_url',
        'linkedin_url'
    ];

    // Define the relationship to events (A club can have many events)
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function club_activity()
    {
        return $this->hasMany(ClubActivity::class);
    }

    public function manager()
    {
        return $this->hasOne(User::class, 'club_id')->whereHas('roles', function ($query) {
            $query->where('name', 'club_manager');
        });
    }

    public function members()
    {
        return $this->hasMany(ClubMember::class, 'club_id', 'id');
    }

    public function club_category()
    {
        return $this->belongsTo(ClubCategory::class, 'category_id', 'id');
    }

    public function club_gallery()
    {
        return $this->hasMany(ClubGallery::class, 'club_id');
    }

    public function hasUserJoined($user)
    {
        // Check if the user has joined the club and whether they are approved
        $membership = $this->members()->where('user_id', $user->id)->first();

        if ($membership) {
            return $membership->isApproved;
        }

        // If no membership exists, return null or a default value
        return null;
    }
}
