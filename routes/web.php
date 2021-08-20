<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\DirectoriesController;
use App\Http\Controllers\PhotosController;
use App\Http\Controllers\SyncController;
use App\Models\Directory;
use App\Models\Photo;
use App\Http\Middleware\MigrationsRan;

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

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');


Route::middleware([MigrationsRan::class])->group(function() {
    Route::get('/directories', [DirectoriesController::class, 'index'])->name('directories');
    Route::patch('/directories/{directory}/update', [DirectoriesController::class, 'update'])->name('directories.update');

    Route::get('/photos/random', [PhotosController::class, 'random'])->name('photos.random');
    Route::get('/photos/search', [PhotosController::class, 'search'])->name('photos.search');
    Route::get('/photos/broken', [PhotosController::class, 'broken'])->name('photos.broken');


    Route::post('/sync/directories', [SyncController::class, 'syncDirectories'])->name('sync.directories');
    Route::get('/sync/status', [SyncController::class, 'syncStatus'])->name('sync.status');
    Route::post('/sync/directories/{directory}', [SyncController::class, 'syncDirectory'])->name('sync.directory');
    Route::post('/sync/photos/broken', [SyncController::class, 'syncBrokenPhotos'])->name('sync.photos.broken');
});
