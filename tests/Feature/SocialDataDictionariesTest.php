<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialDataDictionariesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_see_television_interests_and_page_is_working()
    {
        $admin = $this->getSuperAdminUser();

        $this->actingAs($admin)
            ->get('/social-data/dictionaries')
            ->assertSeeText('Interests')
            ->assertSeeText('Languages')
            ->assertSeeText('Brands')
            ->assertSeeText('Countries');

    }
}
