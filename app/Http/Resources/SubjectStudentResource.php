<?php

namespace App\Http\Resources;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectStudentResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request)
    {
        return [
            'id',
            'subject_id',
            'student_id'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
