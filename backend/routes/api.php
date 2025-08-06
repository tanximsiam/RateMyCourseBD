<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ReviewController;


Route::get('/users', [UserController::class, 'index']); // for API testing
Route::get('/students', [StudentController::class, 'index']); // for API testing
Route::get('/departments', [DepartmentController::class, 'index']); // for API testing
Route::get('/universities', [UniversityController::class, 'index']); // for API testing
Route::get('/courses', [CourseController::class, 'index']); // for API testing
Route::get('/reviews', [ReviewController::class, 'index']); // for API testing

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [StudentController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Student
    Route::get('/me', [StudentController::class, 'me']);
    Route::put('/student/update-profile', [StudentController::class, 'updateProfile']);
    Route::put('/student/change-password', [StudentController::class, 'changePassword']);

    // User
    Route::put('/users/change-password', [UserController::class, 'changePassword']);
});
