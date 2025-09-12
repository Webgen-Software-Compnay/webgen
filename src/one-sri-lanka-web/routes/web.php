<?php

use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\CommonComplaintController;
use App\Http\Controllers\Admin\CommonComplaintController as AdminCommonComplaintController;
use Illuminate\Support\Facades\Route;

Route::get('user/dashboard', function () {
    return view('user.dashboard');
})->name('dashboard');
// Route::get('login', function () {
//     return view('user.auth.login');
// })->name('login');

Route::get('user/action', [DashboardController::class, 'action'])->name('action');

// User complaint routes
Route::get('common-complaint-create', [CommonComplaintController::class, 'create'])->name('complaint.create');
Route::post('common-complaint-store', [CommonComplaintController::class, 'store'])->name('complaint.store');
Route::get('complaint-success', [CommonComplaintController::class, 'success'])->name('complaint.success');
Route::get('common-complaint/{id}/form-data', [CommonComplaintController::class, 'getFormData'])->name('complaint.form-data');

Route::get('test', [AuthController::class, 'index'])->name('test');

Route::get('user/profile', function () {
    return view('user.profile');
})->name('userProfile');

Route::get('user/business-registrations', function () {
    return view('user.business-registrations');
})->name('business.registrations');

Route::get('user/food-details', function () {
    return view('user.food-details');
})->name('food.details');


Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
});

Route::group([], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
