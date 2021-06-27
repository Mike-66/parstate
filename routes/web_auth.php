<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AcknowledgeAlertController;

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
Route::get('parstate/parstate',[App\Http\Controllers\ParstatesController::class,'create'] )->name('parstateget');
Route::post('parstate/parstate',[App\Http\Controllers\ParstatesController::class,'store'] )->name('parstatepost');
Route::get('/acknowledgealert/{alert:uuid}', [AcknowledgeAlertController::class, 'acknowledge'])->name('acknowledge');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

