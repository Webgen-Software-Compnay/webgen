<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Dynamic Forms",
 *     description="Dynamic form rendering and validation"
 * )
 */
class DynamicFormController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/forms/category/{categoryId}",
     *     summary="Get form structure for a specific complaint category",
     *     tags={"Dynamic Forms"},
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form structure retrieved successfully"
     *     )
     * )
     */
    public function getFormByCategory($categoryId): JsonResponse
    {
        // Get category info
        $category = DB::table('complaint_categories')
            ->where('id', $categoryId)
            ->where('status', 'active')
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found or inactive'
            ], 404);
        }

        // Get form structure for this category
        $formStructure = DB::table('complaint_form_structures')
            ->where('complaint_category_id', $categoryId)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->first();

        if (!$formStructure) {
            // Return default form structure if no custom form exists
            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category,
                    'form_structure' => $this->getDefaultFormStructure(),
                    'has_custom_form' => false
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'form_structure' => [
                    'id' => $formStructure->id,
                    'name' => $formStructure->name,
                    'slug' => $formStructure->slug,
                    'fields' => json_decode($formStructure->form_structure, true),
                    'validation_rules' => json_decode($formStructure->validation_rules, true)
                ],
                'has_custom_form' => true
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/forms/{slug}",
     *     summary="Get form structure by slug",
     *     tags={"Dynamic Forms"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form structure retrieved successfully"
     *     )
     * )
     */
    public function getFormBySlug($slug): JsonResponse
    {
        $formStructure = DB::table('complaint_form_structures')
            ->join('complaint_categories', 'complaint_form_structures.complaint_category_id', '=', 'complaint_categories.id')
            ->select([
                'complaint_form_structures.*',
                'complaint_categories.name as category_name',
                'complaint_categories.category_type',
                'complaint_categories.description as category_description'
            ])
            ->where('complaint_form_structures.slug', $slug)
            ->where('complaint_form_structures.status', 'active')
            ->where('complaint_categories.status', 'active')
            ->first();

        if (!$formStructure) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found or inactive'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $formStructure->id,
                'name' => $formStructure->name,
                'slug' => $formStructure->slug,
                'category' => [
                    'id' => $formStructure->complaint_category_id,
                    'name' => $formStructure->category_name,
                    'type' => $formStructure->category_type,
                    'description' => $formStructure->category_description
                ],
                'fields' => json_decode($formStructure->form_structure, true),
                'validation_rules' => json_decode($formStructure->validation_rules, true),
                'created_at' => $formStructure->created_at,
                'updated_at' => $formStructure->updated_at
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/forms/validate",
     *     summary="Validate form data against form structure",
     *     tags={"Dynamic Forms"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"form_id", "form_data"},
     *             @OA\Property(property="form_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="form_data",
     *                 type="object",
     *                 @OA\Property(property="route_number", type="string", example="120"),
     *                 @OA\Property(property="incident_time", type="string", example="2025-09-10 14:30")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form validation result"
     *     )
     * )
     */
    public function validateForm(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'form_id' => 'required|exists:complaint_form_structures,id',
            'form_data' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get form structure
        $formStructure = DB::table('complaint_form_structures')
            ->where('id', $request->form_id)
            ->where('status', 'active')
            ->first();

        if (!$formStructure) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found or inactive'
            ], 404);
        }

        $fields = json_decode($formStructure->form_structure, true);
        $customRules = json_decode($formStructure->validation_rules, true) ?? [];

        // Build validation rules from form structure
        $validationRules = $this->buildValidationRules($fields, $customRules);

        // Validate form data
        $formValidator = Validator::make($request->form_data, $validationRules);

        if ($formValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Form validation failed',
                'errors' => $formValidator->errors(),
                'field_errors' => $this->formatFieldErrors($formValidator->errors(), $fields)
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Form validation passed',
            'data' => [
                'validated_data' => $formValidator->validated(),
                'sanitized_data' => $this->sanitizeFormData($request->form_data, $fields)
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/forms/preview/{id}",
     *     summary="Get form preview data (for testing/development)",
     *     tags={"Dynamic Forms"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form preview data"
     *     )
     * )
     */
    public function previewForm($id): JsonResponse
    {
        $formStructure = DB::table('complaint_form_structures')
            ->join('complaint_categories', 'complaint_form_structures.complaint_category_id', '=', 'complaint_categories.id')
            ->select([
                'complaint_form_structures.*',
                'complaint_categories.name as category_name',
                'complaint_categories.category_type'
            ])
            ->where('complaint_form_structures.id', $id)
            ->first();

        if (!$formStructure) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found'
            ], 404);
        }

        $fields = json_decode($formStructure->form_structure, true);

        return response()->json([
            'success' => true,
            'data' => [
                'form_info' => [
                    'id' => $formStructure->id,
                    'name' => $formStructure->name,
                    'slug' => $formStructure->slug,
                    'status' => $formStructure->status,
                    'category_name' => $formStructure->category_name
                ],
                'fields' => $fields,
                'sample_data' => $this->generateSampleData($fields),
                'validation_rules' => $this->buildValidationRules($fields, []),
                'field_count' => count($fields)
            ]
        ]);
    }

    /**
     * Get default form structure for categories without custom forms
     */
    private function getDefaultFormStructure(): array
    {
        return [
            'id' => null,
            'name' => 'Default Complaint Form',
            'slug' => 'default-complaint-form',
            'fields' => [
                [
                    'field_type' => 'text',
                    'field_name' => 'subject',
                    'label' => 'Subject',
                    'placeholder' => 'Brief description of your complaint',
                    'required' => true,
                    'max_length' => 255,
                    'order' => 1
                ],
                [
                    'field_type' => 'textarea',
                    'field_name' => 'description',
                    'label' => 'Description',
                    'placeholder' => 'Please provide detailed information about your complaint...',
                    'required' => true,
                    'rows' => 5,
                    'max_length' => 2000,
                    'order' => 2
                ],
                [
                    'field_type' => 'date',
                    'field_name' => 'incident_date',
                    'label' => 'Date of Incident',
                    'required' => false,
                    'order' => 3
                ],
                [
                    'field_type' => 'text',
                    'field_name' => 'location',
                    'label' => 'Location',
                    'placeholder' => 'Where did this incident occur?',
                    'required' => false,
                    'max_length' => 255,
                    'order' => 4
                ]
            ],
            'validation_rules' => null
        ];
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
                    if (isset($field['accept'])) {
                        // Parse MIME types from accept attribute
                        $mimes = $this->parseMimeTypes($field['accept']);
                        if (!empty($mimes)) {
                            $fieldRules[] = 'mimes:' . implode(',', $mimes);
                        }
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
     * Generate sample data for form preview
     */
    private function generateSampleData(array $fields): array
    {
        $sampleData = [];

        foreach ($fields as $field) {
            $fieldName = $field['field_name'];

            switch ($field['field_type']) {
                case 'text':
                    $sampleData[$fieldName] = 'Sample ' . $field['label'];
                    break;

                case 'textarea':
                    $sampleData[$fieldName] = 'This is a sample description for ' . $field['label'];
                    break;

                case 'email':
                    $sampleData[$fieldName] = 'user@example.com';
                    break;

                case 'number':
                    $sampleData[$fieldName] = 123;
                    break;

                case 'date':
                    $sampleData[$fieldName] = '2025-09-10';
                    break;

                case 'time':
                    $sampleData[$fieldName] = '14:30';
                    break;

                case 'select':
                case 'radio':
                    if (isset($field['options']) && !empty($field['options'])) {
                        $sampleData[$fieldName] = $field['options'][0]['value'] ?? '';
                    }
                    break;

                case 'checkbox':
                    if (isset($field['options']) && !empty($field['options'])) {
                        $sampleData[$fieldName] = [$field['options'][0]['value'] ?? ''];
                    }
                    break;

                default:
                    $sampleData[$fieldName] = 'Sample value';
                    break;
            }
        }

        return $sampleData;
    }

    /**
     * Parse MIME types from file accept attribute
     */
    private function parseMimeTypes(string $accept): array
    {
        $mimeMap = [
            'image/*' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'],
            '.pdf' => ['pdf'],
            '.doc' => ['doc'],
            '.docx' => ['docx'],
            '.xlsx' => ['xlsx'],
            '.xls' => ['xls']
        ];

        $mimes = [];
        $acceptTypes = explode(',', $accept);

        foreach ($acceptTypes as $type) {
            $type = trim($type);
            if (isset($mimeMap[$type])) {
                $mimes = array_merge($mimes, $mimeMap[$type]);
            }
        }

        return array_unique($mimes);
    }
}
