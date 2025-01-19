<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});
*/

Route::get('/', [PetController::class, 'index'])->name('pets.index');
Route::get('/pets/create', [PetController::class, 'showForm'])->name('pets.create');
Route::post('/pets', [PetController::class, 'store'])->name('pets.store');
Route::get('/pets/{id}/edit', [PetController::class, 'showForm'])->name('pets.edit');
Route::put('/pets', [PetController::class, 'update'])->name('pets.update');
Route::delete('/pets/{id}', [PetController::class, 'destroy'])->name('pets.destroy');
