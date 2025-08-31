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
use App\Http\Controllers\ReviewVoteController;
use App\Http\Controllers\CourseOutlineController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CourseSuggestionController;
use App\Http\Controllers\ModerationController;


Route::get('/users', [UserController::class, 'index']); // for API testing
Route::get('/students', [StudentController::class, 'index']); // for API testing
Route::get('/departments', [DepartmentController::class, 'index']); // for API testing
Route::get('/universities', [UniversityController::class, 'index']); // for API testing
Route::get('/courses', [CourseController::class, 'index']); // for API testing
Route::get('/reviews', [ReviewController::class, 'index']); // for API testing


Route::get('/courses/{id}/reviews/details', [ReviewController::class, 'courseReviews']);

Route::get('/courses/{id}/outlines', [CourseOutlineController::class, 'index']);

Route::get('/reviews/{review}/comments', [CommentController::class, 'index']);

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

    // Review
    Route::post('/submit-review', [ReviewController::class, 'store']);
    Route::get('/me/reviews', [ReviewController::class, 'myReviews']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    Route::post('/reviews/{review}/report', [ReviewController::class, 'report']);

    // Moderation
    Route::get('/admin/reviews/reported', [ModerationController::class, 'reported']);
    Route::post('/admin/reviews/{id}/keep', [ModerationController::class, 'keep']);
    Route::get('/admin/course-outlines/review', [ModerationController::class, 'adminReviewList']);
    Route::patch('/admin/course-outlines/{id}/keep', [ModerationController::class, 'adminKeep']);
    Route::delete('/admin/course-outlines/{id}', [ModerationController::class, 'adminDestroy']);


    // Review Vote
    Route::post('/vote', [ReviewVoteController::class, 'store']);

    // Course Outline
    Route::post('/courses/{id}/outline', [CourseOutlineController::class, 'store']);
    Route::post('/courses/{id}/outline/report-old', [CourseOutlineController::class, 'reportOld']);

    // Comments
    Route::post('/reviews/{review}/comments', [CommentController::class, 'store']);

    // Suggestions
    Route::post('/suggestions', [CourseSuggestionController::class, 'store']);
    Route::get('/course-suggestions', [CourseSuggestionController::class, 'index']);
    Route::post('/course-suggestions/{id}/approve', [CourseSuggestionController::class, 'approve']);
    Route::delete('/course-suggestions/{id}', [CourseSuggestionController::class, 'destroy']);
});

Route::get('/courses/{id}/outline-status', [CourseOutlineController::class, 'showStatus']);
