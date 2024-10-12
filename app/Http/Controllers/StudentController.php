<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index()
    {

        $students = User::where('role', 'student')->get();

        // Return students as a JSON response (or you can return a view if needed)
        return response()->json($students);
    }

    public function show($studentId)
    {

        $student = User::find($studentId);

        return response()->json($student);
    }

    public function store(Request $request)
    {

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400); // Bad Request
        }

        // Create New Student
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'student',
            'active' => 0
        ]);

        return response()->json('', 201); // Created
    }

    public function update($studentId)
    {

        $name = request()->name;

        $email = request()->email;

        $student = User::findOrFail($studentId);

        $student->update([
            'name' => $name,
            'email' => $email,
        ]);

        return response()->json('', 201);
    }

    public function activate($studentId)
    {

        $student = User::findOrFail($studentId);

        $student->active = 1;

        $student->save();

        return response()->json('', 201);
    }

    public function destroy($studentId)
    {
        $student = User::findOrFail($studentId);

        $student->delete();

        return response()->json('', 204);
    }
}
