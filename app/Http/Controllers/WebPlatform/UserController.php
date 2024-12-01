<?php

namespace App\Http\Controllers\WebPlatform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function showUserPage()
    {
        return view('webplatform.user');
    }

    public function getUsersData()
    {
        $users = User::select([
            'users.id',
            'users.name',
            'users.email', // Rename for clarity in JSON response
            'users.student_id',
            'users.personal_email',
            'users.phone',
            'users.course_of_study',
            'roles.name as role',
            'club.name as club_name'
        ])
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftJoin('club', 'users.club_id', '=', 'club.id')
            ->get();

        return response()->json(['data' => $users]);
    }


    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        try {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user'], 500);
        }
    }

    public function getUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function updateUser(Request $request, $id)
    {
        // Validation for update
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'student_id' => 'nullable|integer|unique:users,student_id,' . $id,
            'personal_email' => 'nullable|email|unique:users,personal_email,' . $id,
            'phone' => 'nullable|string|max:15',
            'course_of_study' => 'nullable|string|max:255',
            'role' => 'required|string',
            'club_id' => 'nullable|integer|exists:club,id'
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update fields except username and password
        $user->update($request->only([
            'name',
            'email',
            'student_id',
            'personal_email',
            'phone',
            'course_of_study',
            'club_id'
        ]));

        $user->syncRoles($request->input('role')); // Update the user's role

        return response()->json(['message' => 'User updated successfully']);
    }


    public function addUser(Request $request)
    {
        // Validation for add user
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'student_id' => 'nullable|integer|unique:users',
            'personal_email' => 'nullable|email|unique:users',
            'phone' => 'nullable|string|max:15',
            'course_of_study' => 'nullable|string|max:255',
            'role' => 'required|string',
            'club_id' => 'nullable|integer|exists:club,id'
        ]);

        // Create new user
        $user = new User();
        $user->username = $request->username; // Add username
        $user->name = $request->name;
        $user->email = $request->email;
        $user->student_id = $request->student_id;
        $user->personal_email = $request->personal_email;
        $user->phone = $request->phone;
        $user->course_of_study = $request->course_of_study;
        $user->club_id = $request->club_id;
        $user->password = bcrypt('a'); // Default password for the user
        $user->save();

        // Assign the selected role to the user
        $user->assignRole($request->role);

        return response()->json(['message' => 'User added successfully']);
    }


    public function getRole()
    {
        try {
            $roles = Role::select('id', 'name')->get();
            return response()->json(['roles' => $roles], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch roles'], 500);
        }
    }
}
