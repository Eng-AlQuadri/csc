<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function store(Request $request)
    {

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'students' => 'required|array',
            'students.*' => 'exists:users,id',
            'group_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400); // Bad Request
        }

        // Create a new group
        $group = Group::create(['name' => $request->group_name]);

        // Attach students to the group
        $group->users()->attach($request->students);

        return response()->json(['message' => 'Group created and students assigned successfully!']);
    }

    public function getGroupByUserId($userId)
    {
        // Fetch the groups associated with the user
        $groups = Group::whereHas('users', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->get();

        // Return the groups in a JSON response
        return response()->json($groups);
    }

    public function getGroupName($groupId)
    {
        $group = Group::findOrFail($groupId);
        return response()->json($group->name);
    }
}
