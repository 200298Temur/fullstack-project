<?php

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

use App\Http\Controllers\TestController;
use App\Http\Controllers\AdminController;


Route::post('app/create_tag', [AdminController::class, 'addTag']);
Route::get('app/get-tags', [AdminController::class, 'getTag']);
Route::post('app/edit-tag', [AdminController::class, 'editTag']);
Route::post('app/delete_tag', [AdminController::class, 'deleteTag']);
Route::post('app/upload', [AdminController::class, 'upload']);

Route::get('/', function() {
    return view('welcome');
});

Route::any("{slug}", function() {
    return view('welcome');
});
