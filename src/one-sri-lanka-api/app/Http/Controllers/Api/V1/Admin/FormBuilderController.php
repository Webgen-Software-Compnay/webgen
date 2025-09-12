<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplaintFormStructure;
use App\Models\ComplaintCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Admin - Form Builder",
 *     description="Dynamic form builder for complaint categories"
 * )
 */
class FormBuilderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/form-builder",
     *     summary="Get all form structures",
     *     tags={"Admin - Form Builder"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         @OA\Schema(type="string", enum={"active", "inactive", "draft"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form structures retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('complaint_form_structures')
            ->join('complaint_categories', 'complaint_form_structures.complaint_category_id', '=', 'complaint_categories.id')
            ->select([
                'complaint_form_structures.*',
                'complaint_categories.name as category_name',
                'complaint_categories.category_type'
            ])
            ->orderBy('complaint_form_structures.sort_order')
            ->orderBy('complaint_form_structures.name');

        // Apply filters
        if ($request->has('category_id')) {
            $query->where('complaint_form_structures.complaint_category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('complaint_form_structures.status', $request->status);
        }

        $forms = $query->paginate($request->get('per_page', 15));

        // Parse JSON fields
        $forms->getCollection()->transform(function ($form) {
            $form->form_structure = json_decode($form->form_structure, true);
            $form->validation_rules = json_decode($form->validation_rules, true);
            return $form;
        });

        return response()->json([
            'success' => true,
            'data' => $forms->items(),
            'pagination' => [
                'current_page' => $forms->currentPage(),
                'last_page' => $forms->lastPage(),
                'per_page' => $forms->perPage(),
                'total' => $forms->total(),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/form-builder",
     *     summary="Create a new form structure",
     *     tags={"Admin - Form Builder"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "complaint_category_id", "form_structure"},
     *             @OA\Property(property="name", type="string", example="Bus Service Complaint Form"),
     *             @OA\Property(property="complaint_category_id", type="integer", example=2),
     *             @OA\Property(
     *                 property="form_structure",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="field_type", type="string", example="text"),
     *                     @OA\Property(property="field_name", type="string", example="route_number"),
     *                     @OA\Property(property="label", type="string", example="Bus Route Number"),
     *                     @OA\Property(property="placeholder", type="string", example="e.g., 120"),
     *                     @OA\Property(property="required", type="boolean", example=true),
     *                     @OA\Property(property="options", type="array", @OA\Items(type="string"))
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Form structure created successfully"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'complaint_category_id' => 'required|exists:complaint_categories,id',
            'form_structure' => 'required|array',
            'form_structure.*.field_type' => 'required|in:text,textarea,select,radio,checkbox,number,email,date,time,file',
            'form_structure.*.field_name' => 'required|string|max:100',
            'form_structure.*.label' => 'required|string|max:255',
            'form_structure.*.required' => 'boolean',
            'form_structure.*.placeholder' => 'nullable|string|max:255',
            'form_structure.*.options' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'status' => 'nullable|in:active,inactive,draft',
            'sort_order' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate slug from name
        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;

        // Ensure unique slug
        while (DB::table('complaint_form_structures')->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Validate form structure
        $formStructure = $this->validateFormStructure($request->form_structure);

        $formData = [
            'name' => $request->name,
            'slug' => $slug,
            'complaint_category_id' => $request->complaint_category_id,
            'form_structure' => json_encode($formStructure),
            'validation_rules' => $request->validation_rules ? json_encode($request->validation_rules) : null,
            'status' => $request->status ?? 'active',
            'sort_order' => $request->sort_order ?? 0,
            'created_by' => Auth::user()->name ?? 'System',
            'updated_by' => Auth::user()->name ?? 'System',
            'created_at' => now(),
            'updated_at' => now()
        ];

        $formId = DB::table('complaint_form_structures')->insertGetId($formData);

        $createdForm = DB::table('complaint_form_structures')->where('id', $formId)->first();
        $createdForm->form_structure = json_decode($createdForm->form_structure, true);
        $createdForm->validation_rules = json_decode($createdForm->validation_rules, true);

        return response()->json([
            'success' => true,
            'message' => 'Form structure created successfully',
            'data' => $createdForm
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/form-builder/{id}",
     *     summary="Get specific form structure",
     *     tags={"Admin - Form Builder"},
     *     @OA\Parameter(
     *         name="id",
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
    public function show($id): JsonResponse
    {
        $form = DB::table('complaint_form_structures')
            ->join('complaint_categories', 'complaint_form_structures.complaint_category_id', '=', 'complaint_categories.id')
            ->select([
                'complaint_form_structures.*',
                'complaint_categories.name as category_name',
                'complaint_categories.category_type'
            ])
            ->where('complaint_form_structures.id', $id)
            ->first();

        if (!$form) {
            return response()->json([
                'success' => false,
                'message' => 'Form structure not found'
            ], 404);
        }

        $form->form_structure = json_decode($form->form_structure, true);
        $form->validation_rules = json_decode($form->validation_rules, true);

        return response()->json([
            'success' => true,
            'data' => $form
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/form-builder/{id}",
     *     summary="Update form structure",
     *     tags={"Admin - Form Builder"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form structure updated successfully"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $form = DB::table('complaint_form_structures')->where('id', $id)->first();

        if (!$form) {
            return response()->json([
                'success' => false,
                'message' => 'Form structure not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'complaint_category_id' => 'sometimes|required|exists:complaint_categories,id',
            'form_structure' => 'sometimes|required|array',
            'form_structure.*.field_type' => 'required_with:form_structure|in:text,textarea,select,radio,checkbox,number,email,date,time,file',
            'form_structure.*.field_name' => 'required_with:form_structure|string|max:100',
            'form_structure.*.label' => 'required_with:form_structure|string|max:255',
            'validation_rules' => 'nullable|array',
            'status' => 'sometimes|in:active,inactive,draft',
            'sort_order' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [];

        if ($request->has('name')) {
            $updateData['name'] = $request->name;
            // Update slug if name changes
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $counter = 1;
            
            while (DB::table('complaint_form_structures')->where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $updateData['slug'] = $slug;
        }

        if ($request->has('complaint_category_id')) {
            $updateData['complaint_category_id'] = $request->complaint_category_id;
        }

        if ($request->has('form_structure')) {
            $formStructure = $this->validateFormStructure($request->form_structure);
            $updateData['form_structure'] = json_encode($formStructure);
        }

        if ($request->has('validation_rules')) {
            $updateData['validation_rules'] = $request->validation_rules ? json_encode($request->validation_rules) : null;
        }

        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }

        if ($request->has('sort_order')) {
            $updateData['sort_order'] = $request->sort_order;
        }

        $updateData['updated_by'] = Auth::user()->name ?? 'System';
        $updateData['updated_at'] = now();

        DB::table('complaint_form_structures')->where('id', $id)->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Form structure updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/form-builder/{id}",
     *     summary="Delete form structure",
     *     tags={"Admin - Form Builder"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form structure deleted successfully"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $form = DB::table('complaint_form_structures')->where('id', $id)->first();

        if (!$form) {
            return response()->json([
                'success' => false,
                'message' => 'Form structure not found'
            ], 404);
        }

        DB::table('complaint_form_structures')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Form structure deleted successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/form-builder/{id}/duplicate",
     *     summary="Duplicate form structure",
     *     tags={"Admin - Form Builder"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Form structure duplicated successfully"
     *     )
     * )
     */
    public function duplicate($id): JsonResponse
    {
        $originalForm = DB::table('complaint_form_structures')->where('id', $id)->first();

        if (!$originalForm) {
            return response()->json([
                'success' => false,
                'message' => 'Form structure not found'
            ], 404);
        }

        // Create new slug
        $baseName = $originalForm->name . ' (Copy)';
        $slug = Str::slug($baseName);
        $originalSlug = $slug;
        $counter = 1;

        while (DB::table('complaint_form_structures')->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $duplicateData = [
            'name' => $baseName,
            'slug' => $slug,
            'complaint_category_id' => $originalForm->complaint_category_id,
            'form_structure' => $originalForm->form_structure,
            'validation_rules' => $originalForm->validation_rules,
            'status' => 'draft',
            'sort_order' => $originalForm->sort_order,
            'created_by' => Auth::user()->name ?? 'System',
            'updated_by' => Auth::user()->name ?? 'System',
            'created_at' => now(),
            'updated_at' => now()
        ];

        $newFormId = DB::table('complaint_form_structures')->insertGetId($duplicateData);

        return response()->json([
            'success' => true,
            'message' => 'Form structure duplicated successfully',
            'data' => ['id' => $newFormId]
        ], 201);
    }

    /**
     * Validate and sanitize form structure
     */
    private function validateFormStructure(array $formStructure): array
    {
        $validatedStructure = [];

        foreach ($formStructure as $field) {
            $validatedField = [
                'field_type' => $field['field_type'],
                'field_name' => Str::snake($field['field_name']), // Ensure snake_case
                'label' => $field['label'],
                'required' => $field['required'] ?? false,
                'placeholder' => $field['placeholder'] ?? '',
                'help_text' => $field['help_text'] ?? '',
                'order' => $field['order'] ?? count($validatedStructure) + 1
            ];

            // Add field-specific properties
            if (in_array($field['field_type'], ['select', 'radio', 'checkbox'])) {
                $validatedField['options'] = $field['options'] ?? [];
            }

            if ($field['field_type'] === 'number') {
                $validatedField['min'] = $field['min'] ?? null;
                $validatedField['max'] = $field['max'] ?? null;
                $validatedField['step'] = $field['step'] ?? 1;
            }

            if ($field['field_type'] === 'text') {
                $validatedField['max_length'] = $field['max_length'] ?? 255;
            }

            if ($field['field_type'] === 'textarea') {
                $validatedField['rows'] = $field['rows'] ?? 3;
                $validatedField['max_length'] = $field['max_length'] ?? 1000;
            }

            if ($field['field_type'] === 'file') {
                $validatedField['accept'] = $field['accept'] ?? 'image/*,.pdf';
                $validatedField['max_size'] = $field['max_size'] ?? 5; // MB
            }

            $validatedStructure[] = $validatedField;
        }

        return $validatedStructure;
    }
}
