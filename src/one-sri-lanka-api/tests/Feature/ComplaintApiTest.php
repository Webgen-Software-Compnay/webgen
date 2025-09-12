<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\ComplaintCategory;
use Illuminate\Support\Facades\DB;

class ComplaintApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $admin;
    protected $category;
    protected $entity;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create([
            'email' => 'user@example.com'
        ]);
        
        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@gov.lk'
        ]);
        
        // Create test category
        $this->category = ComplaintCategory::create([
            'name' => 'Transport',
            'slug' => 'transport',
            'category_type' => 'government_department',
            'description' => 'Transportation related complaints',
            'status' => 'active'
        ]);
        
        // Create test entity
        $this->entity = DB::table('entities')->insertGetId([
            'name' => 'Sri Lanka Transport Board',
            'entity_type' => 'government_dept',
            'description' => 'Government transport department',
            'created_at' => now()
        ]);
    }

    public function test_public_endpoints_work_without_authentication()
    {
        // Test public categories endpoint
        $response = $this->getJson('/api/public/complaint-categories');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'main_categories',
                        'total_main',
                        'total_sub'
                    ]
                ]);

        // Test public entities endpoint
        $response = $this->getJson('/api/public/entities');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'all',
                        'by_type',
                        'total'
                    ]
                ]);

        // Test public statistics endpoint
        $response = $this->getJson('/api/public/statistics');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'total_complaints',
                        'resolved_complaints',
                        'total_categories',
                        'total_entities',
                        'resolution_rate'
                    ]
                ]);
    }

    public function test_user_can_submit_complaint()
    {
        $complaintData = [
            'title' => 'Bus Service Delay',
            'description' => 'Route 120 bus is frequently delayed',
            'complaint_category_id' => $this->category->id,
            'entity_id' => $this->entity,
            'priority' => 'medium',
            'form_data' => [
                'route_number' => '120',
                'delay_duration' => '30 minutes'
            ]
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                        ->postJson('/api/user/complaints', $complaintData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'title',
                        'status',
                        'category_name',
                        'entity_name'
                    ]
                ]);
    }

    public function test_user_can_view_their_complaints()
    {
        // Create a complaint for the user
        $complaintId = DB::table('complaints')->insertGetId([
            'user_id' => $this->user->id,
            'entity_id' => $this->entity,
            'complaint_category_id' => $this->category->id,
            'title' => 'Test Complaint',
            'description' => 'Test description',
            'status' => 'pending',
            'priority' => 'medium',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
                        ->getJson('/api/user/complaints');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'status',
                            'category_name',
                            'entity_name'
                        ]
                    ],
                    'pagination'
                ]);
    }

    public function test_user_can_view_specific_complaint()
    {
        $complaintId = DB::table('complaints')->insertGetId([
            'user_id' => $this->user->id,
            'entity_id' => $this->entity,
            'complaint_category_id' => $this->category->id,
            'title' => 'Test Complaint',
            'description' => 'Test description',
            'status' => 'pending',
            'priority' => 'medium',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
                        ->getJson("/api/user/complaints/{$complaintId}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'category_name',
                        'entity_name'
                    ]
                ]);
    }

    public function test_user_cannot_view_other_users_complaints()
    {
        $otherUser = User::factory()->create();
        
        $complaintId = DB::table('complaints')->insertGetId([
            'user_id' => $otherUser->id,
            'entity_id' => $this->entity,
            'complaint_category_id' => $this->category->id,
            'title' => 'Other User Complaint',
            'description' => 'Test description',
            'status' => 'pending',
            'priority' => 'medium',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
                        ->getJson("/api/user/complaints/{$complaintId}");

        $response->assertStatus(404);
    }

    public function test_admin_can_view_all_complaints()
    {
        // Create complaints from different users
        DB::table('complaints')->insert([
            [
                'user_id' => $this->user->id,
                'entity_id' => $this->entity,
                'complaint_category_id' => $this->category->id,
                'title' => 'User Complaint',
                'description' => 'Test description',
                'status' => 'pending',
                'priority' => 'medium',
                'submitted_at' => now(),
            ],
            [
                'user_id' => $this->admin->id,
                'entity_id' => $this->entity,
                'complaint_category_id' => $this->category->id,
                'title' => 'Admin Complaint',
                'description' => 'Test description',
                'status' => 'resolved',
                'priority' => 'high',
                'submitted_at' => now(),
            ]
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
                        ->getJson('/api/admin/complaints');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'status',
                            'user_name',
                            'category_name',
                            'entity_name'
                        ]
                    ],
                    'pagination'
                ]);
    }

    public function test_admin_can_update_complaint_status()
    {
        $complaintId = DB::table('complaints')->insertGetId([
            'user_id' => $this->user->id,
            'entity_id' => $this->entity,
            'complaint_category_id' => $this->category->id,
            'title' => 'Test Complaint',
            'description' => 'Test description',
            'status' => 'pending',
            'priority' => 'medium',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
                        ->putJson("/api/admin/complaints/{$complaintId}/status", [
                            'status' => 'resolved',
                            'resolution_notes' => 'Issue has been resolved'
                        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Complaint status updated successfully'
                ]);

        // Verify the status was updated in database
        $complaint = DB::table('complaints')->where('id', $complaintId)->first();
        $this->assertEquals('resolved', $complaint->status);
        $this->assertNotNull($complaint->resolved_at);
    }

    public function test_admin_can_view_statistics()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
                        ->getJson('/api/admin/complaints/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'total_complaints',
                        'by_status',
                        'by_priority',
                        'by_category',
                        'recent_activity',
                        'resolution_rate'
                    ]
                ]);
    }

    public function test_admin_can_manage_entities()
    {
        $entityData = [
            'name' => 'Ministry of Health',
            'entity_type' => 'government_dept',
            'description' => 'Government health ministry'
        ];

        // Create entity
        $response = $this->actingAs($this->admin, 'sanctum')
                        ->postJson('/api/admin/entities', $entityData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'entity_type',
                        'description'
                    ]
                ]);

        $entityId = $response->json('data.id');

        // Update entity
        $updateData = ['description' => 'Updated description'];
        $response = $this->actingAs($this->admin, 'sanctum')
                        ->putJson("/api/admin/entities/{$entityId}", $updateData);

        $response->assertStatus(200);

        // Get entity
        $response = $this->actingAs($this->admin, 'sanctum')
                        ->getJson("/api/admin/entities/{$entityId}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'entity',
                        'complaint_statistics'
                    ]
                ]);
    }

    public function test_non_admin_cannot_access_admin_endpoints()
    {
        $response = $this->actingAs($this->user, 'sanctum')
                        ->getJson('/api/admin/complaints');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_protected_endpoints()
    {
        $response = $this->getJson('/api/user/complaints');
        $response->assertStatus(401);

        $response = $this->getJson('/api/admin/complaints');
        $response->assertStatus(401);
    }

    public function test_complaint_validation_works()
    {
        // Test missing required fields
        $response = $this->actingAs($this->user, 'sanctum')
                        ->postJson('/api/user/complaints', []);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ]);

        // Test invalid category
        $response = $this->actingAs($this->user, 'sanctum')
                        ->postJson('/api/user/complaints', [
                            'title' => 'Test',
                            'description' => 'Test description',
                            'complaint_category_id' => 999,
                            'entity_id' => $this->entity,
                        ]);

        $response->assertStatus(422);
    }
}
