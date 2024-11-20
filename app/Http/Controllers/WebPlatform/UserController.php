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
        /** @var \App\Models\User */
        $user = Auth::user();

        if ($user->hasRole('club_manager')) {
            // Fetch only users that belong to the manager's club
            $users = User::select([
                'users.id',
                'users.name',
                'users.email',
                'roles.name as role',
                'club.name as club_name'  // Getting the club name via join
            ])
                ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->leftJoin('club', 'users.club_id', '=', 'club.id') // Joining with club table
                ->where('users.club_id', $user->club_id) // Filter by logged-in user's club_id
                ->get();
        } else {
            // If the user is not a club manager, they can view all users
            $users = User::select([
                'users.id',
                'users.name',
                'users.email',
                'roles.name as role',
                'club.name as club_name'
            ])
                ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->leftJoin('club', 'users.club_id', '=', 'club.id')
                ->get();
        }

        // Return data as JSON
        return response()->json(['data' => $users]);
    }

    public function deleteUser($id)
    {
        // Find the user by id
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Attempt to delete the user
        try {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            // If there's an error, return a response with the error message
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|string',
            'club_id' => 'nullable|integer|exists:clubs,id'
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update($request->only(['name', 'email', 'club_id']));
        $user->syncRoles($request->input('role')); // Update user role

        return response()->json(['message' => 'User updated successfully']);
    }

    public function addUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|string',
            'club_id' => 'nullable|integer|exists:clubs,id'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt('a'); // Set a default password (in your case, "a")
        $user->club_id = $request->club_id;
        $user->save();

        $user->assignRole($request->role); // Assign role to user

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
