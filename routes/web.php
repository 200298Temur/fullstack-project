<?php

use App\Http\Middleware\AdminCheck;
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
use App\Http\Controllers\TagController;

Route::prefix("app")->middleware([AdminCheck::class])->group(function(){
    // Route::post('/create_tag', [TagController::class, 'store']);
    // Route::get('/get-tags', [TagController::class, 'index']); 
    // Route::put('/edit-tag/{tag}', [TagController::class, 'update']);
    // Route::post('/delete_tag/{tag}', [TagController::class, 'destroy']);
   
    Route::apiResource('tags', TagController::class);

    // Route::resource('tags', TagController::class)->except([
    //     'store', 'update', 'destroy','index'
    // ]);

    Route::post('/upload', [AdminController::class, 'upload']);
    Route::post('/delete_image', [AdminController::class,'deleteImage']);
    Route::post('/create_category', [AdminController::class,'addCategory']);
    Route::get('/get-category', [AdminController::class,'getCategory']);
    Route::post('/edit_category', [AdminController::class,'editCategory']);
    Route::post('/delete_category', [AdminController::class,'deleteCategory']);
    Route::post('/create_user',[AdminController::class,"createUser"]);
    Route::get('/get_users',[AdminController::class,"getUser"]);
    Route::post('/edit_user',[AdminController::class,"editUser"]);
    Route::post('/admin_login',[AdminController::class,"adminlogin"]);
    Route::post('create_role',[AdminController::class,'createRole']);
    Route::get('get_role',[AdminController::class,'getRole']);
    Route::post('edit_role',[AdminController::class,'editRole']);
    Route::post('delete_role',[AdminController::class,'deleteRole']);
    
});

Route::get('/logout',[AdminController::class,'logout']);
Route::get('/',[AdminController::class,'index']);
Route::any('{slug}',[AdminController::class,'index']);
