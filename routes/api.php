<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Dashboard API endpoints
Route::middleware('auth:sanctum')->prefix('dashboard')->group(function () {
    Route::get('/students-with-ca', [DashboardController::class, 'getStudentsWithCA']);
    Route::get('/courses-from-edurole', [DashboardController::class, 'getCoursesFromEdurole']);
    Route::get('/courses-from-lmmax', [DashboardController::class, 'getCoursesFromLMMAX']);
    Route::get('/programme-chart', [DashboardController::class, 'getProgrammeChartData']);
    Route::get('/school-chart', [DashboardController::class, 'getSchoolChartData']);
    Route::get('/recent-activities', [DashboardController::class, 'getRecentActivities']);
    Route::get('/announcements', [DashboardController::class, 'getAnnouncements']);
});
