<?php

namespace App\Http\Controllers\WebPlatform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClubMember;
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
                'club_member.name',
                'club_member.student_id',
                'club_member.sunway_imail',
                'club_member.personal_email',
                'club_member.phone',
                'club_member.course_of_study',
                'club_member.created_at',
                'club.name as club_name'  // Getting the club name via join
            ])
                ->join('club', 'club_member.club_id', '=', 'club.id') // Joining with club table
                ->where('club_member.club_id', $user->club_id) // Filter by logged-in user's club_id
                ->get();
        } else {
            // If the user is not a club manager, they can view all members (you can customize this as needed)
            $members = ClubMember::select([
                'club_member.id',
                'club_member.name',
                'club_member.student_id',
                'club_member.sunway_imail',
                'club_member.personal_email',
                'club_member.phone',
                'club_member.course_of_study',
                'club_member.created_at',
                'club.name as club_name'
            ])
                ->join('club', 'club_member.club_id', '=', 'club.id')
                ->get();
        }

        // Return data as JSON
        return response()->json(['data' => $members]);
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
            // If there's an error, return a response with the error message
            return response()->json(['message' => 'Failed to delete member'], 500);
        }
    }

    public function getMember($id)
    {
        $member = ClubMember::find($id);
        return response()->json($member);
    }

    public function updateMember(Request $request, $id)
    {
        $member = ClubMember::find($id);
        $member->update($request->only(['name', 'student_id', 'sunway_imail', 'phone', 'personal_email', 'course_of_study']));
        return response()->json(['message' => 'Member updated successfully']);
    }

    public function addMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'required|string|unique:club_member',
            'sunway_imail' => 'required|email|unique:club_member',
            'phone' => 'required|string',
            'personal_email' => 'nullable|email',
            'course_of_study' => 'required|string'
        ]);

        $member = new ClubMember();
        $member->name = $request->name;
        $member->student_id = $request->student_id;
        $member->sunway_imail = $request->sunway_imail;
        $member->phone = $request->phone;
        $member->personal_email = $request->personal_email;
        $member->course_of_study = $request->course_of_study;
        $member->club_id = Auth::user()->club_id; // Assign club_id to the logged-in user's club
        $member->save();

        return response()->json(['message' => 'Member added successfully']);
    }
}
