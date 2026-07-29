<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_returns_healthy_status(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'services' => ['database', 'cache'],
            'app' => ['env', 'debug'],
        ]);
        $response->assertJsonPath('services.database', 'healthy');
        $response->assertJsonPath('status', 'healthy');
    }
}
