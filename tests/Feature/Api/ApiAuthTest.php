<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    public function test_health_endpoint_is_public(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk();
    }

    public function test_api_routes_require_authentication(): void
    {
        $response = $this->getJson('/api/competitions');

        $response->assertUnauthorized();
    }

    public function test_token_can_be_issued_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/token', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'test-device',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['token']);
    }

    public function test_token_cannot_be_issued_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/token', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrong-password',
            'device_name' => 'test-device',
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $tokenResponse = $this->postJson('/api/token', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'test-device',
        ]);

        $token = $tokenResponse->json('token');

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user');

        $response->assertOk();
        $response->assertJsonPath('email', $user->email);
    }

    public function test_token_validation_fails_without_email(): void
    {
        $response = $this->postJson('/api/token', [
            'password' => 'password',
            'device_name' => 'test',
        ]);

        $response->assertUnprocessable();
    }

    public function test_token_validation_fails_without_device_name(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/token', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertUnprocessable();
    }
}
