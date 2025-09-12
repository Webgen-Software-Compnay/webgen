<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\ComplaintCategory;
use App\Models\CommonComplaint;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="User - Complaints",
 *     description="User complaint submission and management endpoints"
 * )
 */
class ComplaintController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/complaints",
     *     summary="Submit a new complaint",
     *     tags={"User - Complaints"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "complaint_category_id", "entity_id"},
     *             @OA\Property(property="title", type="string", example="Bus Service Delay Issue"),
     *             @OA\Property(property="description", type="string", example="Route 120 bus is frequently delayed by 30+ minutes"),
     *             @OA\Property(property="complaint_category_id", type="integer", example=2),
     *             @OA\Property(property="entity_id", type="integer", example=1),
     *             @OA\Property(property="common_complaint_id", type="integer", nullable=true, example=2),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}, example="medium"),
     *             @OA\Property(
     *                 property="form_data",
     *                 type="object",
     *                 example={"route_number": "120", "time_of_day": "Morning Peak", "delay_duration": "35 minutes"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Complaint submitted successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    /**
     * @OA\Post(
     *     path="/api/user/complaints/with-form",
     *     summary="Submit complaint using dynamic form",
     *     tags={"User - Complaints"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"complaint_category_id", "entity_id", "form_data"},
     *             @OA\Property(property="complaint_category_id", type="integer", example=2),
     *             @OA\Property(property="entity_id", type="integer", example=1),
     *             @OA\Property(property="form_structure_id", type="integer", nullable=true, example=1),
     *             @OA\Property(property="common_complaint_id", type="integer", nullable=true, example=2),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}, example="medium"),
     *             @OA\Property(
     *                 property="form_data",
     *                 type="object",
     *                 example={
     *                     "route_number": "120",
     *                     "incident_time": "08:30",
     *                     "description": "Bus was 45 minutes late during morning rush hour"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Complaint submitted successfully"
     *     )
     * )
     */
    public function storeWithForm(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'complaint_category_id' => 'required|exists:complaint_categories,id',
            'entity_id' => 'required|exists:entities,id',
            'form_structure_id' => 'nullable|exists:complaint_form_structures,id',
            'common_complaint_id' => 'nullable|exists:common_complaints,id',
            'priority' => 'nullable|in:low,medium,high',
            'form_data' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify category is active
        $category = ComplaintCategory::where('id', $request->complaint_category_id)
            ->where('status', 'active')
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Selected complaint category is not available'
            ], 422);
        }

        // Get form structure for validation
        $formStructure = null;
        if ($request->form_structure_id) {
            $formStructure = DB::table('complaint_form_structures')
                ->where('id', $request->form_structure_id)
                ->where('status', 'active')
                ->first();
        } else {
            // Try to find form structure by category
            $formStructure = DB::table('complaint_form_structures')
                ->where('complaint_category_id', $request->complaint_category_id)
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->first();
        }

        // Validate form data against form structure
        if ($formStructure) {
            $formFields = json_decode($formStructure->form_structure, true);
            $customRules = json_decode($formStructure->validation_rules, true) ?? [];
            
            $validationRules = $this->buildValidationRules($formFields, $customRules);
            
            $formValidator = Validator::make($request->form_data, $validationRules);
            
            if ($formValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Form validation failed',
                    'errors' => $formValidator->errors(),
                    'field_errors' => $this->formatFieldErrors($formValidator->errors(), $formFields)
                ], 422);
            }
            
            // Sanitize form data
            $sanitizedFormData = $this->sanitizeFormData($request->form_data, $formFields);
        } else {
            $sanitizedFormData = $request->form_data;
        }

        // Extract title and description from form data or generate defaults
        $title = $this->extractTitle($sanitizedFormData, $category);
        $description = $this->extractDescription($sanitizedFormData, $formStructure);

        // Create complaint
        $complaintData = [
            'user_id' => Auth::id(),
            'title' => $title,
            'description' => $description,
            'complaint_category_id' => $request->complaint_category_id,
            'entity_id' => $request->entity_id,
            'common_complaint_id' => $request->common_complaint_id,
            'form_structure_id' => $formStructure ? $formStructure->id : null,
            'priority' => $request->priority ?? 'medium',
            'status' => 'pending',
            'submitted_at' => now(),
            'form_data' => json_encode($sanitizedFormData),
            'created_at' => now(),
            'updated_at' => now()
        ];

        $complaintId = DB::table('complaints')->insertGetId($complaintData);

        // Fetch the created complaint with related data
        $complaint = DB::table('complaints')
            ->join('complaint_categories', 'complaints.complaint_category_id', '=', 'complaint_categories.id')
            ->join('entities', 'complaints.entity_id', '=', 'entities.id')
            ->leftJoin('common_complaints', 'complaints.common_complaint_id', '=', 'common_complaints.id')
            ->leftJoin('complaint_form_structures', 'complaints.form_structure_id', '=', 'complaint_form_structures.id')
            ->select([
                'complaints.*',
                'complaint_categories.name as category_name',
                'entities.name as entity_name',
                'common_complaints.title as common_complaint_title',
                'complaint_form_structures.name as form_name'
            ])
            ->where('complaints.id', $complaintId)
            ->first();

        if ($complaint) {
            $complaint->form_data = json_decode($complaint->form_data, true);
        }

        return response()->json([
            'success' => true,
            'message' => 'Complaint submitted successfully using dynamic form',
            'data' => $complaint
        ], 201);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'complaint_category_id' => 'required|exists:complaint_categories,id',
            'entity_id' => 'required|exists:entities,id',
            'common_complaint_id' => 'nullable|exists:common_complaints,id',
            'priority' => 'nullable|in:low,medium,high',
            'form_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify category is active
        $category = ComplaintCategory::where('id', $request->complaint_category_id)
            ->where('status', 'active')
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Selected complaint category is not available'
            ], 422);
        }

        // Create complaint
        $complaintData = [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'complaint_category_id' => $request->complaint_category_id,
            'entity_id' => $request->entity_id,
            'common_complaint_id' => $request->common_complaint_id,
            'priority' => $request->priority ?? 'medium',
            'status' => 'pending',
            'submitted_at' => now(),
            'form_data' => $request->form_data ? json_encode($request->form_data) : null,
        ];

        $complaintId = DB::table('complaints')->insertGetId($complaintData);

        // Fetch the created complaint with related data
        $complaint = DB::table('complaints')
            ->join('complaint_categories', 'complaints.complaint_category_id', '=', 'complaint_categories.id')
            ->join('entities', 'complaints.entity_id', '=', 'entities.id')
            ->leftJoin('common_complaints', 'complaints.common_complaint_id', '=', 'common_complaints.id')
            ->select([
                'complaints.*',
                'complaint_categories.name as category_name',
                'entities.name as entity_name',
                'common_complaints.title as common_complaint_title'
            ])
            ->where('complaints.id', $complaintId)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Complaint submitted successfully',
            'data' => $complaint
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/user/complaints",
     *     summary="Get user's complaints",
     *     tags={"User - Complaints"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         @OA\Schema(type="string", enum={"pending", "in_progress", "resolved", "rejected"})
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User complaints retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('complaints')
            ->join('complaint_categories', 'complaints.complaint_category_id', '=', 'complaint_categories.id')
            ->join('entities', 'complaints.entity_id', '=', 'entities.id')
            ->leftJoin('common_complaints', 'complaints.common_complaint_id', '=', 'common_complaints.id')
            ->select([
                'complaints.*',
                'complaint_categories.name as category_name',
                'entities.name as entity_name',
                'entities.entity_type',
                'common_complaints.title as common_complaint_title'
            ])
            ->where('complaints.user_id', Auth::id());

        // Apply filters
        if ($request->has('status')) {
            $query->where('complaints.status', $request->status);
        }

        // Sort by submission date (newest first)
        $query->orderBy('complaints.submitted_at', 'desc');

        // Pagination
        $perPage = min($request->get('per_page', 10), 50); // Max 50 items per page for users
        $complaints = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $complaints->items(),
            'pagination' => [
                'current_page' => $complaints->currentPage(),
                'last_page' => $complaints->lastPage(),
                'per_page' => $complaints->perPage(),
                'total' => $complaints->total(),
                'from' => $complaints->firstItem(),
                'to' => $complaints->lastItem(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/complaints/{id}",
     *     summary="Get user's specific complaint",
     *     tags={"User - Complaints"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Complaint details retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Complaint not found"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $complaint = DB::table('complaints')
            ->join('complaint_categories', 'complaints.complaint_category_id', '=', 'complaint_categories.id')
            ->join('entities', 'complaints.entity_id', '=', 'entities.id')
            ->leftJoin('common_complaints', 'complaints.common_complaint_id', '=', 'common_complaints.id')
            ->select([
                'complaints.*',
                'complaint_categories.name as category_name',
                'complaint_categories.description as category_description',
                'entities.name as entity_name',
                'entities.entity_type',
                'entities.description as entity_description',
                'common_complaints.title as common_complaint_title',
                'common_complaints.form_data as common_complaint_form_data'
            ])
            ->where('complaints.id', $id)
            ->where('complaints.user_id', Auth::id()) // Ensure user can only see their own complaints
            ->first();

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found or access denied'
            ], 404);
        }

        // Parse JSON fields
        if ($complaint->form_data) {
            $complaint->form_data = json_decode($complaint->form_data, true);
        }

        if ($complaint->common_complaint_form_data) {
            $complaint->common_complaint_form_data = json_decode($complaint->common_complaint_form_data, true);
        }

        return response()->json([
            'success' => true,
            'data' => $complaint
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/user/complaints/{id}",
     *     summary="Update user's complaint (only if pending)",
     *     tags={"User - Complaints"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}),
     *             @OA\Property(property="form_data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Complaint updated successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Cannot update complaint in current status"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Complaint not found"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $complaint = DB::table('complaints')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found or access denied'
            ], 404);
        }

        // Only allow updates if complaint is still pending
        if ($complaint->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update complaint once it has been processed'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:5000',
            'priority' => 'sometimes|in:low,medium,high',
            'form_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only(['title', 'description', 'priority']);
        
        if ($request->has('form_data')) {
            $updateData['form_data'] = json_encode($request->form_data);
        }

        DB::table('complaints')->where('id', $id)->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Complaint updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/user/complaints/{id}",
     *     summary="Delete user's complaint (only if pending)",
     *     tags={"User - Complaints"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Complaint deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Cannot delete complaint in current status"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Complaint not found"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $complaint = DB::table('complaints')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found or access denied'
            ], 404);
        }

        // Only allow deletion if complaint is still pending
        if ($complaint->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete complaint once it has been processed'
            ], 403);
        }

        DB::table('complaints')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complaint deleted successfully'
        ]);
    }

    /**
     * Build Laravel validation rules from form structure
     */
    private function buildValidationRules(array $fields, array $customRules = []): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $fieldName = $field['field_name'];
            $fieldRules = [];

            // Required validation
            if ($field['required'] ?? false) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Type-specific validation
            switch ($field['field_type']) {
                case 'text':
                    $fieldRules[] = 'string';
                    if (isset($field['max_length'])) {
                        $fieldRules[] = 'max:' . $field['max_length'];
                    }
                    break;

                case 'textarea':
                    $fieldRules[] = 'string';
                    if (isset($field['max_length'])) {
                        $fieldRules[] = 'max:' . $field['max_length'];
                    }
                    break;

                case 'email':
                    $fieldRules[] = 'email';
                    break;

                case 'number':
                    $fieldRules[] = 'numeric';
                    if (isset($field['min'])) {
                        $fieldRules[] = 'min:' . $field['min'];
                    }
                    if (isset($field['max'])) {
                        $fieldRules[] = 'max:' . $field['max'];
                    }
                    break;

                case 'date':
                    $fieldRules[] = 'date';
                    break;

                case 'time':
                    $fieldRules[] = 'date_format:H:i';
                    break;

                case 'select':
                case 'radio':
                    if (isset($field['options']) && !empty($field['options'])) {
                        $fieldRules[] = 'in:' . implode(',', array_column($field['options'], 'value'));
                    }
                    break;

                case 'checkbox':
                    $fieldRules[] = 'array';
                    if (isset($field['options']) && !empty($field['options'])) {
                        $fieldRules[] = 'in:' . implode(',', array_column($field['options'], 'value'));
                    }
                    break;

                case 'file':
                    $fieldRules[] = 'file';
                    if (isset($field['max_size'])) {
                        $fieldRules[] = 'max:' . ($field['max_size'] * 1024); // Convert MB to KB
                    }
                    break;
            }

            // Apply custom rules if any
            if (isset($customRules[$fieldName])) {
                $fieldRules = array_merge($fieldRules, $customRules[$fieldName]);
            }

            $rules[$fieldName] = $fieldRules;
        }

        return $rules;
    }

    /**
     * Format field errors for better frontend display
     */
    private function formatFieldErrors($errors, array $fields): array
    {
        $formattedErrors = [];
        $fieldLabels = [];

        // Create field label mapping
        foreach ($fields as $field) {
            $fieldLabels[$field['field_name']] = $field['label'];
        }

        foreach ($errors->messages() as $fieldName => $messages) {
            $formattedErrors[$fieldName] = [
                'field_label' => $fieldLabels[$fieldName] ?? $fieldName,
                'messages' => $messages,
                'first_message' => $messages[0] ?? ''
            ];
        }

        return $formattedErrors;
    }

    /**
     * Sanitize form data based on field types
     */
    private function sanitizeFormData(array $formData, array $fields): array
    {
        $sanitized = [];
        $fieldTypes = [];

        // Create field type mapping
        foreach ($fields as $field) {
            $fieldTypes[$field['field_name']] = $field['field_type'];
        }

        foreach ($formData as $fieldName => $value) {
            $fieldType = $fieldTypes[$fieldName] ?? 'text';

            switch ($fieldType) {
                case 'text':
                case 'textarea':
                    $sanitized[$fieldName] = is_string($value) ? trim(strip_tags($value)) : $value;
                    break;

                case 'email':
                    $sanitized[$fieldName] = is_string($value) ? strtolower(trim($value)) : $value;
                    break;

                case 'number':
                    $sanitized[$fieldName] = is_numeric($value) ? (float) $value : $value;
                    break;

                case 'checkbox':
                    $sanitized[$fieldName] = is_array($value) ? $value : [$value];
                    break;

                default:
                    $sanitized[$fieldName] = $value;
                    break;
            }
        }

        return $sanitized;
    }

    /**
     * Extract title from form data
     */
    private function extractTitle(array $formData, $category): string
    {
        // Look for common title fields
        $titleFields = ['subject', 'title', 'summary', 'complaint_type'];
        
        foreach ($titleFields as $field) {
            if (isset($formData[$field]) && !empty($formData[$field])) {
                return is_string($formData[$field]) ? $formData[$field] : json_encode($formData[$field]);
            }
        }

        // Generate title from category and key data
        $keyData = [];
        foreach (['route_number', 'location', 'hospital_clinic_name', 'institution_name'] as $field) {
            if (isset($formData[$field]) && !empty($formData[$field])) {
                $keyData[] = $formData[$field];
            }
        }

        if (!empty($keyData)) {
            return $category->name . ' - ' . implode(', ', $keyData);
        }

        return $category->name . ' Complaint';
    }

    /**
     * Extract description from form data
     */
    private function extractDescription(array $formData, $formStructure): string
    {
        // Look for description field
        if (isset($formData['description']) && !empty($formData['description'])) {
            return $formData['description'];
        }

        // Build description from all form fields
        $description = [];
        $fields = $formStructure ? json_decode($formStructure->form_structure, true) : [];
        
        foreach ($fields as $field) {
            $fieldName = $field['field_name'];
            $fieldLabel = $field['label'];
            
            if (isset($formData[$fieldName]) && !empty($formData[$fieldName])) {
                $value = $formData[$fieldName];
                
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                
                $description[] = $fieldLabel . ': ' . $value;
            }
        }

        return !empty($description) ? implode("\n", $description) : 'No description provided';
    }
}
