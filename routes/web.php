<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\ScoreController;

Route::get('/', function () {
    return redirect('/admin/login');
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('/setup', [AdminController::class, 'setupAdmin']);
    
    // Protected Routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // Resource Routes
        Route::resource('clients', ClientController::class);
        Route::resource('projects', ProjectController::class);
        Route::resource('locations', LocationController::class);
        Route::resource('criteria', CriteriaController::class);
        Route::resource('scores', ScoreController::class);
        
        // Project Locations Routes
        Route::get('/projects/{project}/locations', [ProjectController::class, 'locations'])->name('projects.locations');
        Route::post('/projects/{project}/locations', [ProjectController::class, 'assignLocation'])->name('projects.assign-location');
        Route::delete('/projects/{project}/locations/{location}', [ProjectController::class, 'removeLocation'])->name('projects.remove-location');
        
        // Scoring Routes
        Route::get('/projects/{project}/scoring', [ProjectController::class, 'scoring'])->name('projects.scoring');
        Route::post('/projects/{project}/scoring', [ProjectController::class, 'saveScoring'])->name('projects.save-scoring');
        
        // Dashboard Routes
        Route::get('/projects/{project}/dashboard', [ProjectController::class, 'dashboard'])->name('projects.dashboard');
        Route::get('/projects/{project}/export', [ProjectController::class, 'export'])->name('projects.export');
    });
});
