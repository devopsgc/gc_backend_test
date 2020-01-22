<?php

namespace Tests\Feature;

use App\Models\Record;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShortlistTest extends TestCase
{
    use RefreshDatabase;

    public function createRecordAndAddInSession($property, $num)
    {
        factory(Record::class, $num)->create($property);

        $records = Record::all();

        // add records into shortlist session
        $this->withSession([
            'selected' => $records->pluck('id')
        ]);

        return $records;
    }

    public function test_can_see_shortlist_with_records()
    {
        $records = $this->createRecordAndAddInSession([], 2);

        $this->actingAs($this->getSuperAdminUser())
            ->get('/campaigns/shortlist')
            ->assertStatus(200)
            ->assertSee($records->first()->name);
    }

    public function test_shortlist_cannot_create_campaign_with_missing_interests_profession_gender()
    {
        $user = $this->getSuperAdminUser();

        $this->createRecordAndAddInSession(['gender' => null, 'description' => null, 'description_ppt' => null], 2);

        $this->actingAs($user)
            ->get('/campaigns/shortlist')
            ->assertStatus(200)
            ->assertSee('Missing profile description')
            ->assertSee('Missing profile interests')
            ->assertSee('Missing profile professions')
            ->assertSee('Missing profile gender');
    }
}
