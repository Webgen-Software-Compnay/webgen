<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplaintCategory;
use App\Models\CommonComplaint;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Admin - Complaints",
 *     description="Admin complaint management endpoints"
 * )
 */
class ComplaintController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/complaints",
     *     summary="Get all complaints with filters",
     *     tags={"Admin - Complaints"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         @OA\Schema(type="string", enum={"pending", "in_progress", "resolved", "rejected"})
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter by priority",
     *         @OA\Schema(type="string", enum={"low", "medium", "high"})
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Complaints retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('complaints')
            ->join('users', 'complaints.user_id', '=', 'users.id')
            ->join('entities', 'complaints.entity_id', '=', 'entities.id')
            ->join('complaint_categories', 'complaints.complaint_category_id', '=', 'complaint_categories.id')
            ->leftJoin('common_complaints', 'complaints.common_complaint_id', '=', 'common_complaints.id')
            ->select([
                'complaints.*',
                'users.name as user_name',
                'users.email as user_email',
                'entities.name as entity_name',
                'entities.entity_type',
                'complaint_categories.name as category_name',
                'common_complaints.title as common_complaint_title'
            ]);

        // Apply filters
        if ($request->has('status')) {
            $query->where('complaints.status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('complaints.priority', $request->priority);
        }

        if ($request->has('category_id')) {
            $query->where('complaints.complaint_category_id', $request->category_id);
        }

        if ($request->has('entity_id')) {
            $query->where('complaints.entity_id', $request->entity_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('complaints.title', 'LIKE', "%{$search}%")
                  ->orWhere('complaints.description', 'LIKE', "%{$search}%")
                  ->orWhere('users.name', 'LIKE', "%{$search}%");
            });
        }

        // Date range filters
        if ($request->has('date_from')) {
            $query->where('complaints.submitted_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('complaints.submitted_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'submitted_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy('complaints.' . $sortBy, $sortDirection);

        // Pagination
        $perPage = min($request->get('per_page', 15), 100); // Max 100 items per page
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
            ],
            'filters_applied' => $request->only(['status', 'priority', 'category_id', 'entity_id', 'search', 'date_from', 'date_to'])
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/complaints/{id}",
     *     summary="Get complaint details",
     *     tags={"Admin - Complaints"},
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
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $complaint = DB::table('complaints')
            ->join('users', 'complaints.user_id', '=', 'users.id')
            ->join('entities', 'complaints.entity_id', '=', 'entities.id')
            ->join('complaint_categories', 'complaints.complaint_category_id', '=', 'complaint_categories.id')
            ->leftJoin('common_complaints', 'complaints.common_complaint_id', '=', 'common_complaints.id')
            ->select([
                'complaints.*',
                'users.name as user_name',
                'users.email as user_email',
                'entities.name as entity_name',
                'entities.entity_type',
                'entities.description as entity_description',
                'complaint_categories.name as category_name',
                'complaint_categories.description as category_description',
                'common_complaints.title as common_complaint_title',
                'common_complaints.form_data as common_complaint_form_data'
            ])
            ->where('complaints.id', $id)
            ->first();

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found'
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
     *     path="/api/complaints/{id}/status",
     *     summary="Update complaint status",
     *     tags={"Admin - Complaints"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"pending", "in_progress", "resolved", "rejected"}),
     *             @OA\Property(property="resolution_notes", type="string", description="Optional notes about status change")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Complaint status updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Complaint not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_progress,resolved,rejected',
            'resolution_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $complaint = DB::table('complaints')->where('id', $id)->first();

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found'
            ], 404);
        }

        $updateData = [
            'status' => $request->status,
        ];

        // Set resolution timestamp if status is resolved
        if ($request->status === 'resolved') {
            $updateData['resolved_at'] = now();
        }

        DB::table('complaints')->where('id', $id)->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Complaint status updated successfully',
            'data' => [
                'id' => $id,
                'status' => $request->status,
                'resolution_notes' => $request->resolution_notes
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/complaints/statistics",
     *     summary="Get complaint statistics",
     *     tags={"Admin - Complaints"},
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved successfully"
     *     )
     * )
     */
    public function getStatistics(): JsonResponse
    {
        $stats = [
            'total_complaints' => DB::table('complaints')->count(),
            'by_status' => DB::table('complaints')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_priority' => DB::table('complaints')
                ->select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority'),
            'by_category' => DB::table('complaints')
                ->join('complaint_categories', 'complaints.complaint_category_id', '=', 'complaint_categories.id')
                ->select('complaint_categories.name', DB::raw('count(*) as count'))
                ->groupBy('complaint_categories.id', 'complaint_categories.name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'recent_activity' => [
                'last_7_days' => DB::table('complaints')
                    ->where('submitted_at', '>=', now()->subDays(7))
                    ->count(),
                'last_30_days' => DB::table('complaints')
                    ->where('submitted_at', '>=', now()->subDays(30))
                    ->count(),
            ],
            'resolution_rate' => [
                'resolved' => DB::table('complaints')->where('status', 'resolved')->count(),
                'total' => DB::table('complaints')->count(),
            ]
        ];

        // Calculate resolution percentage
        if ($stats['resolution_rate']['total'] > 0) {
            $stats['resolution_rate']['percentage'] = round(
                ($stats['resolution_rate']['resolved'] / $stats['resolution_rate']['total']) * 100, 
                2
            );
        } else {
            $stats['resolution_rate']['percentage'] = 0;
        }

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/complaints/{id}",
     *     summary="Delete a complaint (Admin only)",
     *     tags={"Admin - Complaints"},
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
     *         response=404,
     *         description="Complaint not found"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $complaint = DB::table('complaints')->where('id', $id)->first();

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found'
            ], 404);
        }

        DB::table('complaints')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complaint deleted successfully'
        ]);
    }
}
