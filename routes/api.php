<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CriteriaApiController;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\JudgmentApiController;
use App\Http\Controllers\Api\DashboardApiController;

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

// Criteria API Routes
Route::prefix('criteria')->group(function () {
    Route::get('/', [CriteriaApiController::class, 'index']);
    Route::get('/hierarchy', [CriteriaApiController::class, 'hierarchy']);
    Route::get('/types', [CriteriaApiController::class, 'types']);
    Route::get('/by-type/{criteriaType}', [CriteriaApiController::class, 'byType']);
    Route::get('/by-client/{client}', [CriteriaApiController::class, 'byClient']);
    Route::get('/by-industry/{industry}', [CriteriaApiController::class, 'byIndustry']);
    Route::get('/{criteria}', [CriteriaApiController::class, 'show']);
    Route::post('/', [CriteriaApiController::class, 'store']);
    Route::put('/{criteria}', [CriteriaApiController::class, 'update']);
    Route::delete('/{criteria}', [CriteriaApiController::class, 'destroy']);
    Route::post('/{criteria}/assign-industries', [CriteriaApiController::class, 'assignToIndustries']);
});

// Industries API Routes
Route::prefix('industries')->group(function () {
    Route::get('/', [CriteriaApiController::class, 'industries']);
});

// Projects API Routes
Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectApiController::class, 'index']);
    Route::get('/{project}', [ProjectApiController::class, 'show']);
    Route::get('/{project}/scoring', [ProjectApiController::class, 'scoring']);
    Route::post('/{project}/scoring', [ProjectApiController::class, 'saveScoring']);
    Route::get('/{project}/export/{format}', [ProjectApiController::class, 'export']);
});

// Judgment API Routes
Route::prefix('judgment')->group(function () {
    Route::get('/details', [JudgmentApiController::class, 'index']);
    Route::post('/details', [JudgmentApiController::class, 'store']);
    Route::put('/details/{judgmentDetail}', [JudgmentApiController::class, 'update']);
    Route::delete('/details/{judgmentDetail}', [JudgmentApiController::class, 'destroy']);
    Route::post('/bulk-update', [JudgmentApiController::class, 'bulkUpdate']);
    Route::get('/by-project/{project}', [JudgmentApiController::class, 'byProject']);
    Route::get('/by-criteria/{criteria}', [JudgmentApiController::class, 'byCriteria']);
    Route::get('/scoring-summary/{project}', [JudgmentApiController::class, 'scoringSummary']);
});

// Dashboard API Routes
Route::prefix('dashboard')->group(function () {
    Route::get('/statistics', [DashboardApiController::class, 'statistics']);
    Route::get('/recent-projects', [DashboardApiController::class, 'recentProjects']);
    Route::get('/scoring-status', [DashboardApiController::class, 'scoringStatus']);
    Route::get('/project-comparison', [DashboardApiController::class, 'projectComparison']);
    Route::get('/criteria-usage', [DashboardApiController::class, 'criteriaUsage']);
    Route::get('/client-statistics', [DashboardApiController::class, 'clientStatistics']);
    Route::get('/monthly-statistics', [DashboardApiController::class, 'monthlyStatistics']);
    Route::get('/top-locations', [DashboardApiController::class, 'topLocations']);
    Route::get('/scoring-trends', [DashboardApiController::class, 'scoringTrends']);
});
