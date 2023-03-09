<?php

use App\Http\Controllers\AppointmentBookerController;
use App\Http\Controllers\PatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Patients Routes
|--------------------------------------------------------------------------
*/
Route::group(
    ['prefix' => 'patients'],
    function () {
        Route::post('', [PatientController::class, 'store'])->name('patients.store');
        Route::post('/search/{dni}', [PatientController::class, 'search'])->name('patients.search');
    }
);

/*
|--------------------------------------------------------------------------
| Appointments Routes
|--------------------------------------------------------------------------
*/

Route::group(
    ['prefix' => 'appointments'],
    function () {
        Route::post('', [AppointmentBookerController::class, 'book'])->name('appointments.book');
    }
);
