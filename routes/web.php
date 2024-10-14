<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;



// Public Routes
Route::get('/', fn() => view('loginRegester'));  // Login & Register Screen

Route::get('/login', fn() => view('loginRegester'))->name('login');  // Login & Register Screen

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Admin Routes
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {

    Route::get('/', fn() => view('AdminLayout'))->name('admin');

    Route::get('/admin/load-view/{view}', fn($view) => view($view))->name('admin.load-view');

    Route::get('/admin/students', [StudentController::class, 'index'])->name('admin.students.index');

    Route::get('/admin/students/{id}', [StudentController::class, 'show'])->name('admin.students.show');

    Route::post('/admin/students', [StudentController::class, 'store'])->name('admin.students.store');

    Route::delete('/admin/students/{id}', [StudentController::class, 'destroy'])->name('admin.students.destroy');

    Route::put('/admin/students/{id}', [StudentController::class, 'update'])->name('admin.students.update');

    Route::put('/admin/students/activate/{id}', [StudentController::class, 'activate'])->name('admin.students.activate');

    // Subject Controller

    Route::post('/admin/subjects', [SubjectController::class, 'store'])->name('admin.subjects.store');

    Route::get('/admin/assign-subjects-students', [SubjectController::class, 'getStudentsAndSubjects'])->name('admin.assign.subjects.students');

    Route::post('/admin/assign-subjects', [SubjectController::class, 'assignSubject'])->name('admin.assign.subject');

    Route::get('/admin/studentsubjects/{studentId}', [SubjectController::class, 'getSubjectsByStudent'])->name('admin.subjectByStudent');

    // Mark Controller

    Route::post('/admin/marks', [MarkController::class, 'store'])->name('admin.marks.store');

    Route::get('/admin/marks/courses/{studentId}', [MarkController::class, 'admin.getMarksWithCourses'])->name('MarksWithCourses');

    // Message Controller
    Route::get('/admin/messages/{id}', [MessageController::class, 'index'])->name('admin.message.index');
});


// Student Routes
Route::group(['prefix' => 'student', 'middleware' => 'student'], function () {

    Route::get('/', fn() => view('StudentLayout'))->name('student');

    Route::get('/student/load-view/{view}', fn($view) => view($view))->name('student.load-view');

    Route::get('/student', fn() => view('StudentLayout'))->name('student');

    Route::get('/studentsubjects/{studentId}', [SubjectController::class, 'getSubjectsByStudent'])->name('subjectByStudent');

    Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');

    Route::get('/marks/courses/{studentId}', [MarkController::class, 'getMarksWithCourses'])->name('MarksWithCourses');

    Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');

    Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
});
