<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username', // Add username
        'name',
        'email', // Add Sunway iMail
        'student_id', // Add student ID
        'personal_email', // Add personal email
        'phone', // Add phone
        'course_of_study', // Add course of study
        'club_id', // Add club_id for club association
        'password', // Ensure password is included for account creation
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relationship with Club.
     */
    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }


    /**
     * Relationship with Event.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_participant');
    }
}
