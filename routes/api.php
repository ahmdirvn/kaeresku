<?php

use App\Http\Controllers\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'index']);          // Read
    Route::post('/', [CourseController::class, 'store']);         // Create
    Route::put('/{id}', [CourseController::class, 'update']);     // Update
    Route::delete('/{id}', [CourseController::class, 'destroy']); // Delete
});
