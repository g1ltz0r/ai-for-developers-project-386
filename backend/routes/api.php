<?php

use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\OwnerBookingController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\PublicEventTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/event-types', [EventTypeController::class, 'index']);
Route::post('/event-types', [EventTypeController::class, 'store']);
Route::patch('/event-types/{id}', [EventTypeController::class, 'update']);
Route::delete('/event-types/{id}', [EventTypeController::class, 'destroy']);

Route::get('/event-types/{id}/slots', [PublicEventTypeController::class, 'slots']);
Route::post('/event-types/{id}/book', [PublicBookingController::class, 'book']);

Route::get('/admin/bookings', [OwnerBookingController::class, 'index']);
Route::delete('/admin/bookings/{id}', [OwnerBookingController::class, 'destroy']);
