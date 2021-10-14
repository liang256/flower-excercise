<?php

use App\Http\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('skills', [SkillController::class, 'index'])->name('skills.index');
Route::get('skills/compare', [SkillController::class, 'compare'])->name('skills.compare');
Route::post('skills/compare', [SkillController::class, 'store'])->name('skills.compare.store');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
