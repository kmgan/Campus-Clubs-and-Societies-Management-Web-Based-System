<?php

namespace App\Http\Controllers\WebPlatform;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function showClubPage(Request $request)
    {
        // Get filters from the request
        $joinStatus = $request->input('join_status', 'all');
        $categoryId = $request->input('category_id', null);

        /** @var \App\Models\User */
        $user = auth()->user();

        // Start building the query
        $query = Club::query()
            ->with('members') // Eager load members to prevent N+1 queries
            ->withCount(['members as members_count' => function ($q) {
                $q->where('isApproved', 1);
            }]);

        if ($user->hasRole('user')) {
            if ($joinStatus == 'joined') {
                // Filter clubs where the user is already a member
                $query->whereHas('members', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($joinStatus == 'available') {
                // Filter clubs where the user is not a member
                $query->whereDoesntHave('members', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        // Filter clubs by category, if provided
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $clubs = $query->withCount(['members as members_count' => function ($q) {
            $q->where('isApproved', 1);  // Only count approved members
        }])->get();

        // Fetch all categories for the dropdown filter
        $categories = ClubCategory::all();

        // Return the view with the filtered clubs and categories
        return view('webplatform.club', compact('clubs', 'categories'));
    }

    public function register(Request $request)
    {
        $userId = Auth::id(); // Get the authenticated user's ID
        $clubId = $request->input('club_id'); // Get the club ID from the request

        // Check if the user is already registered to the club
        $existingRegistration = DB::table('club_member')
            ->where('user_id', $userId)
            ->where('club_id', $clubId)
            ->first();

        if ($existingRegistration) {
            return response()->json([
                'success' => false,
                'message' => 'You are already a member of this club.'
            ]);
        }

        // Register the user for the club
        DB::table('club_member')->insert([
            'user_id' => $userId,
            'club_id' => $clubId,
            'isApproved' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined the club!'
        ]);
    }

    public function deleteClub(Request $request, $clubId)
    {
        // Fetch the club by its ID
        $club = Club::find($clubId);

        if (!$club) {
            return response()->json([
                'success' => false,
                'message' => 'Club not found.'
            ], 404);
        }

        // Check if the club has an associated club manager
        $clubManager = User::where('club_id', $club->id)
            ->role('club_manager') // Ensure the user is a club manager
            ->first();

        if ($clubManager) {
            return response()->json([
                'success' => false,
                'message' => 'This club has an associated club manager account. Please delete the manager account before deleting the club.'
            ]);
        }

        try {
            // Delete the club and its related data
            $club->delete();

            return response()->json([
                'success' => true,
                'message' => 'Club deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the club. Please try again later.'
            ], 500);
        }
    }

    public function createClub(Request $request)
    {
        // Validate the input data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:club,name',
            'category_id' => 'required|exists:club_category,id', // Ensure the category exists
        ]);

        try {
            // Create the club
            $club = Club::create([
                'name' => $validatedData['name'],
                'category_id' => $validatedData['category_id'],
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Club created successfully!',
                'club' => $club,
            ], 201); // 201 for resource creation
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error creating club: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the club. Please try again later.',
            ], 500);
        }
    }
}
