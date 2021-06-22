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
Route::get('parstate/submit',[ParstatesController::class,'create'] );
Route::post('parstate/submit',[ParstatesController::class,'store'] );
Route::get('/acknowledgealert/{alert:uuid}', [AcknowledgeAlertController::class, 'acknowledge']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

