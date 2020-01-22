<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MetricsTest extends TestCase
{
    public function test_metrics_page()
    {
        $user = $this->getSuperAdminUser();

        $this->actingAs($user)
            ->get('/metrics')
            ->assertStatus(200);
    }
}
