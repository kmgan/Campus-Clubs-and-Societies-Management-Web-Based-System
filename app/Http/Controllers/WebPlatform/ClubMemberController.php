<?php

namespace App\Http\Controllers\WebPlatform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClubMember;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ClubMemberController extends Controller
{
    public function showMemberPage()
    {
        return view('webplatform.member');
    }

    public function getClubMembersData()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if ($user->hasRole('club_manager')) {
            // Fetch only members that belong to the manager's club
            $members = ClubMember::select([
                'club_member.id',
                'users.name',
                'users.student_id',
                'users.email',
                'club_member.role',
                'users.personal_email',
                'users.phone',
                'users.course_of_study',
                'club_member.created_at',
                'club.name as club_name'
            ])
                ->join('users', 'club_member.user_id', '=', 'users.id') // Join with users table
                ->join('club', 'club_member.club_id', '=', 'club.id') // Join with club table
                ->where('club_member.club_id', $user->club_id)
                ->where('club_member.isApproved', 1)
                ->get();
        }

        // Return data as JSON
        return response()->json(['data' => $members]);
    }
    public function getPendingMember()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if ($user->hasRole('club_manager')) {
            // Fetch only pending members that belong to the manager's club
            $members = ClubMember::select([
                'club_member.id',
                'users.name',
                'users.student_id'
            ])
                ->join('users', 'club_member.user_id', '=', 'users.id')
                ->where('club_member.club_id', $user->club_id)
                ->where('club_member.isapproved', 0)
                ->get();

            return response()->json(['members' => $members]);
        }

        return response()->json(['members' => []]);
    }

    public function approveMember($id)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Ensure the user is a club manager
        if ($user->hasRole('club_manager')) {
            // Find the club member and ensure they belong to the club managed by the authenticated user
            $member = ClubMember::where('id', $id)
                ->where('club_id', $user->club_id)
                ->where('isapproved', 0)
                ->first();

            if ($member) {
                // Approve the member
                $member->isapproved = 1;
                $member->save();

                return response()->json(['message' => 'Member approved successfully.']);
            } else {
                return response()->json(['message' => 'Member not found or already approved.'], 404);
            }
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    public function rejectMember($id)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Ensure the user is a club manager
        if ($user->hasRole('club_manager')) {
            // Find the club member and ensure they belong to the club managed by the authenticated user
            $member = ClubMember::where('id', $id)
                ->where('club_id', $user->club_id)
                ->where('isapproved', 0)
                ->first();

            if ($member) {
                // Delete the member (reject)
                $member->delete();

                return response()->json(['message' => 'Member rejected successfully.']);
            } else {
                return response()->json(['message' => 'Member not found or already processed.'], 404);
            }
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    public function deleteMember($id)
    {
        // Find the member by id
        $member = ClubMember::find($id);

        // Check if the member exists
        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        // Attempt to delete the member
        try {
            $member->delete();
            return response()->json(['message' => 'Member deleted successfully'], 200);
        } catch (\Exception $e) {
            // Return an error response if deletion fails
            return response()->json(['message' => 'Failed to delete member'], 500);
        }
    }

    public function updateMember(Request $request, $id)
    {
        // Validate that only the role is updated
        $request->validate([
            'role' => 'required|string'
        ]);

        // Find the member by id
        $member = ClubMember::find($id);

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        // Update the role
        $member->role = $request->role;
        $member->save();

        return response()->json(['message' => 'Member role updated successfully']);
    }

    public function updateRole(Request $request, $id)
    {
        // Validate the incoming role value
        $request->validate([
            'role' => 'required|string|in:President,Vice President,Secretary,Treasurer,Public Relations,Event,Media,Member'
        ]);

        // Find the club member by ID
        $member = ClubMember::find($id);

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        // Enforce the rule: Only one President and one Vice President per club
        if (in_array($request->role, ['President', 'Vice President'])) {
            $existingMember = ClubMember::where('club_id', $member->club_id)
                ->where('role', $request->role)
                ->first();

            if ($existingMember && $existingMember->id !== $id) {
                return response()->json([
                    'message' => "Only one {$request->role} is allowed per club."
                ], 422);
            }
        }

        // Update the role
        $member->role = $request->role;
        $member->save();

        // Return success response
        return response()->json(['message' => 'Role updated successfully']);
    }
}
