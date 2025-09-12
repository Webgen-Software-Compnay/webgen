<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CommonComplaintController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.api_base_url');
    }

    /**
     * Show the form for creating a new complaint
     */
    public function create(Request $request)
    {
        try {
            // Get parameters from request
            $categoryId = $request->get('category_id');
            $categorySlug = $request->get('category');
            $categoryName = $request->get('category_name');
            $commonComplaintId = $request->get('common_complaint_id');
            $commonComplaintTitle = $request->get('common_complaint_title');
            $type = $request->get('type', 'custom'); // custom or common
            
            // Initialize category with fallback data first
            $category = [
                'id' => $categoryId ?: 1,
                'name' => $categoryName ?: ucfirst($categorySlug ?: 'General'),
                'slug' => $categorySlug ?: strtolower($categoryName ?: 'general'),
                'description' => 'Category for ' . ($categoryName ?: $categorySlug ?: 'general complaints'),
                'category_type' => 'main'
            ];
            
            // Initialize common complaint and form structure
            $commonComplaint = null;
            $formStructure = [];
            
            // If this is a common complaint, fetch its details and form structure
            if ($commonComplaintId) {
                try {
                    // Use the new form data endpoint
                    $response = Http::timeout(10)->get("{$this->apiBaseUrl}/common-complaints/{$commonComplaintId}/form-data");
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data['data'])) {
                            $commonComplaint = $data['data']['complaint'];
                            $formStructure = $data['data']['form_structure'] ?? [];
                            
                            // Update category info if available from the complaint
                            if (isset($commonComplaint['category']) && $commonComplaint['category']) {
                                $category = [
                                    'id' => $commonComplaint['category']['id'],
                                    'name' => $commonComplaint['category']['name'],
                                    'slug' => $commonComplaint['category']['slug'] ?? '',
                                    'description' => $commonComplaint['category']['description'] ?? '',
                                    'category_type' => 'main'
                                ];
                            }
                        }
                    } else {
                        // If specific endpoint fails, try to get from sample data
                        $sampleResponse = Http::timeout(10)->get("{$this->apiBaseUrl}/common-complaints/sample", [
                            'category_id' => $categoryId
                        ]);
                        
                        if ($sampleResponse->successful()) {
                            $sampleData = $sampleResponse->json();
                            if (isset($sampleData['data']) && is_array($sampleData['data'])) {
                                // Find the specific complaint in sample data
                                $foundComplaint = collect($sampleData['data'])->firstWhere('id', $commonComplaintId);
                                if ($foundComplaint) {
                                    $commonComplaint = $foundComplaint;
                                    
                                    // Parse form structure from sample data
                                    if (isset($commonComplaint['form_data']) && !empty($commonComplaint['form_data'])) {
                                        if (is_array($commonComplaint['form_data'])) {
                                            $formStructure = $commonComplaint['form_data'];
                                        } elseif (is_string($commonComplaint['form_data'])) {
                                            $formStructure = json_decode($commonComplaint['form_data'], true) ?: [];
                                        }
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::info('Failed to fetch common complaint details', [
                        'common_complaint_id' => $commonComplaintId,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Try to fetch detailed category information from API if we have an ID
            if ($categoryId) {
                try {
                    $response = Http::timeout(10)->get("{$this->apiBaseUrl}/complaint-category/{$categoryId}");
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data['status']) && $data['status'] === 'success' && isset($data['data'])) {
                            $apiCategory = $data['data'];
                            // Update category with API data, keeping fallback values for missing fields
                            $category = [
                                'id' => $apiCategory['id'] ?? $category['id'],
                                'name' => $apiCategory['name'] ?? $category['name'],
                                'slug' => $apiCategory['slug'] ?? $category['slug'],
                                'description' => $apiCategory['description'] ?? $category['description'],
                                'category_type' => $apiCategory['category_type'] ?? $category['category_type']
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    // Continue with fallback data
                    Log::info('Failed to fetch category details from API', [
                        'category_id' => $categoryId,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Fetch other common complaints for this category (for reference)
            $otherCommonComplaints = [];
            if ($category['id']) {
                try {
                    $response = Http::timeout(10)->get("{$this->apiBaseUrl}/common-complaints", [
                        'category_id' => $category['id'],
                        'status' => 'active'
                    ]);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data['data'])) {
                            $otherCommonComplaints = $data['data'];
                            
                            // Remove current common complaint from the list
                            if ($commonComplaintId) {
                                $otherCommonComplaints = array_filter($otherCommonComplaints, function($complaint) use ($commonComplaintId) {
                                    return $complaint['id'] != $commonComplaintId;
                                });
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Continue with empty complaints
                    Log::info('Failed to fetch other common complaints', [
                        'category_id' => $category['id'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            return view('user.complaint.create', compact('category', 'commonComplaint', 'formStructure', 'otherCommonComplaints', 'type'));
            
        } catch (\Exception $e) {
            Log::error('Error loading complaint creation form: ' . $e->getMessage());
            
            // Fallback with minimal data ensuring all required fields exist
            $category = [
                'id' => $request->get('category_id', 1),
                'name' => $request->get('category_name', 'General'),
                'slug' => $request->get('category', 'general'),
                'description' => 'General complaints category',
                'category_type' => 'main'
            ];
            
            return view('user.complaint.create', [
                'category' => $category,
                'commonComplaint' => null,
                'formStructure' => [],
                'otherCommonComplaints' => [],
                'type' => 'custom'
            ]);
        }
    }

    /**
     * Store a new complaint
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'common_complaint_id' => 'nullable|integer',
            'dynamic_fields' => 'nullable|array',
        ]);

        try {
            $data = [];
            
            // Handle dynamic fields from common complaint forms
            if ($request->has('dynamic_fields') && is_array($request->dynamic_fields)) {
                // Include dynamic fields in the submission
                foreach ($request->dynamic_fields as $fieldName => $fieldValue) {
                    $data["field_{$fieldName}"] = $fieldValue;
                }
                
                // Set title and description from dynamic fields if they exist
                $data['title'] = $request->dynamic_fields['title'] ?? 'Dynamic Form Submission';
                $data['description'] = $request->dynamic_fields['description'] ?? 'Submitted through dynamic form';
            } else {
                // Handle standard form fields for custom complaints
                $request->validate([
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'location' => 'nullable|string|max:255',
                    'priority' => 'in:low,medium,high',
                    'contact_name' => 'nullable|string|max:255',
                    'contact_phone' => 'nullable|string|max:20',
                    'contact_email' => 'nullable|email'
                ]);
                
                $data = [
                    'title' => $request->title,
                    'description' => $request->description,
                    'location' => $request->location,
                    'priority' => $request->priority ?? 'medium',
                    'contact_name' => $request->contact_name,
                    'contact_phone' => $request->contact_phone,
                    'contact_email' => $request->contact_email,
                ];
            }
            
            // Common fields for all submissions
            $data['category_id'] = $request->category_id;
            $data['common_complaint_id'] = $request->common_complaint_id;
            $data['submission_type'] = $request->has('dynamic_fields') ? 'common' : 'custom';
            $data['submitted_at'] = now()->toISOString();

            $response = Http::post("{$this->apiBaseUrl}/complaints", $data);

            if ($response->successful()) {
                $responseData = $response->json();
                
                return redirect()->route('complaint.success')
                    ->with('success', 'Your complaint has been submitted successfully!')
                    ->with('complaint_id', $responseData['data']['id'] ?? null);
            }

            return back()->withErrors([
                'error' => 'Failed to submit complaint. Please try again.'
            ])->withInput();

        } catch (\Exception $e) {
            Log::error('Error submitting complaint: ' . $e->getMessage());
            
            return back()->withErrors([
                'error' => 'An error occurred while submitting your complaint. Please try again.'
            ])->withInput();
        }
    }

    /**
     * Get form data for a specific common complaint (AJAX endpoint)
     */
    public function getFormData($id)
    {
        try {
            // Call the new API endpoint for form data
            $response = Http::timeout(10)->get("{$this->apiBaseUrl}/common-complaints/{$id}/form-data");
            
            if ($response->successful()) {
                $data = $response->json();
                
                return response()->json([
                    'success' => true,
                    'data' => $data['data'] ?? null,
                    'message' => $data['message'] ?? 'Form data retrieved successfully'
                ]);
            } else {
                // Fallback to sample data
                $sampleResponse = Http::timeout(10)->get("{$this->apiBaseUrl}/common-complaints/sample");
                
                if ($sampleResponse->successful()) {
                    $sampleData = $sampleResponse->json();
                    if (isset($sampleData['data']) && is_array($sampleData['data'])) {
                        // Find the specific complaint in sample data
                        $foundComplaint = collect($sampleData['data'])->firstWhere('id', (int)$id);
                        if ($foundComplaint) {
                            $formStructure = [];
                            if (isset($foundComplaint['form_data'])) {
                                if (is_array($foundComplaint['form_data'])) {
                                    $formStructure = $foundComplaint['form_data'];
                                } elseif (is_string($foundComplaint['form_data'])) {
                                    $formStructure = json_decode($foundComplaint['form_data'], true) ?: [];
                                }
                            }
                            
                            return response()->json([
                                'success' => true,
                                'data' => [
                                    'complaint' => [
                                        'id' => $foundComplaint['id'],
                                        'title' => $foundComplaint['title'],
                                        'description' => $foundComplaint['description'] ?? '',
                                        'category' => $foundComplaint['category'] ?? null,
                                    ],
                                    'form_structure' => $formStructure,
                                    'has_form' => !empty($formStructure)
                                ],
                                'message' => 'Form data retrieved from sample data'
                            ]);
                        }
                    }
                }
                
                return response()->json([
                    'success' => false,
                    'error' => 'Could not retrieve form data for this complaint',
                    'message' => 'The API request failed and no sample data was found'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching form data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch form data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show complaint submission success page
     */
    public function success()
    {
        return view('user.complaint.success');
    }
}
