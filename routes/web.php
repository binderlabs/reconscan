<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\SubdomainController;
use App\Http\Controllers\ConsoleController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Project Controller
Route::middleware(['auth'])->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
    // Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/project/create', [ProjectController::class, 'index']);
    Route::post('/project/create', [ProjectController::class, 'create']);
    Route::get('/project/delete/{id}', [ProjectController::class, 'delete']);
    Route::get('/project/{id}', [ProjectController::class, 'browse']);
    Route::get('/scan', [ProjectController::class, 'index']);
});

// DomainController
Route::middleware(['auth'])->group(function () {
Route::post('/newdomain', [DomainController::class, 'newdomain']);
Route::get('/domain/{id}', [DomainController::class, 'index']);
Route::get('/domain/delete/{id}', [DomainController::class, 'delete']);
Route::post('/subdomain/add',[DomainController::class, 'adddomain']);
});

//ScannerController
Route::middleware(['auth'])->group(function () {
Route::post('/scan', [ScannerController::class,'scan']);
Route::post('/subdomainscan', [ScannerController::class,'subdomainscan']);
});


//SubdomainController
Route::middleware(['auth'])->group(function () {
Route::get('/subdomain/{id}', [SubdomainController::class, 'index']);
Route::get('/subdomain/delete/{id}', [SubdomainController::class, 'delete']);
Route::post('/subdomain/scan', [SubdomainController::class, 'scan']);
});

// JobControllers
Route::middleware(['auth'])->group(function () {
Route::get('/status/{batchId}', function (string $batchId) {
    return Bus::findBatch($batchId);
});
});

Route::middleware(['auth'])->group(function () {
Route::get('/console', [ConsoleController::class, 'index']);
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');