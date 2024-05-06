<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReportsController;
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

          //  Route::get('/employee/{id}',[DashboardController::class,'employeDetail'])->name('employee');

            /** THE ROUTES ARE FOR EMPLOYEE CONTROLLER */
           // Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
            Route::get('/employee/{id}', [EmployeeController::class, 'employeDetail'])->name('employees.detail');
            Route::get('/employee/jobinformation/{id}', [EmployeeController::class, 'employeJobinformation'])->name('employees.jobinformation');

            Route::get('/employee/row/{id}', [EmployeeController::class, 'employeEmptyFieldsCount'])->name('employees.emptyFieldCount');

            Route::post('/employee/row/timetracker/{id}', [EmployeeController::class, 'employeTimetracker'])->name('employees.timetracker');

            /**ROUTES FOR EMPLOYEE DOCUMENTS */ 
            Route::get('/employee/documents/{id}', [DocumentController::class, 'listEmplyeeDocuments'])->name('employees.documents');
            Route::post('/doucument/row/count/{id}', [DocumentController::class, 'getDoucumentCount'])->name('document.count');

            Route::get('/document/report', [DocumentController::class, 'getReports'])->name('document.report');
            
            Route::post('/start-report', [DashboardController::class, 'startCreatingReport'])->name('start.report');
            Route::post('/get-saved-folder', [DashboardController::class, 'getSavedFolder'])->name('get.savedfolder');
            Route::post('/save-folder', [DashboardController::class, 'saveFolder'])->name('save.folder');
            Route::get('/folder', [DashboardController::class, 'folder'])->name('show.folder');
            Route::get('/reports', [ReportsController::class, 'index'])->name('show.reports');
            Route::post('/empty-field-detail', [ReportsController::class, 'getEmptyFiledsDetails'])->name('show.emptyFieldDetail');
            Route::post('/generate-data', [ReportsController::class, 'generateData'])->name('generate.data');
            Route::get('/runcron', [DashboardController::class, 'runCron'])->name('run.cron');
            
        });
        //Ends Protected Routes For Admin

    Route::get('logout', [AuthController::class, 'logOut'])->name('logout');



});
//Authenticated Group Routes Ends
