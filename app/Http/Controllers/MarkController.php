<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MarkController extends Controller
{
    public function store(Request $request)
    {

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|integer',
            'student_id' => 'required|integer',
            'mark' => 'required|integer|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400); // Bad Request
        }

        $studentId = $request->student_id;
        $subjectId = $request->subject_id;
        $mark = $request->mark;

        // Check if the mark is already assigned to the student
        $exists = DB::table('marks')
            ->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'This mark is already assigned to the student.'
            ], 409); // 409 Conflict
        }

        // Assign the subject to the student
        DB::table('marks')->insert([
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'mark' => $mark,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['message' => 'Mark assigned successfully'], 201);
    }

    public function getMarksWithCourses($studentId)
    {

        $courses = DB::table('subjects')
            ->join('subject_students', 'subjects.id', '=', 'subject_students.subject_id')
            ->leftJoin('marks', function ($join) use ($studentId) {
                $join->on('subjects.id', '=', 'marks.subject_id')
                    ->where('marks.student_id', '=', $studentId);
            })
            ->where('subject_students.student_id', $studentId)
            ->select('subjects.name', 'subjects.minmark', 'marks.mark')
            ->get();

        return response()->json($courses);
    }
}
