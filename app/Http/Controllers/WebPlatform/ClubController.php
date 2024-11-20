<?php

namespace App\Http\Controllers\WebPlatform;

use App\Http\Controllers\Controller;
use App\Models\Club; // Assuming you have a Club model
use Illuminate\Http\Request;

class ClubController extends Controller
{
    public function getClub()
    {
        try {
            $clubs = Club::select('id', 'name')->get(); // Only fetch required columns
            return response()->json(['clubs' => $clubs], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch clubs data'], 500);
        }
    }
}
