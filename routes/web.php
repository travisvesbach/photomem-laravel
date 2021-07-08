<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DirectoriesController;
use App\Http\Controllers\PicturesController;
use App\Models\Directory;
use App\Models\Picture;

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

Route::view('/', 'index', [
    'pictures' => Picture::all(),
    'pictures_today' => Picture::takenToday()->get(),
    'directories' => Directory::orderBy('path')->get(),
]);
Route::view('/about', 'about')->name('about');

Route::get('/directories', [DirectoriesController::class, 'index'])->name('directories');
Route::post('/directories/sync', [DirectoriesController::class, 'syncDirectories'])->name('directories.syncDirectories');
Route::get('/directories/sync_status', [DirectoriesController::class, 'syncStatus'])->name('directories.syncStatus');
Route::post('/directories/{directory}/sync', [DirectoriesController::class, 'sync'])->name('directories.sync');
Route::patch('/directories/{directory}/update', [DirectoriesController::class, 'update'])->name('directories.update');

Route::get('/pictures/random', [PicturesController::class, 'random'])->name('pictures.random');
