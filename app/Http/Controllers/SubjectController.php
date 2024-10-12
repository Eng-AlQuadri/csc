<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SubjectStudents;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    public function store(Request $request)
    {

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:subjects,name',
            'minmark' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400); // Bad Request
        }

        // Create New Student
        $user = Subject::create([
            'name' => $request->name,
            'minmark' => $request->minmark
        ]);

        return response()->json('', 201); // Created
    }

    // Fetch all students and subjects
    public function getStudentsAndSubjects()
    {
        $students = User::where('role', 'student')->get();
        $subjects = Subject::all();

        return response()->json([
            'students' => $students,
            'subjects' => $subjects
        ]);
    }

    public function assignSubject(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer',
            'subject_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400); // Bad Request
        }

        $studentId = $request->student_id;
        $subjectId = $request->subject_id;

        // Check if the subject is already assigned to the student
        $exists = DB::table('subject_students')
            ->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'This subject is already assigned to the student.'
            ], 409); // 409 Conflict
        }

        // Assign the subject to the student
        DB::table('subject_students')->insert([
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['message' => 'Subject assigned successfully'], 201);
    }

    public function getSubjectsByStudent($id)
    {
        $subjects = SubjectStudents::where('student_id', $id)->get(); // Assuming relation is defined

        $subjects->load('subject');

        return response()->json(['subjects' => $subjects]);
    }
}
