<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubMember extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural form of the model name
    protected $table = 'club_member';

    // Define fillable properties to allow mass assignment
    protected $fillable = [
        'club_id',
        'name',
        'student_id',
        'sunway_imail',
        'personal_email',
        'phone',
        'course_of_study',
        'created_at'
    ];

    // Define the relationship to the Club model
    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
