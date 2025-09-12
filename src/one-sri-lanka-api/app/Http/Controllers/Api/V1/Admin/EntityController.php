<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Admin - Entities",
 *     description="Admin entity management endpoints"
 * )
 */
class EntityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/entities",
     *     summary="Get all entities with filters",
     *     tags={"Admin - Entities"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by entity type",
     *         @OA\Schema(type="string", enum={"government_dept", "organization", "private_company", "civil_cat"})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by name or description",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entities retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('entities')
            ->select('*')
            ->orderBy('name');

        // Apply filters
        if ($request->has('type')) {
            $query->where('entity_type', $request->type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $entities = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $entities->items(),
            'pagination' => [
                'current_page' => $entities->currentPage(),
                'last_page' => $entities->lastPage(),
                'per_page' => $entities->perPage(),
                'total' => $entities->total(),
                'from' => $entities->firstItem(),
                'to' => $entities->lastItem(),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/entities",
     *     summary="Create a new entity",
     *     tags={"Admin - Entities"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "entity_type"},
     *             @OA\Property(property="name", type="string", example="Ministry of Health"),
     *             @OA\Property(property="entity_type", type="string", enum={"government_dept", "organization", "private_company", "civil_cat"}),
     *             @OA\Property(property="description", type="string", example="Government ministry responsible for health services")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Entity created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:entities,name',
            'entity_type' => 'required|in:government_dept,organization,private_company,civil_cat',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $entityData = [
            'name' => $request->name,
            'entity_type' => $request->entity_type,
            'description' => $request->description,
            'created_at' => now()
        ];

        $entityId = DB::table('entities')->insertGetId($entityData);

        $entity = DB::table('entities')->where('id', $entityId)->first();

        return response()->json([
            'success' => true,
            'message' => 'Entity created successfully',
            'data' => $entity
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/entities/{id}",
     *     summary="Get entity details",
     *     tags={"Admin - Entities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entity details retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entity not found"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $entity = DB::table('entities')->where('id', $id)->first();

        if (!$entity) {
            return response()->json([
                'success' => false,
                'message' => 'Entity not found'
            ], 404);
        }

        // Get complaint statistics for this entity
        $complaintStats = DB::table('complaints')
            ->where('entity_id', $id)
            ->select([
                DB::raw('COUNT(*) as total_complaints'),
                DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as pending'),
                DB::raw('COUNT(CASE WHEN status = "in_progress" THEN 1 END) as in_progress'),
                DB::raw('COUNT(CASE WHEN status = "resolved" THEN 1 END) as resolved'),
                DB::raw('COUNT(CASE WHEN status = "rejected" THEN 1 END) as rejected')
            ])
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'entity' => $entity,
                'complaint_statistics' => $complaintStats
            ]
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/entities/{id}",
     *     summary="Update entity",
     *     tags={"Admin - Entities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="entity_type", type="string", enum={"government_dept", "organization", "private_company", "civil_cat"}),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entity updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entity not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $entity = DB::table('entities')->where('id', $id)->first();

        if (!$entity) {
            return response()->json([
                'success' => false,
                'message' => 'Entity not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:entities,name,' . $id,
            'entity_type' => 'sometimes|required|in:government_dept,organization,private_company,civil_cat',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only(['name', 'entity_type', 'description']);

        DB::table('entities')->where('id', $id)->update($updateData);

        $updatedEntity = DB::table('entities')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Entity updated successfully',
            'data' => $updatedEntity
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/entities/{id}",
     *     summary="Delete entity",
     *     tags={"Admin - Entities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entity deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entity not found"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Cannot delete entity with existing complaints"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $entity = DB::table('entities')->where('id', $id)->first();

        if (!$entity) {
            return response()->json([
                'success' => false,
                'message' => 'Entity not found'
            ], 404);
        }

        // Check if entity has any complaints
        $complaintCount = DB::table('complaints')->where('entity_id', $id)->count();

        if ($complaintCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete entity with existing complaints',
                'data' => ['complaint_count' => $complaintCount]
            ], 409);
        }

        DB::table('entities')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Entity deleted successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/entities/statistics",
     *     summary="Get entity statistics",
     *     tags={"Admin - Entities"},
     *     @OA\Response(
     *         response=200,
     *         description="Entity statistics retrieved successfully"
     *     )
     * )
     */
    public function getStatistics(): JsonResponse
    {
        $stats = [
            'total_entities' => DB::table('entities')->count(),
            'by_type' => DB::table('entities')
                ->select('entity_type', DB::raw('count(*) as count'))
                ->groupBy('entity_type')
                ->pluck('count', 'entity_type'),
            'most_complained_entities' => DB::table('entities')
                ->leftJoin('complaints', 'entities.id', '=', 'complaints.entity_id')
                ->select([
                    'entities.id',
                    'entities.name',
                    'entities.entity_type',
                    DB::raw('COUNT(complaints.id) as complaint_count')
                ])
                ->groupBy('entities.id', 'entities.name', 'entities.entity_type')
                ->orderBy('complaint_count', 'desc')
                ->limit(10)
                ->get(),
            'entities_without_complaints' => DB::table('entities')
                ->leftJoin('complaints', 'entities.id', '=', 'complaints.entity_id')
                ->whereNull('complaints.entity_id')
                ->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
