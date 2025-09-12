<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class CommonComplaintController extends Controller
{
    private $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.api_base_url');
        
        // Log API configuration for debugging
        Log::info('CommonComplaintController initialized', [
            'api_base_url' => $this->apiBaseUrl
        ]);
        
        if (empty($this->apiBaseUrl)) {
            Log::warning('API_BASE_URL not configured in environment');
        }
    }

    /**
     * Display the common complaints management page
     */
    public function index()
    {
        return view('admin.pages.complaints.common-complaints.index');
    }

    /**
     * Show the form for creating a new common complaint
     */
    public function create()
    {
        try {
            $categories = [];
            
            // Try to fetch categories from API using the correct endpoint
            try {
                $apiUrl = "{$this->apiBaseUrl}/complaint-category/get-active";
                Log::info('Attempting to fetch categories from API', ['url' => $apiUrl]);
                
                $response = Http::timeout(10)->get($apiUrl);
                
                Log::info('API Response received', [
                    'url' => $apiUrl,
                    'status' => $response->status(),
                    'successful' => $response->successful(),
                    'body_preview' => substr($response->body(), 0, 200)
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $apiCategories = $data['data'] ?? [];
                    
                    Log::info('API Response parsed', [
                        'data_structure' => is_array($data) ? array_keys($data) : 'not_array',
                        'categories_count' => is_array($apiCategories) ? count($apiCategories) : 'not_array',
                        'first_category' => !empty($apiCategories) ? $apiCategories[0] : null
                    ]);
                    
                    if (is_array($apiCategories) && !empty($apiCategories)) {
                        $categories = $apiCategories;
                        Log::info('Successfully loaded categories from API', ['count' => count($categories)]);
                    }
                } else {
                    Log::warning('API request failed', [
                        'url' => $apiUrl,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                }
            } catch (\Exception $apiException) {
                Log::error('Primary API endpoint error', [
                    'url' => $apiUrl ?? 'undefined',
                    'message' => $apiException->getMessage(),
                    'code' => $apiException->getCode(),
                    'trace' => $apiException->getTraceAsString()
                ]);
            }
            
            // Fallback to get-all endpoint if get-active fails
            if (empty($categories)) {
                try {
                    $apiUrl = "{$this->apiBaseUrl}/complaint-category/get-all";
                    Log::info('Attempting fallback API call', ['url' => $apiUrl]);
                    
                    $response = Http::timeout(10)->get($apiUrl);
                    
                    Log::info('Fallback API Response received', [
                        'url' => $apiUrl,
                        'status' => $response->status(),
                        'successful' => $response->successful(),
                        'body_preview' => substr($response->body(), 0, 200)
                    ]);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        $apiCategories = $data['data'] ?? [];
                        
                        if (is_array($apiCategories) && !empty($apiCategories)) {
                            // Filter only active categories if data includes status
                            $categories = array_filter($apiCategories, function($category) {
                                return !isset($category['status']) || $category['status'] === 'active';
                            });
                            Log::info('Successfully loaded categories from fallback API', [
                                'total_count' => count($apiCategories),
                                'active_count' => count($categories)
                            ]);
                        }
                    } else {
                        Log::warning('Fallback API request failed', [
                            'url' => $apiUrl,
                            'status' => $response->status(),
                            'body' => $response->body()
                        ]);
                    }
                } catch (\Exception $fallbackException) {
                    Log::error('Fallback API endpoint error', [
                        'url' => $apiUrl ?? 'undefined',
                        'message' => $fallbackException->getMessage(),
                        'code' => $fallbackException->getCode()
                    ]);
                }
            }
            
            // Try public endpoint as additional fallback
            if (empty($categories)) {
                try {
                    $apiUrl = "{$this->apiBaseUrl}/public/complaint-categories";
                    Log::info('Attempting public API call', ['url' => $apiUrl]);
                    
                    $response = Http::timeout(10)->get($apiUrl);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        Log::info('Public API response structure', ['keys' => array_keys($data), 'data_structure' => $data]);
                        
                        // The public endpoint returns different structure: {success: true, data: {main_categories: [], total_main: X, total_sub: Y}}
                        if (isset($data['success']) && $data['success'] && isset($data['data'])) {
                            $responseData = $data['data'];
                            
                            // If it has main_categories structure
                            if (isset($responseData['main_categories'])) {
                                $mainCategories = $responseData['main_categories'];
                                $categories = [];
                                
                                foreach ($mainCategories as $mainCategory) {
                                    // Add main category
                                    $categories[] = [
                                        'id' => $mainCategory['id'],
                                        'name' => $mainCategory['name'],
                                        'description' => $mainCategory['description'] ?? '',
                                        'category_type' => 'main',
                                        'status' => 'active'
                                    ];
                                    
                                    // Add sub-categories if they exist
                                    if (isset($mainCategory['sub_categories']) && is_array($mainCategory['sub_categories'])) {
                                        foreach ($mainCategory['sub_categories'] as $subCategory) {
                                            $categories[] = [
                                                'id' => $subCategory['id'],
                                                'name' => $subCategory['name'],
                                                'description' => $subCategory['description'] ?? '',
                                                'category_type' => 'sub',
                                                'parent_id' => $mainCategory['id'],
                                                'status' => 'active'
                                            ];
                                        }
                                    }
                                }
                                
                                Log::info('Successfully loaded categories from public API (structured)', [
                                    'main_count' => $responseData['total_main'] ?? 0,
                                    'sub_count' => $responseData['total_sub'] ?? 0,
                                    'total_processed' => count($categories)
                                ]);
                            }
                            // If it's a direct array structure
                            elseif (is_array($responseData)) {
                                $categories = array_filter($responseData, function($category) {
                                    return !isset($category['status']) || $category['status'] === 'active';
                                });
                                Log::info('Successfully loaded categories from public API (direct)', [
                                    'count' => count($categories)
                                ]);
                            }
                        }
                    } else {
                        Log::warning('Public API request failed', [
                            'status' => $response->status(),
                            'body' => $response->body()
                        ]);
                    }
                } catch (\Exception $publicException) {
                    Log::error('Public API endpoint error', [
                        'url' => $apiUrl ?? 'undefined',
                        'message' => $publicException->getMessage()
                    ]);
                }
            }
            
            // Final fallback to sample categories if API is completely unavailable
            if (empty($categories)) {
                $categories = [
                    ['id' => 1, 'name' => 'Transportation', 'description' => 'Bus, train, and public transport issues', 'category_type' => 'main', 'status' => 'active'],
                    ['id' => 2, 'name' => 'Infrastructure', 'description' => 'Roads, bridges, and public infrastructure', 'category_type' => 'main', 'status' => 'active'],
                    ['id' => 3, 'name' => 'Utilities', 'description' => 'Water, electricity, and other utilities', 'category_type' => 'main', 'status' => 'active'],
                    ['id' => 4, 'name' => 'Healthcare', 'description' => 'Hospital and medical service issues', 'category_type' => 'main', 'status' => 'active'],
                    ['id' => 5, 'name' => 'Education', 'description' => 'School and educational facility problems', 'category_type' => 'main', 'status' => 'active'],
                    ['id' => 6, 'name' => 'Environment', 'description' => 'Waste management and environmental issues', 'category_type' => 'main', 'status' => 'active'],
                    ['id' => 7, 'name' => 'Public Safety', 'description' => 'Security and safety concerns', 'category_type' => 'main', 'status' => 'active'],
                    ['id' => 8, 'name' => 'Government Services', 'description' => 'Administrative and government service issues', 'category_type' => 'main', 'status' => 'active']
                ];
                Log::info('Using fallback categories', ['count' => count($categories)]);
            }
            
            return view('admin.pages.complaints.common-complaints.create', compact('categories') + [
                'title' => 'Common Complaints',
                'subtitle' => 'Create New Common Complaint'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading create form: ' . $e->getMessage());
            
            // Ultimate fallback categories
            $categories = [
                ['id' => 1, 'name' => 'Transportation', 'description' => 'Bus, train, and public transport issues', 'category_type' => 'main', 'status' => 'active'],
                ['id' => 2, 'name' => 'Infrastructure', 'description' => 'Roads, bridges, and public infrastructure', 'category_type' => 'main', 'status' => 'active'],
                ['id' => 3, 'name' => 'Utilities', 'description' => 'Water, electricity, and other utilities', 'category_type' => 'main', 'status' => 'active'],
                ['id' => 4, 'name' => 'Healthcare', 'description' => 'Hospital and medical service issues', 'category_type' => 'main', 'status' => 'active'],
                ['id' => 5, 'name' => 'Education', 'description' => 'School and educational facility problems', 'category_type' => 'main', 'status' => 'active'],
                ['id' => 6, 'name' => 'Environment', 'description' => 'Waste management and environmental issues', 'category_type' => 'main', 'status' => 'active'],
                ['id' => 7, 'name' => 'Public Safety', 'description' => 'Security and safety concerns', 'category_type' => 'main', 'status' => 'active'],
                ['id' => 8, 'name' => 'Government Services', 'description' => 'Administrative and government service issues', 'category_type' => 'main', 'status' => 'active']
            ];
            
            return view('admin.pages.complaints.common-complaints.create', compact('categories') + [
                'title' => 'Common Complaints',
                'subtitle' => 'Create New Common Complaint'
            ]);
        }
    }

    /**
     * Get common complaints data for DataTables (fallback method)
     */
    public function get(Request $request): JsonResponse
    {
        try {
            // Add a short timeout and try the API first
            $response = Http::timeout(3)->get("{$this->apiBaseUrl}/common-complaints", [
                'draw' => $request->get('draw', 1),
                'start' => $request->get('start', 0),
                'length' => $request->get('length', 10),
                'search' => $request->get('search'),
                'order' => $request->get('order'),
                'columns' => $request->get('columns')
            ]);

            Log::info('API Response', ['status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                return response()->json($response->json());
            }
        } catch (\Exception $e) {
            // Log the API failure but don't fail the request
            Log::info('API not available, using sample data: ' . $e->getMessage());
        }
        
        // Always return sample data if API is not available
        return $this->getSampleData($request);
    }

    /**
     * Show the form for editing the specified common complaint
     */
    public function edit($id)
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/common-complaints/{$id}");
            
            if ($response->successful()) {
                $complaint = $response->json()['data'] ?? null;
            } else {
                // Fallback to sample data
                $complaint = $this->getSampleComplaint($id);
            }
            
            return view('admin.pages.complaints.common-complaints.edit', compact('complaint'));
        } catch (\Exception $e) {
            Log::error('Error fetching complaint for edit: ' . $e->getMessage());
            $complaint = $this->getSampleComplaint($id);
            return view('admin.pages.complaints.common-complaints.edit', compact('complaint'));
        }
    }

    /**
     * Show form builder for the specified complaint
     */
    public function formBuilder($id)
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/common-complaints/{$id}");
            
            if ($response->successful()) {
                $complaint = $response->json()['data'] ?? null;
            } else {
                // Fallback to sample data
                $complaint = $this->getSampleComplaint($id);
            }
            
            return view('admin.pages.complaints.common-complaints.form-builder', compact('complaint'));
        } catch (\Exception $e) {
            Log::error('Error fetching complaint for form builder: ' . $e->getMessage());
            $complaint = $this->getSampleComplaint($id);
            return view('admin.pages.complaints.common-complaints.form-builder', compact('complaint'));
        }
    }

    /**
     * Get usage analytics for a specific complaint (alias for existing method)
     */
    public function usage($id): JsonResponse
    {
        return $this->getUsageAnalytics($id);
    }

    /**
     * Get sample data when API is not available
     */
    private function getSampleData(Request $request): JsonResponse
    {
        $complaints = [
            [
                'id' => 1,
                'title' => 'Bus Service Delay',
                'category' => 'Transportation',
                'description' => 'Issues related to delayed bus services and poor scheduling',
                'form_status' => 'configured',
                'status' => 'active',
                'priority' => 'medium',
                'usage_count' => 245,
                'created_at' => '2024-01-15 10:30:00',
                'updated_at' => '2024-01-20 14:45:00'
            ],
            [
                'id' => 2,
                'title' => 'Road Damage',
                'category' => 'Infrastructure',
                'description' => 'Potholes, damaged roads, and poor road conditions',
                'form_status' => 'pending',
                'status' => 'active',
                'priority' => 'high',
                'usage_count' => 189,
                'created_at' => '2024-01-12 09:15:00',
                'updated_at' => '2024-01-18 16:20:00'
            ],
            [
                'id' => 3,
                'title' => 'Water Supply Issue',
                'category' => 'Utilities',
                'description' => 'Water shortage, quality issues, and supply interruptions',
                'form_status' => 'configured',
                'status' => 'active',
                'priority' => 'high',
                'usage_count' => 156,
                'created_at' => '2024-01-10 11:00:00',
                'updated_at' => '2024-01-19 13:30:00'
            ],
            [
                'id' => 4,
                'title' => 'Hospital Queue Management',
                'category' => 'Healthcare',
                'description' => 'Long waiting times and poor queue management in hospitals',
                'form_status' => 'incomplete',
                'status' => 'draft',
                'priority' => 'medium',
                'usage_count' => 78,
                'created_at' => '2024-01-08 08:45:00',
                'updated_at' => '2024-01-16 10:15:00'
            ],
            [
                'id' => 5,
                'title' => 'School Facility Issues',
                'category' => 'Education',
                'description' => 'Problems with school buildings, equipment, and facilities',
                'form_status' => 'configured',
                'status' => 'active',
                'priority' => 'low',
                'usage_count' => 92,
                'created_at' => '2024-01-05 14:20:00',
                'updated_at' => '2024-01-17 09:40:00'
            ],
            [
                'id' => 6,
                'title' => 'Waste Management',
                'category' => 'Environment',
                'description' => 'Garbage collection delays and improper waste disposal',
                'form_status' => 'configured',
                'status' => 'active',
                'priority' => 'medium',
                'usage_count' => 134,
                'created_at' => '2024-01-03 12:10:00',
                'updated_at' => '2024-01-15 15:25:00'
            ]
        ];

        // Handle DataTables server-side processing parameters
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search');
        $order = $request->get('order', []);
        $columns = $request->get('columns', []);

        // Apply search filtering
        $filteredComplaints = $complaints;
        if (!empty($search['value'])) {
            $searchValue = strtolower($search['value']);
            $filteredComplaints = array_filter($complaints, function($complaint) use ($searchValue) {
                return stripos($complaint['title'], $searchValue) !== false ||
                       stripos($complaint['category'], $searchValue) !== false ||
                       stripos($complaint['status'], $searchValue) !== false;
            });
        }

        // Apply column-specific filtering
        foreach ($columns as $index => $column) {
            if (!empty($column['search']['value'])) {
                $columnSearch = $column['search']['value'];
                $columnName = $column['data'] ?? '';
                
                $filteredComplaints = array_filter($filteredComplaints, function($complaint) use ($columnName, $columnSearch) {
                    if (isset($complaint[$columnName])) {
                        return stripos($complaint[$columnName], $columnSearch) !== false;
                    }
                    return true;
                });
            }
        }

        $totalRecords = count($complaints);
        $filteredRecords = count($filteredComplaints);

        // Apply sorting
        if (!empty($order)) {
            $orderColumn = $columns[$order[0]['column']]['data'] ?? 'title';
            $orderDir = $order[0]['dir'] ?? 'asc';
            
            usort($filteredComplaints, function($a, $b) use ($orderColumn, $orderDir) {
                $valueA = $a[$orderColumn] ?? '';
                $valueB = $b[$orderColumn] ?? '';
                
                if ($orderDir === 'desc') {
                    return strcmp($valueB, $valueA);
                }
                return strcmp($valueA, $valueB);
            });
        }

        // Apply pagination
        $filteredComplaints = array_slice($filteredComplaints, $start, $length);

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => array_values($filteredComplaints)
        ]);
    }

    /**
     * Get a single sample complaint by ID
     */
    private function getSampleComplaint($id): ?array
    {
        $complaints = $this->getSampleData(new Request())->getData(true)['data'] ?? [];
        return collect($complaints)->firstWhere('id', $id);
    }

    /**
     * Get all common complaints
     */
    public function getComplaints(Request $request): JsonResponse
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/common-complaints", [
                'page' => $request->get('page', 1),
                'per_page' => $request->get('per_page', 15),
                'search' => $request->get('search'),
                'category_id' => $request->get('category_id'),
                'status' => $request->get('status')
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch complaints from API'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error fetching common complaints: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to API service'
            ], 500);
        }
    }

    /**
     * Store a new common complaint
     */
    public function store(Request $request): JsonResponse
    {
        // Handle form_structure JSON string
        $requestData = $request->all();
        if (isset($requestData['form_structure'])) {
            if (is_string($requestData['form_structure'])) {
                // If it's a JSON string, decode it
                $decoded = json_decode($requestData['form_structure'], true);
                $requestData['form_structure'] = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($requestData['form_structure'])) {
                // If it's not an array and not a string, set to empty array
                $requestData['form_structure'] = [];
            }
        } else {
            // If not set, default to empty array
            $requestData['form_structure'] = [];
        }

        // Handle tags conversion from comma-separated string to array
        if (isset($requestData['tags'])) {
            if (is_string($requestData['tags'])) {
                // Convert comma-separated string to array
                $tags = array_map('trim', explode(',', $requestData['tags']));
                // Remove empty values
                $requestData['tags'] = array_filter($tags, function($tag) {
                    return !empty($tag);
                });
                // Re-index array
                $requestData['tags'] = array_values($requestData['tags']);
            } elseif (!is_array($requestData['tags'])) {
                // If it's not an array and not a string, set to empty array
                $requestData['tags'] = [];
            }
        } else {
            // If not set, default to empty array
            $requestData['tags'] = [];
        }

        // Filter out any empty/invalid form structure elements
        if (is_array($requestData['form_structure'])) {
            $requestData['form_structure'] = array_filter($requestData['form_structure'], function($field) {
                return is_array($field) && 
                       !empty($field['field_type']) && 
                       !empty($field['field_name']) && 
                       !empty($field['label']);
            });
            // Re-index array to avoid gaps
            $requestData['form_structure'] = array_values($requestData['form_structure']);
        }

        Log::info('Processing form structure and tags', [
            'original_form_structure' => $request->get('form_structure'),
            'processed_form_structure' => $requestData['form_structure'],
            'form_structure_count' => count($requestData['form_structure']),
            'original_tags' => $request->get('tags'),
            'processed_tags' => $requestData['tags'],
            'tags_count' => count($requestData['tags'])
        ]);

        // Add custom validation for form_structure (validate before field mapping)
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'description' => 'nullable|string',
            'priority' => 'in:low,medium,high',
            'status' => 'in:active,inactive,draft',
            'tags' => 'nullable|string', // Validate as string since it comes from form
            'estimated_resolution_time' => 'nullable|integer|min:1'
        ]);

        // Map form field names to API field names
        if (isset($requestData['category_id'])) {
            $requestData['complaint_category_id'] = $requestData['category_id'];
            unset($requestData['category_id']); // Remove the old field name
        }

        // Map form_structure to form_data for the API
        if (isset($requestData['form_structure'])) {
            $requestData['form_data'] = json_encode($requestData['form_structure']);
            unset($requestData['form_structure']); // Remove the old field name
        }

        // Only validate form structure fields if the array is not empty
        if (!empty($requestData['form_structure'])) {
            $structureValidator = Validator::make($requestData, [
                'form_structure.*.field_type' => 'required|string',
                'form_structure.*.field_name' => 'required|string',
                'form_structure.*.label' => 'required|string',
                'form_structure.*.required' => 'boolean',
            ]);
            
            if ($structureValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Form structure validation failed',
                    'errors' => $structureValidator->errors()
                ], 422);
            }
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Log::info('Sending complaint creation request', [
                'api_url' => "{$this->apiBaseUrl}/common-complaints",
                'data' => $requestData
            ]);

            $response = Http::post("{$this->apiBaseUrl}/common-complaints", $requestData);

            Log::info('API Response received', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Common complaint created successfully', [
                    'complaint_id' => $data['data']['id'] ?? null,
                    'title' => $requestData['title']
                ]);

                return response()->json($data);
            }

            // Log the failure details
            Log::error('API request failed', [
                'status' => $response->status(),
                'response_body' => $response->body(),
                'headers' => $response->headers()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create complaint',
                'api_error' => $response->body(),
                'status_code' => $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error creating common complaint: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $requestData
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to API service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a specific common complaint
     */
    public function show($id): JsonResponse
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/common-complaints/{$id}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Complaint not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error fetching common complaint: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to API service'
            ], 500);
        }
    }

    /**
     * Update a common complaint
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'category_id' => 'integer',
            'description' => 'nullable|string',
            'priority' => 'in:low,medium,high',
            'status' => 'in:active,inactive,draft',
            'form_structure' => 'nullable|array',
            'form_structure.*.field_type' => 'required_with:form_structure|string',
            'form_structure.*.field_name' => 'required_with:form_structure|string',
            'form_structure.*.label' => 'required_with:form_structure|string',
            'form_structure.*.required' => 'boolean',
            'tags' => 'nullable|array',
            'estimated_resolution_time' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Http::put("{$this->apiBaseUrl}/common-complaints/{$id}", $request->all());

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Common complaint updated successfully', [
                    'complaint_id' => $id,
                    'title' => $request->title
                ]);

                return response()->json($data);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update complaint'
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error updating common complaint: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to API service'
            ], 500);
        }
    }

    /**
     * Delete a common complaint
     */
    public function destroy($id): JsonResponse
    {
        try {
            $response = Http::delete("{$this->apiBaseUrl}/common-complaints/{$id}");

            if ($response->successful()) {
                Log::info('Common complaint deleted successfully', ['complaint_id' => $id]);
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete complaint'
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error deleting common complaint: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to API service'
            ], 500);
        }
    }

    /**
     * Get common complaint statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/common-complaints/statistics");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            // Return default statistics if API fails
            return response()->json([
                'success' => true,
                'data' => [
                    'total_complaints' => 0,
                    'active_complaints' => 0,
                    'with_forms' => 0,
                    'total_usage' => 0,
                    'monthly_usage' => 0,
                    'popular_categories' => [],
                    'recent_activity' => []
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching complaint statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to API service'
            ], 500);
        }
    }

    /**
     * Get usage analytics for a specific complaint
     */
    public function getUsageAnalytics($id): JsonResponse
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/common-complaints/{$id}/analytics");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Analytics not available'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error fetching usage analytics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to API service'
            ], 500);
        }
    }

    /**
     * Export common complaints data
     */
    public function export(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'format' => 'in:csv,xlsx,pdf',
            'category_id' => 'nullable|integer',
            'status' => 'nullable|in:active,inactive,draft',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Http::post("{$this->apiBaseUrl}/common-complaints/export", $request->all());

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Export failed'
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error exporting complaints: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to API service'
            ], 500);
        }
    }

    /**
     * Duplicate a common complaint
     */
    public function duplicate($id): JsonResponse
    {
        try {
            $response = Http::post("{$this->apiBaseUrl}/common-complaints/{$id}/duplicate");

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Common complaint duplicated successfully', [
                    'original_id' => $id,
                    'new_id' => $data['data']['id'] ?? null
                ]);

                return response()->json($data);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate complaint'
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error duplicating common complaint: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to API service'
            ], 500);
        }
    }

    /**
     * Get form structure for a complaint
     */
    public function getFormStructure($id): JsonResponse
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/common-complaints/{$id}/form-structure");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Form structure not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error fetching form structure: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to API service'
            ], 500);
        }
    }

    /**
     * Update form structure for a complaint
     */
    public function updateFormStructure(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'form_structure' => 'required|array',
            'form_structure.*.field_type' => 'required|string',
            'form_structure.*.field_name' => 'required|string',
            'form_structure.*.label' => 'required|string',
            'form_structure.*.required' => 'boolean',
            'form_structure.*.options' => 'nullable|array',
            'form_structure.*.validation_rules' => 'nullable|array',
            'form_structure.*.order' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Http::put("{$this->apiBaseUrl}/common-complaints/{$id}/form-structure", $request->all());

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Form structure updated successfully', [
                    'complaint_id' => $id,
                    'fields_count' => count($request->form_structure)
                ]);

                return response()->json($data);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update form structure'
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error updating form structure: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to API service'
            ], 500);
        }
    }

    public function getCategories(Request $request): JsonResponse
    {
        try {
            $search = $request->get('q', '');
            $limit = $request->get('limit', 20);
            
            // Try to fetch from API first
            $response = Http::timeout(5)->get("{$this->apiBaseUrl}/complaint-category/search", [
                'q' => $search,
                'limit' => $limit,
                'status' => 'active'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $categories = $data['data'] ?? [];
                
                // Format for Select2
                $formattedCategories = array_map(function($category) {
                    return [
                        'id' => $category['id'],
                        'text' => $category['name'],
                        'name' => $category['name'],
                        'description' => $category['description'] ?? '',
                        'category_type' => $category['category_type'] ?? 'main',
                        'parent_name' => isset($category['parent']) ? $category['parent']['name'] : null
                    ];
                }, $categories);
                
                return response()->json([
                    'success' => true,
                    'data' => $formattedCategories
                ]);
            }
            
            // Fallback to public endpoint if search fails
            $response = Http::timeout(5)->get("{$this->apiBaseUrl}/public/complaint-categories");
            
            if ($response->successful()) {
                $data = $response->json();
                $categories = [];
                
                if (isset($data['success']) && $data['success'] && isset($data['data'])) {
                    $responseData = $data['data'];
                    
                    // Handle structured response with main_categories
                    if (isset($responseData['main_categories'])) {
                        $mainCategories = $responseData['main_categories'];
                        
                        foreach ($mainCategories as $mainCategory) {
                            // Add main category
                            $categories[] = [
                                'id' => $mainCategory['id'],
                                'name' => $mainCategory['name'],
                                'description' => $mainCategory['description'] ?? '',
                                'category_type' => 'main',
                                'parent_name' => null
                            ];
                            
                            // Add sub-categories if they exist
                            if (isset($mainCategory['sub_categories']) && is_array($mainCategory['sub_categories'])) {
                                foreach ($mainCategory['sub_categories'] as $subCategory) {
                                    $categories[] = [
                                        'id' => $subCategory['id'],
                                        'name' => $subCategory['name'],
                                        'description' => $subCategory['description'] ?? '',
                                        'category_type' => 'sub',
                                        'parent_name' => $mainCategory['name']
                                    ];
                                }
                            }
                        }
                    }
                    // Handle direct array response
                    elseif (is_array($responseData)) {
                        $categories = $responseData;
                    }
                }
                
                // Apply search filter locally if needed
                if (!empty($search)) {
                    $categories = array_filter($categories, function($category) use ($search) {
                        return stripos($category['name'] ?? '', $search) !== false ||
                               stripos($category['description'] ?? '', $search) !== false;
                    });
                }
                
                // Limit results
                $categories = array_slice($categories, 0, $limit);
                
                // Format for Select2
                $formattedCategories = array_map(function($category) {
                    return [
                        'id' => $category['id'],
                        'text' => $category['name'],
                        'name' => $category['name'],
                        'description' => $category['description'] ?? '',
                        'category_type' => $category['category_type'] ?? 'main',
                        'parent_name' => $category['parent_name'] ?? null
                    ];
                }, $categories);
                
                return response()->json([
                    'success' => true,
                    'data' => $formattedCategories
                ]);
            }
            
            if ($response->successful()) {
                $data = $response->json();
                $categories = $data['data'] ?? [];
                
                // Apply search filter locally if API search failed
                if (!empty($search)) {
                    $categories = array_filter($categories, function($category) use ($search) {
                        return stripos($category['name'] ?? '', $search) !== false ||
                               stripos($category['description'] ?? '', $search) !== false;
                    });
                }
                
                // Limit results
                $categories = array_slice($categories, 0, $limit);
                
                // Format for Select2
                $formattedCategories = array_map(function($category) {
                    return [
                        'id' => $category['id'],
                        'text' => $category['name'],
                        'name' => $category['name'],
                        'description' => $category['description'] ?? '',
                        'category_type' => $category['category_type'] ?? 'main',
                        'parent_name' => isset($category['parent']) ? $category['parent']['name'] : null
                    ];
                }, $categories);
                
                return response()->json([
                    'success' => true,
                    'data' => $formattedCategories
                ]);
            }

            // Ultimate fallback to sample data with search
            $fallbackCategories = [
                ['id' => 1, 'name' => 'Transportation', 'description' => 'Bus, train, and public transport issues', 'category_type' => 'main'],
                ['id' => 2, 'name' => 'Infrastructure', 'description' => 'Roads, bridges, and public infrastructure', 'category_type' => 'main'],
                ['id' => 3, 'name' => 'Utilities', 'description' => 'Water, electricity, and other utilities', 'category_type' => 'main'],
                ['id' => 4, 'name' => 'Healthcare', 'description' => 'Hospital and medical service issues', 'category_type' => 'main'],
                ['id' => 5, 'name' => 'Education', 'description' => 'School and educational facility problems', 'category_type' => 'main'],
                ['id' => 6, 'name' => 'Environment', 'description' => 'Waste management and environmental issues', 'category_type' => 'main'],
                ['id' => 7, 'name' => 'Public Safety', 'description' => 'Security and safety concerns', 'category_type' => 'main'],
                ['id' => 8, 'name' => 'Government Services', 'description' => 'Administrative and government service issues', 'category_type' => 'main'],
                ['id' => 9, 'name' => 'Public Transport Delays', 'description' => 'Specific issues with bus and train delays', 'category_type' => 'sub', 'parent_id' => 1],
                ['id' => 10, 'name' => 'Road Maintenance', 'description' => 'Road repair and maintenance issues', 'category_type' => 'sub', 'parent_id' => 2],
                ['id' => 11, 'name' => 'Water Supply', 'description' => 'Water shortage and quality issues', 'category_type' => 'sub', 'parent_id' => 3],
                ['id' => 12, 'name' => 'Hospital Services', 'description' => 'Hospital queue and service issues', 'category_type' => 'sub', 'parent_id' => 4]
            ];
            
            // Apply search filter
            if (!empty($search)) {
                $fallbackCategories = array_filter($fallbackCategories, function($category) use ($search) {
                    return stripos($category['name'], $search) !== false ||
                           stripos($category['description'], $search) !== false;
                });
            }
            
            // Limit results
            $fallbackCategories = array_slice($fallbackCategories, 0, $limit);
            
            // Format for Select2
            $formattedCategories = array_map(function($category) use ($fallbackCategories) {
                $parentName = null;
                if (isset($category['parent_id'])) {
                    $parent = collect($fallbackCategories)->firstWhere('id', $category['parent_id']);
                    $parentName = $parent ? $parent['name'] : null;
                }
                
                return [
                    'id' => $category['id'],
                    'text' => $category['name'],
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'category_type' => $category['category_type'],
                    'parent_name' => $parentName
                ];
            }, array_values($fallbackCategories));

            return response()->json([
                'success' => true,
                'data' => $formattedCategories
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview a complaint form
     */
    public function previewForm($id): JsonResponse
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/public/forms/preview/{$id}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Form preview not available'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error getting form preview: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to API service'
            ], 500);
        }
    }
}
