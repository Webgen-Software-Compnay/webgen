<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\ComplaintCategory;
use App\Models\CommonComplaint;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Public - Categories & Templates",
 *     description="Public endpoints for complaint categories and templates"
 * )
 */
class PublicController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/public/complaint-categories",
     *     summary="Get all active complaint categories",
     *     tags={"Public - Categories & Templates"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by category type",
     *         @OA\Schema(type="string", enum={"civil_issue", "government_department", "organization", "private_company"})
     *     ),
     *     @OA\Parameter(
     *         name="parent_id",
     *         in="query",
     *         description="Filter by parent category",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully"
     *     )
     * )
     */
    public function getComplaintCategories(Request $request): JsonResponse
    {
        $query = ComplaintCategory::where('status', 'active')
            ->select('id', 'name', 'slug', 'parent_id', 'category_type', 'description')
            ->orderBy('name');

        // Apply filters
        if ($request->has('type')) {
            $query->where('category_type', $request->type);
        }

        if ($request->has('parent_id')) {
            if ($request->parent_id === 'null' || $request->parent_id === '0') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        $categories = $query->get();

        // Group by main categories and sub-categories if no specific parent filter
        if (!$request->has('parent_id')) {
            $mainCategories = $categories->whereNull('parent_id')->values();
            $subCategories = $categories->whereNotNull('parent_id')->groupBy('parent_id');

            // Attach sub-categories to their parents
            $mainCategories->transform(function ($category) use ($subCategories) {
                $category->sub_categories = $subCategories->get($category->id, collect())->values();
                return $category;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'main_categories' => $mainCategories,
                    'total_main' => $mainCategories->count(),
                    'total_sub' => $subCategories->flatten()->count()
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $categories,
            'total' => $categories->count()
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/public/complaint-categories/{id}",
     *     summary="Get specific complaint category details",
     *     tags={"Public - Categories & Templates"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category details retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function getComplaintCategory($id): JsonResponse
    {
        $category = ComplaintCategory::with(['parent', 'children' => function($query) {
            $query->where('status', 'active');
        }])
        ->where('id', $id)
        ->where('status', 'active')
        ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found or inactive'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/public/common-complaints",
     *     summary="Get common complaint templates",
     *     tags={"Public - Categories & Templates"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Common complaints retrieved successfully"
     *     )
     * )
     */
    public function getCommonComplaints(Request $request): JsonResponse
    {
        $query = DB::table('common_complaints')
            ->join('complaint_categories', 'common_complaints.complaint_category_id', '=', 'complaint_categories.id')
            ->select([
                'common_complaints.*',
                'complaint_categories.name as category_name',
                'complaint_categories.category_type'
            ])
            ->where('complaint_categories.status', 'active');

        if ($request->has('category_id')) {
            $query->where('common_complaints.complaint_category_id', $request->category_id);
        }

        $commonComplaints = $query->orderBy('common_complaints.title')->get();

        // Parse form_data JSON
        $commonComplaints->transform(function ($complaint) {
            if ($complaint->form_data) {
                $complaint->form_data = json_decode($complaint->form_data, true);
            }
            return $complaint;
        });

        return response()->json([
            'success' => true,
            'data' => $commonComplaints,
            'total' => $commonComplaints->count()
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/public/common-complaints/{id}",
     *     summary="Get specific common complaint template",
     *     tags={"Public - Categories & Templates"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Common complaint retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Common complaint not found"
     *     )
     * )
     */
    public function getCommonComplaint($id): JsonResponse
    {
        $commonComplaint = DB::table('common_complaints')
            ->join('complaint_categories', 'common_complaints.complaint_category_id', '=', 'complaint_categories.id')
            ->select([
                'common_complaints.*',
                'complaint_categories.name as category_name',
                'complaint_categories.description as category_description',
                'complaint_categories.category_type'
            ])
            ->where('common_complaints.id', $id)
            ->where('complaint_categories.status', 'active')
            ->first();

        if (!$commonComplaint) {
            return response()->json([
                'success' => false,
                'message' => 'Common complaint not found or category inactive'
            ], 404);
        }

        // Parse form_data JSON
        if ($commonComplaint->form_data) {
            $commonComplaint->form_data = json_decode($commonComplaint->form_data, true);
        }

        return response()->json([
            'success' => true,
            'data' => $commonComplaint
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/public/entities",
     *     summary="Get all entities (government departments, organizations, companies)",
     *     tags={"Public - Categories & Templates"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by entity type",
     *         @OA\Schema(type="string", enum={"government_dept", "organization", "private_company", "civil_cat"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entities retrieved successfully"
     *     )
     * )
     */
    public function getEntities(Request $request): JsonResponse
    {
        $query = DB::table('entities')
            ->select('id', 'name', 'entity_type', 'description')
            ->orderBy('name');

        if ($request->has('type')) {
            $query->where('entity_type', $request->type);
        }

        $entities = $query->get();

        // Group by entity type
        $groupedEntities = $entities->groupBy('entity_type');

        return response()->json([
            'success' => true,
            'data' => [
                'all' => $entities,
                'by_type' => $groupedEntities,
                'total' => $entities->count()
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/public/entities/{id}",
     *     summary="Get specific entity details",
     *     tags={"Public - Categories & Templates"},
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
    public function getEntity($id): JsonResponse
    {
        $entity = DB::table('entities')->where('id', $id)->first();

        if (!$entity) {
            return response()->json([
                'success' => false,
                'message' => 'Entity not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $entity
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/public/statistics",
     *     summary="Get public statistics",
     *     tags={"Public - Categories & Templates"},
     *     @OA\Response(
     *         response=200,
     *         description="Public statistics retrieved successfully"
     *     )
     * )
     */
    public function getPublicStatistics(): JsonResponse
    {
        $stats = [
            'total_complaints' => DB::table('complaints')->count(),
            'resolved_complaints' => DB::table('complaints')->where('status', 'resolved')->count(),
            'total_categories' => DB::table('complaint_categories')->where('status', 'active')->count(),
            'total_entities' => DB::table('entities')->count(),
            'most_common_categories' => DB::table('complaints')
                ->join('complaint_categories', 'complaints.complaint_category_id', '=', 'complaint_categories.id')
                ->select('complaint_categories.name', DB::raw('count(*) as count'))
                ->where('complaint_categories.status', 'active')
                ->groupBy('complaint_categories.id', 'complaint_categories.name')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get()
        ];

        // Calculate resolution rate
        if ($stats['total_complaints'] > 0) {
            $stats['resolution_rate'] = round(
                ($stats['resolved_complaints'] / $stats['total_complaints']) * 100, 
                1
            );
        } else {
            $stats['resolution_rate'] = 0;
        }

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
