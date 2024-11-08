<?php

use App\Http\Controllers\EmployeesController;
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

Route::get('/', [EmployeesController::class, ('index')]);
Route::prefix('/employee')->name('employee.')->group(function () {
    Route::post('/stamping', [EmployeesController::class, ('attendance')])->name('attendance');
    Route::post('/stamped/{employee_id}', [EmployeesController::class, 'clickedBtn'])->name('stamped');
    Route::get('/records/{employee_id}', [EmployeesController::class, 'records'])->name('records');
    Route::post('/record/change/{employee_id}', [EmployeesController::class, 'changeRequest'])->name('changeRequest');
    Route::post('/record/send-change/{employee_id}', [EmployeesController::class, 'sendChangeRequest'])->name('sendRequest');
});

