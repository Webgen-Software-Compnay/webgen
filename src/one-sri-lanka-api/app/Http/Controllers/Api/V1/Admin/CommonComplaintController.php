<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommonComplaint;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Log;

class CommonComplaintController extends Controller
{
    public function __construct() {}

    /**
     * DataTables server-side processing endpoint
     */
    public function index(HttpRequest $request)
    {
        try {
            // Check if this is a simple API request for category filtering
            if ($request->has('category_id') && !$request->has('draw')) {
                return $this->getComplaintsByCategory($request);
            }

            $start = (int) $request->get('start', 0);
            $length = (int) $request->get('length', 10);
            $search = $request->get('search')['value'] ?? '';
            $orderColumn = (int) $request->get('order')[0]['column'] ?? 0;
            $orderDir = $request->get('order')[0]['dir'] ?? 'desc';

            // Define column mapping for ordering
            $columns = ['id', 'title', 'category.name', 'description', 'created_at'];
            $orderColumnName = $columns[$orderColumn] ?? 'id';

            // Base query with relationship
            $query = CommonComplaint::with('category');

            // Apply category filter if provided
            if ($request->has('category_id')) {
                $query->where('complaint_category_id', $request->get('category_id'));
            }

            // Apply status filter if provided
            if ($request->has('status')) {
                // Assuming there's a status field - adjust as needed
                $query->where('status', $request->get('status'));
            }

            // Apply search if provided
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Get total count before applying pagination
            $totalRecords = CommonComplaint::count();
            $filteredRecords = $query->count();

            // Apply ordering
            if ($orderColumnName === 'category.name') {
                $query->join('complaint_categories', 'common_complaints.complaint_category_id', '=', 'complaint_categories.id')
                    ->orderBy('complaint_categories.name', $orderDir)
                    ->select('common_complaints.*');
            } else {
                $query->orderBy($orderColumnName, $orderDir);
            }

            // Apply pagination
            $data = $query->offset($start)->limit($length)->get();

            // Format data for DataTables
            $formattedData = $data->map(function ($complaint) {
                return [
                    'id' => $complaint->id,
                    'title' => $complaint->title,
                    'category' => $complaint->category ? $complaint->category->name : 'N/A',
                    'description' => $complaint->description ? \Str::limit($complaint->description, 100) : 'No description',
                    'created_at' => $complaint->created_at ? $complaint->created_at->format('M j, Y g:i A') : 'N/A',
                    'actions' => $complaint->id // Will be processed by frontend
                ];
            });

            return response()->json([
                'draw' => (int) $request->get('draw', 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            Log::error('CommonComplaint DataTables Error: ' . $e->getMessage());

            return response()->json([
                'draw' => (int) $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to load data'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource
     */
    public function create()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Create form data retrieved successfully.'
        ]);
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(HttpRequest $request)
    {
        try {
            // First validate the basic fields
            $validated = $request->validate([
                'complaint_category_id' => 'required|exists:complaint_categories,id',
                'district_id' => 'nullable|exists:districts,id',
                'title'                 => 'required|string|max:255',
                'description'           => 'nullable|string',
                'form_data'             => 'nullable|string|json',
            ]);

            // If form_data is a JSON string, validate that it's proper JSON
            if (isset($validated['form_data']) && !empty($validated['form_data'])) {
                $decodedFormData = json_decode($validated['form_data'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Invalid JSON format in form_data field.',
                        'error'   => 'JSON decode error: ' . json_last_error_msg()
                    ], 422);
                }
                
                // Optionally validate the structure of form_data
                if (is_array($decodedFormData)) {
                    foreach ($decodedFormData as $index => $field) {
                        if (!is_array($field)) {
                            return response()->json([
                                'status'  => 'error',
                                'message' => "Invalid form field structure at index {$index}.",
                                'error'   => 'Each form field must be an object/array'
                            ], 422);
                        }
                        
                        // Check required field properties
                        $requiredFields = ['field_type', 'field_name', 'label'];
                        foreach ($requiredFields as $requiredField) {
                            if (!isset($field[$requiredField]) || empty($field[$requiredField])) {
                                return response()->json([
                                    'status'  => 'error',
                                    'message' => "Missing required field '{$requiredField}' at form field index {$index}.",
                                    'error'   => 'Form field validation failed'
                                ], 422);
                            }
                        }
                    }
                }
            }

            $complaint = CommonComplaint::create($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Complaint created successfully.',
                'data'    => $complaint->load('category'),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('CommonComplaint Store Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create complaint.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource
     */
    public function show(HttpRequest $request, $id)
    {
        try {
            $complaint = CommonComplaint::with('category')->findOrFail($id);

            return response()->json([
                'status'  => 'success',
                'data'    => $complaint->append('district_id'),
            ]);
        } catch (\Exception $e) {
            Log::error('CommonComplaint Show Error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Complaint not found.',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get form data for a specific common complaint
     */
    public function getFormData($id)
    {
        try {
            $complaint = CommonComplaint::with('category')->findOrFail($id);
            
            // Parse form_data
            $formStructure = [];
            if (!empty($complaint->form_data)) {
                if (is_string($complaint->form_data)) {
                    // Try first decode
                    $decoded = json_decode($complaint->form_data, true);
                    
                    if (is_array($decoded)) {
                        $formStructure = $decoded;
                    } elseif (is_string($decoded)) {
                        // Double-encoded JSON - decode again
                        $formStructure = json_decode($decoded, true) ?: [];
                    }
                } elseif (is_array($complaint->form_data)) {
                    $formStructure = $complaint->form_data;
                }
            }

            // Return form data with complaint info
            return response()->json([
                'status' => 'success',
                'data' => [
                    'complaint' => [
                        'id' => $complaint->id,
                        'title' => $complaint->title,
                        'description' => $complaint->description,
                        'category' => $complaint->category ? [
                            'id' => $complaint->category->id,
                            'name' => $complaint->category->name,
                            'slug' => $complaint->category->slug,
                            'description' => $complaint->category->description,
                        ] : null,
                    ],
                    'form_structure' => $formStructure,
                    'has_form' => !empty($formStructure)
                ],
                'message' => 'Form data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('CommonComplaint Form Data Error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Could not retrieve form data.',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit($id)
    {
        try {
            $complaint = CommonComplaint::with('category')->findOrFail($id);

            return response()->json([
                'status'  => 'success',
                'data'    => $complaint,
            ]);
        } catch (\Exception $e) {
            Log::error('CommonComplaint Edit Error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Complaint not found.',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage
     */
    public function update(HttpRequest $request, $id)
    {
        try {
            $complaint = CommonComplaint::findOrFail($id);

            // First validate the basic fields
            $validated = $request->validate([
                'complaint_category_id' => 'required|exists:complaint_categories,id',
                'district_id' => 'nullable|exists:districts,id',
                'title'                 => 'required|string|max:255',
                'description'           => 'nullable|string',
                'form_data'             => 'nullable|string|json',
            ]);

            // If form_data is a JSON string, validate that it's proper JSON
            if (isset($validated['form_data']) && !empty($validated['form_data'])) {
                $decodedFormData = json_decode($validated['form_data'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Invalid JSON format in form_data field.',
                        'error'   => 'JSON decode error: ' . json_last_error_msg()
                    ], 422);
                }
                
                // Optionally validate the structure of form_data
                if (is_array($decodedFormData)) {
                    foreach ($decodedFormData as $index => $field) {
                        if (!is_array($field)) {
                            return response()->json([
                                'status'  => 'error',
                                'message' => "Invalid form field structure at index {$index}.",
                                'error'   => 'Each form field must be an object/array'
                            ], 422);
                        }
                        
                        // Check required field properties
                        $requiredFields = ['field_type', 'field_name', 'label'];
                        foreach ($requiredFields as $requiredField) {
                            if (!isset($field[$requiredField]) || empty($field[$requiredField])) {
                                return response()->json([
                                    'status'  => 'error',
                                    'message' => "Missing required field '{$requiredField}' at form field index {$index}.",
                                    'error'   => 'Form field validation failed'
                                ], 422);
                            }
                        }
                    }
                }
            }

            $complaint->update($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Complaint updated successfully.',
                'data'    => $complaint->fresh('category'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('CommonComplaint Update Error: ' . $e->getMessage(), [
                'complaint_id' => $id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update complaint.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy($id)
    {
        try {
            $complaint = CommonComplaint::findOrFail($id);
            $complaint->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Complaint deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('CommonComplaint Destroy Error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete complaint.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sample common complaints for testing (fallback method)
     */
    public function getSampleData(HttpRequest $request)
    {
        $categoryId = $request->get('category_id', 1);
        
        // Sample common complaints with form structures
        $sampleComplaints = [
            [
                'id' => 1,
                'title' => 'Bus Service Delay',
                'description' => 'Report delays and scheduling issues with public bus services',
                'complaint_category_id' => $categoryId,
                'priority' => 'medium',
                'status' => 'active',
                'form_data' => json_encode([
                    [
                        'field_type' => 'text',
                        'field_name' => 'bus_route',
                        'label' => 'Bus Route Number',
                        'placeholder' => 'e.g., Route 138',
                        'required' => true,
                        'help_text' => 'Enter the specific bus route number'
                    ],
                    [
                        'field_type' => 'select',
                        'field_name' => 'delay_duration',
                        'label' => 'Delay Duration',
                        'required' => true,
                        'options' => ['5-15 minutes', '15-30 minutes', '30-60 minutes', 'Over 1 hour']
                    ],
                    [
                        'field_type' => 'textarea',
                        'field_name' => 'incident_details',
                        'label' => 'Incident Details',
                        'placeholder' => 'Describe the delay incident...',
                        'required' => true,
                        'rows' => 4
                    ],
                    [
                        'field_type' => 'date',
                        'field_name' => 'incident_date',
                        'label' => 'Date of Incident',
                        'required' => true
                    ]
                ]),
                'created_at' => now()->toISOString()
            ],
            [
                'id' => 2,
                'title' => 'Road Damage Report',
                'description' => 'Report potholes, damaged roads, and infrastructure issues',
                'complaint_category_id' => $categoryId,
                'priority' => 'high',
                'status' => 'active',
                'form_data' => json_encode([
                    [
                        'field_type' => 'text',
                        'field_name' => 'location_address',
                        'label' => 'Exact Location',
                        'placeholder' => 'Street name and area',
                        'required' => true
                    ],
                    [
                        'field_type' => 'radio',
                        'field_name' => 'damage_type',
                        'label' => 'Type of Damage',
                        'required' => true,
                        'options' => ['Potholes', 'Cracks', 'Missing manhole cover', 'Flooding', 'Other']
                    ],
                    [
                        'field_type' => 'select',
                        'field_name' => 'severity',
                        'label' => 'Severity Level',
                        'required' => true,
                        'options' => ['Minor', 'Moderate', 'Severe', 'Dangerous']
                    ],
                    [
                        'field_type' => 'textarea',
                        'field_name' => 'description',
                        'label' => 'Damage Description',
                        'placeholder' => 'Provide detailed description of the damage...',
                        'required' => true,
                        'rows' => 3
                    ]
                ]),
                'created_at' => now()->toISOString()
            ],
            [
                'id' => 3,
                'title' => 'Water Supply Issue',
                'description' => 'Report water quality, shortage, or supply interruption issues',
                'complaint_category_id' => $categoryId,
                'priority' => 'high',
                'status' => 'active',
                'form_data' => json_encode([
                    [
                        'field_type' => 'text',
                        'field_name' => 'area_affected',
                        'label' => 'Area Affected',
                        'placeholder' => 'Neighborhood or district name',
                        'required' => true
                    ],
                    [
                        'field_type' => 'checkbox',
                        'field_name' => 'issue_types',
                        'label' => 'Issue Types (Select all that apply)',
                        'required' => true,
                        'options' => ['No water supply', 'Low water pressure', 'Dirty/contaminated water', 'Intermittent supply', 'Pipe leakage']
                    ],
                    [
                        'field_type' => 'number',
                        'field_name' => 'hours_without_water',
                        'label' => 'Hours Without Water',
                        'placeholder' => 'Number of hours',
                        'min' => 0,
                        'max' => 168,
                        'required' => false
                    ],
                    [
                        'field_type' => 'phone',
                        'field_name' => 'contact_number',
                        'label' => 'Contact Number',
                        'placeholder' => 'Your phone number for follow-up',
                        'required' => true
                    ]
                ]),
                'created_at' => now()->toISOString()
            ]
        ];

        // Filter by category if specified
        if ($request->has('category_id')) {
            $sampleComplaints = array_map(function($complaint) use ($categoryId) {
                $complaint['complaint_category_id'] = $categoryId;
                return $complaint;
            }, $sampleComplaints);
        }

            return response()->json([
                'status' => 'success',
                'data' => $sampleComplaints,
                'total' => count($sampleComplaints),
                'message' => 'Sample complaints retrieved successfully'
            ]);
        }

    /**
     * Get common complaints by category for frontend use
     */
    private function getComplaintsByCategory(HttpRequest $request)
    {
        try {
            $categoryId = $request->get('category_id');
            $status = $request->get('status', 'active'); // Default to active
            
            // Build query
            $query = CommonComplaint::with('category')
                ->where('complaint_category_id', $categoryId);
            
            // Add status filter if the table has a status column
            // Uncomment this if you have a status column
            // $query->where('status', $status);
            
            $complaints = $query->orderBy('created_at', 'desc')->get();

            // Transform data for frontend
            $formattedComplaints = $complaints->map(function ($complaint) {
                return [
                    'id' => $complaint->id,
                    'title' => $complaint->title,
                    'description' => $complaint->description,
                    'priority' => $complaint->priority ?? 'medium',
                    'category' => $complaint->category ? [
                        'id' => $complaint->category->id,
                        'name' => $complaint->category->name,
                        'slug' => $complaint->category->slug
                    ] : null,
                    'form_data' => $complaint->form_data,
                    'created_at' => $complaint->created_at ? $complaint->created_at->toISOString() : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedComplaints,
                'total' => $formattedComplaints->count(),
                'message' => 'Common complaints retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching complaints by category: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Failed to fetch common complaints',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
