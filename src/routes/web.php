<?php

use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\PaidHolidayController;
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

Route::get('/', [EmployeesController::class, ('index')])->name('index');
Route::prefix('/employee')->name('employee.')->group(function () {
    Route::post('/stamping', [EmployeesController::class, ('attendance')])->name('attendance');
    Route::post('/stamped', [EmployeesController::class, 'clickedBtn'])->name('stamped');
    Route::get('/records/{employee_id}', [EmployeesController::class, 'records'])->name('records');
    Route::get('/record/change/{employee_id}', [EmployeesController::class, 'changeRequest'])->name('changeRequest');
    Route::post('/record/confirm', [EmployeesController::class, 'confirmChangeRequest'])->name('confirmRequest');
    Route::post('/record/submit', [EmployeesController::class, 'submitChangeRequest'])->name('submitRequest');
    Route::get('/apply/paid-holiday/{employee_id}', [PaidHolidayController::class, 'applyPaidHoliday'])->name('applyPaidHoliday');
    Route::post('/apply/paid-holiday', [PaidHolidayController::class, 'submitPaidHoliday'])->name('submitPaidHoliday');
});

