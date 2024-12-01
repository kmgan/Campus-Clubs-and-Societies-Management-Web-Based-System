<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubMember extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural form of the model name
    protected $table = 'club_member';

    protected $fillable = [
        'club_id',
        'role',
        'isApproved', // Add this line to make 'isApproved' mass-assignable
        'created_at',
        'updated_at',
        'user_id'
    ];

    // Define the relationship to the Club model
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
