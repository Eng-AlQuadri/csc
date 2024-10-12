<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Middleware\EnsureUserIsAuthenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('loginRegester');
});


Route::get('/login', function () {
    $user = auth()->user(); // Get the authenticated user

    // Check if the user is authenticated
    if ($user) {
        $type = $user->role; // Get the user's role
    } else {
        $type = 'nothing'; // Set a default value if no user is authenticated
    }
    return view('loginRegester', compact('type'));
});

Route::get('/admin', function () {
    return view('AdminLayout');
})->name('admin');

Route::get('/student', function () {
    return view('StudentLayout');
})->name('student');

Route::get('/load-view/{view}', function ($view) {
    return view($view);
})->name('load-view');

// Student Controller

Route::get('/students', [StudentController::class, 'index'])->name('students.index');

Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');

Route::post('/students', [StudentController::class, 'store'])->name('students.store');

Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');

Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');

Route::put('/students/activate/{id}', [StudentController::class, 'activate'])->name('students.activate');

// Subject Controller

Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');

Route::get('/assign-subjects-students', [SubjectController::class, 'getStudentsAndSubjects'])->name('assign.subjects.students');

Route::post('/assign-subjects', [SubjectController::class, 'assignSubject'])->name('assign.subject');

Route::get('/studentsubjects/{studentId}', [SubjectController::class, 'getSubjectsByStudent'])->name('subjectByStudent');

// Mark Controller

Route::post('/marks', [MarkController::class, 'store'])->name('marks.store');

Route::get('/marks/courses/{studentId}', [MarkController::class, 'getMarksWithCourses'])->name('MarksWithCourses');



// Login, Signup And Logout

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

Route::post('/logout', function () {

    Auth::logout();

    return response()->json('', 204);
})->name('logout');


///////////////////////////////////////////////////////////////////////////////

// // Public routes
// Route::get('/', fn() => view('loginRegester'));
// Route::get('/login', fn() => view('loginRegester'))->name('login');
// Route::post('/login', [AuthController::class, 'login'])->name('login');
// Route::post('/signup', [AuthController::class, 'signup'])->name('signup');


// // Logout route
// Route::post('/logout', function () {
//     Auth::logout();
//     return response()->json('', 204);
// })->name('logout');


// // Protected routes for admin only
// Route::middleware([EnsureUserIsAuthenticated::class . ':admin'])->group(function () {

//     Route::get('/admin', fn() => view('AdminLayout'))->name('admin');

//     Route::get('/load-view/{view}', function ($view) {
//         return view($view);
//     })->name('load-view');

//     Route::get('/students', [StudentController::class, 'index'])->name('students.index');

//     Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');

//     Route::post('/students', [StudentController::class, 'store'])->name('students.store');

//     Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');

//     Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');

//     Route::put('/students/activate/{id}', [StudentController::class, 'activate'])->name('students.activate');

//     // Subject Controller

//     Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');

//     Route::get('/assign-subjects-students', [SubjectController::class, 'getStudentsAndSubjects'])->name('assign.subjects.students');

//     Route::post('/assign-subjects', [SubjectController::class, 'assignSubject'])->name('assign.subject');

//     Route::get('/studentsubjects/{studentId}', [SubjectController::class, 'getSubjectsByStudent'])->name('subjectByStudent');

//     // Mark Controller

//     Route::post('/marks', [MarkController::class, 'store'])->name('marks.store');

//     Route::get('/marks/courses/{studentId}', [MarkController::class, 'getMarksWithCourses'])->name('MarksWithCourses');
// });

// // Protected routes for students
// Route::middleware(['auth.role:student'])->group(function () {

//     Route::get('/load-view/{view}', function ($view) {
//         return view($view);
//     })->name('load-view');

//     Route::get('/student', fn() => view('StudentLayout'))->name('student');

//     Route::get('/studentsubjects/{studentId}', [SubjectController::class, 'getSubjectsByStudent'])->name('subjectByStudent');

//     Route::get('/marks/courses/{studentId}', [MarkController::class, 'getMarksWithCourses'])->name('MarksWithCourses');

// });
