<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'pass' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400); // Bad Request
        }

        // Check If User Exists
        $currentUser = User::where('email', $request->email)->get();

        if (!$currentUser) {

            return response()->json([
                'error' => 'Invalid credentials',
            ], 401);
        }



        // Attempt to log the user in
        if (Auth::attempt(['email' => $request->email, 'password' => $request->pass])) {

            // Check If User Active
            $currentUser = User::where('email', $request->email)->firstOrFail();

            if ($currentUser->active === 0) {
                return response()->json([
                    'error' => 'This Account Is Inactive, Please Wait For The Administrator To Activate Your Account',
                ], 401); // Unauthorized
            }
            // Authentication passed
            $user = Auth::user();

            $token = $user->createToken('testA')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user
            ], 200); // OK
        }



        // Authentication failed
        return response()->json([
            'error' => 'Invalid credentials',
        ], 401); // Unauthorized
    }

    public function signup(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400); // Bad Request
        }

        // Create the new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'student',
            'active' => 0
        ]);

        return response()->json([
            'error' => 'Signup successful, Please Wait For The Administrator To Activate Your Account',
        ], 201); // Created
    }
}
