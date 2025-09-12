<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ApiCallerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $apiService;

    public function __construct(ApiCallerService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        try {
            // Fetch alerts from the API
            $response = $this->apiService->get(env('ONE_SRI_LANKA_API_URL') . '/alerts');

            $alerts = collect();
            if ($response && isset($response['success']) && $response['success']) {
                // Get only active alerts and group by category
                $allAlerts = collect($response['data'] ?? []);
                $alerts = $allAlerts->where('status', 'active')->groupBy('category');
            }

            return view('user.dashboard', compact('alerts'));
        } catch (\Exception $e) {
            // Return view with empty alerts collection if API fails
            return view('user.dashboard', ['alerts' => collect()]);
        }
    }

    /**
     * Show user action page with active complaint categories
     */
    public function action()
    {
        try {
            $categories = [];
            $apiBaseUrl = config('app.api_base_url');
            
            // Try to fetch active categories from API
            try {
                $apiUrl = "{$apiBaseUrl}/complaint-category/get-active-ui";
                
                $response = Http::timeout(10)->get($apiUrl);
                Log::info('ceewc',["response"=>$response]);
                 // --- IGNORE ---
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['status']) && $data['status'] === 'success' && isset($data['data'])) {
                        // The API response has nested data structure: data.data
                        $responseData = $data['data'];
                        
                        // Use all_categories and filter out main categories
                        if (isset($responseData['all_categories'])) {
                            $apiCategories = $responseData['all_categories'];
                        } else {
                            // Fallback to responseData directly
                            $apiCategories = $responseData;
                        }
                        
                        if (is_array($apiCategories) && !empty($apiCategories)) {
                            // Map the already formatted API data and filter out main categories
                            $categories = collect($apiCategories)
                                ->filter(function($category) {
                                    return ($category['category_type'] ?? 'main') !== 'main';
                                })
                                ->map(function($category) {
                                    return [
                                        'id' => $category['id'],
                                        'name' => $category['name'],
                                        'slug' => $category['slug'],
                                        'description' => $category['description'] ?? '',
                                        'category_type' => $category['category_type'] ?? 'main'
                                    ];
                                })
                                ->values()
                                ->toArray();
                        }
                    }
                }
            } catch (\Exception $apiException) {
                // Silent fail, will use fallback
            }
            
            // Fallback to public API if main API fails
            if (empty($categories)) {
                try {
                    $apiUrl = "{$apiBaseUrl}/public/complaint-categories/active-ui";
                    $response = Http::timeout(10)->get($apiUrl);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        
                        if (isset($data['status']) && $data['status'] === 'success' && isset($data['data'])) {
                            $responseData = $data['data'];
                            
                            // Handle structured response with all_categories, filter out main categories
                            if (isset($responseData['all_categories'])) {
                                $allCategories = $responseData['all_categories'];
                                
                                foreach ($allCategories as $category) {
                                    // Skip main categories
                                    if (($category['category_type'] ?? 'main') === 'main') {
                                        continue;
                                    }
                                    
                                    $categories[] = [
                                        'id' => $category['id'],
                                        'name' => $category['name'],
                                        'slug' => $category['slug'],
                                        'description' => $category['description'] ?? '',
                                        'category_type' => $category['category_type'] ?? 'main'
                                    ];
                                }
                            }
                        }
                    }
                } catch (\Exception $publicException) {
                    // Silent fail, will use ultimate fallback
                }
            }
            
            // Ultimate fallback categories (non-main categories only)
            if (empty($categories)) {
                $categories = [
                    ['id' => 101, 'name' => 'Fast Food', 'slug' => 'fast-food', 'description' => 'Fast food restaurant issues', 'category_type' => 'sub'],
                    ['id' => 102, 'name' => 'Public Bus', 'slug' => 'public-bus', 'description' => 'Public bus service issues', 'category_type' => 'sub'],
                    ['id' => 103, 'name' => 'Primary School', 'slug' => 'primary-school', 'description' => 'Primary school problems', 'category_type' => 'sub'],
                    ['id' => 104, 'name' => 'Hospital', 'slug' => 'hospital', 'description' => 'Hospital service issues', 'category_type' => 'sub'],
                    ['id' => 105, 'name' => 'TV Broadcasting', 'slug' => 'tv-broadcasting', 'description' => 'Television broadcasting issues', 'category_type' => 'sub'],
                    ['id' => 106, 'name' => 'Retail Store', 'slug' => 'retail-store', 'description' => 'Retail store service issues', 'category_type' => 'sub']
                ];
            }
            
            return view('user.action', compact('categories'));
            
        } catch (\Exception $e) {
            // Return with empty categories if everything fails
            return view('user.action', ['categories' => []]);
        }
    }
}
