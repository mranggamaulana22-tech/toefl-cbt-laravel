<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_check_endpoint_is_accessible(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    public function test_api_user_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_api_user_endpoint_returns_user_when_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson(['id' => $user->id]);
    }

    public function test_api_v1_practice_progress_routes_exist(): void
    {
        $user = User::factory()->create();

        // GET
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/student/practice/progress');
        $this->assertNotEquals(404, $response->status());

        // POST
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/student/practice/progress', []);
        $this->assertNotEquals(404, $response->status());

        // DELETE
        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/v1/student/practice/progress');
        $this->assertNotEquals(404, $response->status());
    }

    public function test_api_v1_suggestion_routes_exist(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        // Dashboard suggestions
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/student/suggestion/dashboard');
        $response->assertOk();
    }

    public function test_api_routes_are_versioned(): void
    {
        $user = User::factory()->create();

        // Should use /api/v1/ prefix
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/student/practice/progress');
        $this->assertNotEquals(404, $response->status(), 'API v1 routes should exist');

        // Should NOT be in old location (either 404 or not found)
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/student/practice/progress');
        $this->assertEquals(404, $response->status(), 'Old API route structure should not exist');
    }
}
