<?php

use App\Http\Controllers\Admin\AlertController;
use App\Http\Controllers\Admin\CommonComplaintController;
use App\Http\Controllers\Admin\ComplaintsCategoryController;
use Illuminate\Support\Facades\Route;

Route::group([], function () {

    Route::get('dashboard', function () {
        return view('admin.index');
    })->name('dashboard');

    Route::prefix('common-complaints')->name('common-complaints.')->group(function () {
        Route::get('/', [CommonComplaintController::class, 'index'])->name('index');
        Route::get('/get', [CommonComplaintController::class, 'get'])->name('get');
        Route::get('/data', [CommonComplaintController::class, 'data'])->name('data');
        Route::get('/create', [CommonComplaintController::class, 'create'])->name('create');
        Route::post('/', [CommonComplaintController::class, 'store'])->name('store');
        Route::post('/store', [CommonComplaintController::class, 'store'])->name('store');
        Route::get('/statistics', [CommonComplaintController::class, 'getStatistics'])->name('statistics');
        Route::get('/categories', [CommonComplaintController::class, 'getCategories'])->name('categories');
        Route::get('/export', [CommonComplaintController::class, 'export'])->name('export');
        Route::post('/export', [CommonComplaintController::class, 'export'])->name('export.post');
        
        // Individual complaint routes
        Route::get('/{id}', [CommonComplaintController::class, 'show'])->whereNumber('id')->name('show');
        Route::get('/{id}/edit', [CommonComplaintController::class, 'edit'])->whereNumber('id')->name('edit');
        Route::put('/{id}', [CommonComplaintController::class, 'update'])->whereNumber('id')->name('update');
        Route::delete('/{id}', [CommonComplaintController::class, 'destroy'])->whereNumber('id')->name('destroy');
        
        // Form builder routes
        Route::get('/{id}/form-builder', [CommonComplaintController::class, 'formBuilder'])->whereNumber('id')->name('form-builder');
        Route::get('/{id}/form-structure', [CommonComplaintController::class, 'getFormStructure'])->whereNumber('id')->name('form.structure');
        Route::put('/{id}/form-structure', [CommonComplaintController::class, 'updateFormStructure'])->whereNumber('id')->name('form.structure.update');
        Route::get('/{id}/preview', [CommonComplaintController::class, 'previewForm'])->whereNumber('id')->name('preview');
        
        // Analytics and usage routes
        Route::get('/{id}/usage', [CommonComplaintController::class, 'usage'])->whereNumber('id')->name('usage');
        Route::get('/{id}/analytics', [CommonComplaintController::class, 'getUsageAnalytics'])->whereNumber('id')->name('analytics');
        Route::post('/{id}/duplicate', [CommonComplaintController::class, 'duplicate'])->whereNumber('id')->name('duplicate');
    });

    Route::prefix('complaint-category')->name('complaint-category.')->group(function () {
        Route::get('/', [ComplaintsCategoryController::class, 'index'])->name('index');
        Route::get('/get', [ComplaintsCategoryController::class, 'get'])->name('get');
        Route::get('/show', [ComplaintsCategoryController::class, 'show'])->name('show');
        Route::get('/create', [ComplaintsCategoryController::class, 'create'])->name('create');
        Route::post('/', [ComplaintsCategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ComplaintsCategoryController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [ComplaintsCategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [ComplaintsCategoryController::class, 'destroy'])->name('destroy');
    });

    // Alert Management Routes
    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/', [AlertController::class, 'index'])->name('index');
        Route::get('/data', [AlertController::class, 'getData'])->name('data');
        Route::get('/create', [AlertController::class, 'create'])->name('create');
        Route::post('/', [AlertController::class, 'store'])->name('store');
        Route::get('/{id}', [AlertController::class, 'show'])->whereNumber('id')->name('show');
        Route::get('/{id}/edit', [AlertController::class, 'edit'])->whereNumber('id')->name('edit');
        Route::put('/{id}', [AlertController::class, 'update'])->whereNumber('id')->name('update');
        Route::delete('/{id}', [AlertController::class, 'destroy'])->whereNumber('id')->name('destroy');
        Route::post('/{id}/toggle-status', [AlertController::class, 'toggleStatus'])->whereNumber('id')->name('toggle-status');
    });


});
