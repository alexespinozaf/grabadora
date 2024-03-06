<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecorderController;
use App\Http\Controllers\MoodleController;
use App\Http\Controllers\GradesController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CharacterController;

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

Auth::routes();

Route::get('/home/{idCourse}', [HomeController::class, 'index'])->name('home');

Route::resource('recorder', RecorderController::class);
Route::post('recordings/upload', [RecorderController::class, 'store'])->name('recordings.upload');
Route::get('recording/mix', [RecorderController::class, 'index'])->name('recording.mix');


Route::get('/grades/{idCourse}', [GradesController::class, 'index'])->name('grades.index');
Route::post('/grades/sendEvaluation', [GradesController::class, 'sendEvaluation'])->name('grades.sendEvaluation');
Route::post('/members', [GradesController::class, 'members'])->name('members');
Route::post('grades', [GradesController::class, 'store'])->name('grades');
Route::post('/publishGrades', [GradesController::class, 'publishGrades'])->name('grades.publishGrades');
Route::get('/courses', [MoodleController::class, 'courses'])->name('courses');
Route::get('/groups/{idCourse}', [MoodleController::class, 'groups'])->name('groups');
Route::post('/countGrades', [GradesController::class, 'countGrades'])->name('countGrades');

Route::resource('characters', CharacterController::class);
Route::resource('activities', ActivityController::class);

Route::get('/format', [RecorderController::class, 'format'])->name('format');



