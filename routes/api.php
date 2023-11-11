<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
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
Route::get('/patients/', [PatientController::class, 'index']);
Route::get('/patient/{patient}', [PatientController::class, 'show']);
Route::post('/patient/', [PatientController::class, 'store']);
Route::put('/patient/{patient}', [PatientController::class, 'update']);
Route::delete('/patient/{patient}', [PatientController::class, 'destroy']);

