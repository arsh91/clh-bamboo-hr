<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

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


Route::match(['get', 'post'], '/', [AuthController::class, 'index']);
Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('login');
Route::get('/forgot-password', [AuthController::class, 'forgotPasswordView'])->name('forgot-password');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.password');
Route::get('/reset/password/{token}', [AuthController::class, 'resetPassword']);
Route::post('/reset/password', [AuthController::class, 'submitResetPasswordForm'])->name('submit.reset.password');

//Authenticated Group Routes Starts
Route::group(['middleware' => ['auth']], function() {

    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
       
        //Protected Routes For Admin
        Route::group(['middleware' => ['admin']], function() {
            //Users Routes
            Route::get('/users', [UsersController::class,'index'])->name('users.index');
            Route::post('/users/add/',[UsersController::class,'store']);
            Route::get('/users/edit/{id}',[UsersController::class,'edit'])->name('users.edit');
            Route::post('/users/{user}',[UsersController::class,'update'])->name('users.update');
            Route::delete('/users/delete/{user}',[UsersController::class,'destroy'])->name('users.destroy');
        });
        //Ends Protected Routes For Admin

        Route::get('/catalogs', [CatalogController::class,'index'])->name('catalogs.index');
        Route::post('/catalogs/add/',[CatalogController::class,'store']);
        Route::get('/catalogs/edit/{id}',[CatalogController::class,'edit'])->name('catalogs.edit');
        Route::post('/catalogs/{catalog}',[CatalogController::class,'update'])->name('catalogs.update');
        Route::delete('/catalogs/delete/{catalog}',[CatalogController::class,'destroy'])->name('catalogs.destroy');
        Route::get('/catalog/{id}',[CatalogController::class,'show'])->name('catalogs.show');


        Route::get('/fetch-catalog-categories', [CatalogController::class,'fetchCategories']);


    Route::get('logout', [AuthController::class, 'logOut'])->name('logout');
});
//Authenticated Group Routes Ends
