<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompaniesController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/', [AuthController::class, 'index']);
Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('login');

//Authenticated Group Routes Starts
Route::group(['middleware' => ['auth']], function() {
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');

    Route::get('/companies', [CompaniesController::class,'index']);
    Route::post('/companies/add/',[CompaniesController::class,'store']);
    Route::delete('/companies/delete/{company}',[CompaniesController::class,'destroy'])->name('companies.destroy');
   
    //Users Routes
    Route::get('/users', [UsersController::class,'index'])->name('users.index');
    Route::post('/users/add/',[UsersController::class,'store']);
    Route::delete('/users/delete/{company}',[UsersController::class,'destroy'])->name('users.destroy');

    Route::get('logout', [AuthController::class, 'logOut'])->name('logout');
});
//Authenticated Group Routes Ends
