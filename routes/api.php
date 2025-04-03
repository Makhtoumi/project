<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ClientReportController;
use App\Http\Controllers\ImportController;
// Clients
Route::get('/clients', [ClientController::class, 'index']);
Route::get('/clients/{id}/cost-distribution', [ClientController::class, 'getCostsDistribution']);
Route::post('/clients', [ClientController::class, 'store']);
Route::get('/clients/{client_id}/rapport', [ClientReportController::class, 'rapport']);
Route::post('/api/import', [ImportController::class, 'importCSV']);
Route::get('/test', function () {
    return 'Route is working!';
});
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/clients', [ClientController::class, 'index']);
    
//     Route::get('/clients/{client}/projets', [ProjectController::class, 'index']);
    
//     Route::post('/projets/{project}/taches', [TaskController::class, 'store']);
// });

// Projects
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{id}', [ProjectController::class, 'show']);
Route::post('/projects', [ProjectController::class, 'store']);
Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);

// Tasks
Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
