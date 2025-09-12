<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NdxDataController;
use App\Http\Controllers\Api\GeographicDataController;

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
| NDX (National Data Exchange) API Routes
|--------------------------------------------------------------------------
|
| These routes provide access to Sri Lankan government data through
| the NDX layer for dynamic form fields and data integration.
|
*/

Route::prefix('ndx')->name('api.ndx.')->group(function () {
    
    // Healthcare Facilities
    Route::get('/hospitals', [NdxDataController::class, 'hospitals'])
        ->name('hospitals');
    
    // Law Enforcement
    Route::get('/police-stations', [NdxDataController::class, 'policeStations'])
        ->name('police-stations');
    
    // Educational Institutions
    Route::get('/schools', [NdxDataController::class, 'schools'])
        ->name('schools');
    
    // Government Offices
    Route::get('/government-offices', [NdxDataController::class, 'governmentOffices'])
        ->name('government-offices');
    
    // Courts and Judicial
    Route::get('/courts', [NdxDataController::class, 'courts'])
        ->name('courts');
    
    // Transportation
    Route::get('/transport-authorities', [NdxDataController::class, 'transportAuthorities'])
        ->name('transport-authorities');
    
    Route::get('/bus-routes', [NdxDataController::class, 'busRoutes'])
        ->name('bus-routes');
    
    // Utilities
    Route::get('/utility-providers', [NdxDataController::class, 'utilityProviders'])
        ->name('utility-providers');
    
    // Banks and Financial
    Route::get('/banks', [NdxDataController::class, 'banks'])
        ->name('banks');
        
    Route::get('/bank-branches', [NdxDataController::class, 'bankBranches'])
        ->name('bank-branches');
});

/*
|--------------------------------------------------------------------------
| Geographic Data API Routes
|--------------------------------------------------------------------------
|
| These routes provide access to Sri Lankan administrative divisions
| for location-based form fields and geographic data integration.
|
*/

Route::prefix('geographic')->name('api.geographic.')->group(function () {
    
    // Administrative Hierarchy
    Route::get('/provinces', [GeographicDataController::class, 'provinces'])
        ->name('provinces');
    
    Route::get('/districts', [GeographicDataController::class, 'districts'])
        ->name('districts');
    
    Route::get('/divisional-secretariats', [GeographicDataController::class, 'divisionalSecretariats'])
        ->name('divisional-secretariats');
    
    Route::get('/grama-niladhari', [GeographicDataController::class, 'gramaNiladhari'])
        ->name('grama-niladhari');
});

/*
|--------------------------------------------------------------------------
| Dynamic Form Builder API Routes
|--------------------------------------------------------------------------
|
| These routes support the dynamic form builder functionality for
| complaint forms and other dynamic content generation.
|
*/

Route::prefix('forms')->name('api.forms.')->group(function () {
    
    // Field Validation
    Route::post('/validate-field', function (Request $request) {
        // Basic field validation logic
        $field = $request->all();
        
        // Validate required field properties
        $required = ['type', 'label', 'name'];
        $missing = array_diff($required, array_keys($field));
        
        if (!empty($missing)) {
            return response()->json([
                'valid' => false,
                'errors' => ['Missing required fields: ' . implode(', ', $missing)]
            ], 400);
        }
        
        return response()->json([
            'valid' => true,
            'field' => $field
        ]);
    })->name('validate-field');
    
    // Form Schema Validation
    Route::post('/validate-schema', function (Request $request) {
        $schema = $request->get('schema', []);
        
        if (empty($schema)) {
            return response()->json([
                'valid' => false,
                'errors' => ['Schema cannot be empty']
            ], 400);
        }
        
        $errors = [];
        foreach ($schema as $index => $field) {
            if (!isset($field['type']) || !isset($field['name'])) {
                $errors[] = "Field at index {$index} is missing required properties";
            }
        }
        
        return response()->json([
            'valid' => empty($errors),
            'errors' => $errors,
            'field_count' => count($schema)
        ]);
    })->name('validate-schema');
});

/*
|--------------------------------------------------------------------------
| System Health and Testing Routes
|--------------------------------------------------------------------------
|
| These routes provide system health checks and testing capabilities
| for the API endpoints and external integrations.
|
*/

Route::prefix('system')->name('api.system.')->group(function () {
    
    // API Health Check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0',
            'services' => [
                'ndx' => 'operational',
                'geographic' => 'operational',
                'forms' => 'operational'
            ]
        ]);
    })->name('health');
    
    // NDX Connection Test
    Route::get('/test-ndx', function () {
        try {
            // Test NDX endpoints
            $tests = [
                'hospitals' => app(NdxDataController::class)->hospitals(request()),
                'police' => app(NdxDataController::class)->policeStations(request()),
                'schools' => app(NdxDataController::class)->schools(request())
            ];
            
            return response()->json([
                'ndx_status' => 'operational',
                'tests_passed' => count($tests),
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ndx_status' => 'error',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    })->name('test-ndx');
});
