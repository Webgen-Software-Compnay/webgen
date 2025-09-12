<?php

use App\Http\Controllers\Api\V1\Admin\CommonComplaintController;
use App\Http\Controllers\Api\V1\Admin\ComplaintCategoryController;
use App\Http\Controllers\Api\V1\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Api\V1\Admin\AlertController;
use App\Http\Controllers\Api\V1\Admin\EntityController;
use App\Http\Controllers\Api\V1\Admin\FormBuilderController;
use App\Http\Controllers\Api\V1\User\ComplaintController as UserComplaintController;
use App\Http\Controllers\Api\V1\DynamicFormController;
use Illuminate\Support\Facades\Route;

// ================================
// TEST ENDPOINT
// ================================
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

Route::prefix('alerts')->name('alerts.')->group(function () {
    Route::get('/', [AlertController::class, 'index'])->name('index');
    Route::get('/data', [AlertController::class, 'getData'])->name('data');
    Route::post('/', [AlertController::class, 'store'])->name('store');
    Route::get('/{id}', [AlertController::class, 'show'])->whereNumber('id')->name('show');
    Route::put('/{id}', [AlertController::class, 'update'])->whereNumber('id')->name('update');
    Route::delete('/{id}', [AlertController::class, 'destroy'])->whereNumber('id')->name('destroy');
    Route::post('/{id}/toggle-status', [AlertController::class, 'toggleStatus'])->whereNumber('id')->name('toggle-status');
});

Route::prefix('complaint-category')->name('complaint-category.')->group(function () {

    // Main CRUD Routes
    Route::get('/', [ComplaintCategoryController::class, 'index'])->name('index');
    Route::get('/create', [ComplaintCategoryController::class, 'create'])->name('create');
    Route::post('/', [ComplaintCategoryController::class, 'store'])->name('store');
    Route::get('/{id}', [ComplaintCategoryController::class, 'show'])->whereNumber('id')->name('show');
    Route::get('/{id}/edit', [ComplaintCategoryController::class, 'edit'])->whereNumber('id')->name('edit');
    Route::put('/{id}', [ComplaintCategoryController::class, 'update'])->whereNumber('id')->name('update');
    Route::patch('/{id}', [ComplaintCategoryController::class, 'update'])->whereNumber('id')->name('patch');
    Route::delete('/{id}', [ComplaintCategoryController::class, 'destroy'])->whereNumber('id')->name('destroy');

    // DataTable Routes
    Route::get('/get-data', [ComplaintCategoryController::class, 'getData'])->name('get-data');
    Route::post('/datatable', [ComplaintCategoryController::class, 'datatable'])->name('datatable');
    Route::get('/index-config', [ComplaintCategoryController::class, 'indexWithConfig'])->name('index-config');
    Route::get('/index-custom', [ComplaintCategoryController::class, 'indexCustom'])->name('index-custom');

    // Status Management Routes
    Route::post('/{id}/toggle-status', [ComplaintCategoryController::class, 'toggleStatus'])->whereNumber('id')->name('toggle-status');
    Route::post('/{id}/activate', [ComplaintCategoryController::class, 'activate'])->whereNumber('id')->name('activate');
    Route::post('/{id}/deactivate', [ComplaintCategoryController::class, 'deactivate'])->whereNumber('id')->name('deactivate');

    // Search and Filter Routes
    Route::get('/search', [ComplaintCategoryController::class, 'search'])->name('search');
    Route::get('/search-parents', [ComplaintCategoryController::class, 'searchParents'])->name('search-parents');
    Route::get('/filter-by-type/{type}', [ComplaintCategoryController::class, 'filterByType'])->name('filter-by-type');
    Route::get('/filter-by-status/{status}', [ComplaintCategoryController::class, 'filterByStatus'])->name('filter-by-status');

    // Category Retrieval Routes
    Route::get('/get-all', [ComplaintCategoryController::class, 'getAll'])->name('get-all');
    Route::get('/get-active', [ComplaintCategoryController::class, 'getActive'])->name('get-active');
    Route::get('/get-active-ui', [ComplaintCategoryController::class, 'getAllActiveForUI'])->name('get-active-ui');
    Route::get('/get-main-categories', [ComplaintCategoryController::class, 'getMainCategories'])->name('get-main-categories');
    Route::get('/get-sub-categories', [ComplaintCategoryController::class, 'getSubCategories'])->name('get-sub-categories');
    Route::get('/get-by-parent/{parentId}', [ComplaintCategoryController::class, 'getByParent'])->whereNumber('parentId')->name('get-by-parent');

    // Statistics and Reports Routes
    Route::get('/statistics', [ComplaintCategoryController::class, 'getStatistics'])->name('statistics');
    Route::get('/reports', [ComplaintCategoryController::class, 'getReports'])->name('reports');
    Route::get('/usage-report', [ComplaintCategoryController::class, 'getUsageReport'])->name('usage-report');

    // Bulk Operations Routes
    Route::post('/bulk-action', [ComplaintCategoryController::class, 'bulkAction'])->name('bulk-action');
    
    // Import/Export Routes
    Route::get('/export', [ComplaintCategoryController::class, 'export'])->name('export');
    Route::post('/import', [ComplaintCategoryController::class, 'import'])->name('import');

    // Legacy/Alternative Routes (for backward compatibility)
    Route::post('/store-category', [ComplaintCategoryController::class, 'storeComplaintCategory'])->name('store-alternative');
});

// ================================
// PUBLIC ENDPOINTS (No Authentication Required)
// ================================
Route::prefix('public')->name('public.')->group(function () {
    // Public category access for frontend applications
    Route::prefix('complaint-categories')->name('complaint-categories.')->group(function () {
        Route::get('/', [ComplaintCategoryController::class, 'getActive'])->name('index');
        Route::get('/active', [ComplaintCategoryController::class, 'getActive'])->name('active');
        Route::get('/active-ui', [ComplaintCategoryController::class, 'getAllActiveForUI'])->name('active-ui');
        Route::get('/main', [ComplaintCategoryController::class, 'getMainCategories'])->name('main');
        Route::get('/sub', [ComplaintCategoryController::class, 'getSubCategories'])->name('sub');
        Route::get('/by-parent/{parentId}', [ComplaintCategoryController::class, 'getByParent'])->whereNumber('parentId')->name('by-parent');
        Route::get('/search', [ComplaintCategoryController::class, 'search'])->name('search');
    });
});

// ================================
// COMMON COMPLAINTS ROUTES
// ================================
Route::prefix('common-complaints')->name('common-complaints.')->group(function () {
    Route::get('/', [CommonComplaintController::class, 'index'])->name('index');
    Route::get('/sample', [CommonComplaintController::class, 'getSampleData'])->name('sample');
    Route::get('/{id}/form-data', [CommonComplaintController::class, 'getFormData'])->whereNumber('id')->name('form-data');
    Route::get('/create', [CommonComplaintController::class, 'create'])->name('create');
    Route::post('/', [CommonComplaintController::class, 'store'])->name('store');
    Route::get('/{id}', [CommonComplaintController::class, 'show'])->whereNumber('id')->name('show');
    Route::get('/{id}/edit', [CommonComplaintController::class, 'edit'])->whereNumber('id')->name('edit');
    Route::put('/{id}', [CommonComplaintController::class, 'update'])->whereNumber('id')->name('update');
    Route::patch('/{id}', [CommonComplaintController::class, 'update'])->whereNumber('id')->name('patch');
    Route::delete('/{id}', [CommonComplaintController::class, 'destroy'])->whereNumber('id')->name('destroy');
});
