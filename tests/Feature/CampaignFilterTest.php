<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Country;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class CampaignFilterTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed('CountriesSeeder');
        $this->seed('RoleSeeder');
    }

    public function test_admin_can_see_all_proposals_listing()
    {
        $admin = $this->createUserAsRole('admin');

        $myCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'created_by_user_id' => $admin->id,
        ]);

        $notMyCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
        ]);

        $this->actingAs($admin)
            ->get('/campaigns?filter_tab=draft')
            ->assertSee($myCampaign->name)
            ->assertSee($notMyCampaign->name);
    }

    public function test_admin_can_see_filter_own_proposals()
    {
        $admin = $this->createUserAsRole('admin');

        $myCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'created_by_user_id' => $admin->id,
        ]);

        $notMyCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
        ]);

        $this->actingAs($admin)
            ->get('/campaigns?filter_tab=draft&filter=me')
            ->assertSee($myCampaign->name)
            ->assertDontSee($notMyCampaign->name);
    }

    public function test_admin_can_see_filter_by_date()
    {
        $admin = $this->createUserAsRole('admin');

        $myCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'created_by_user_id' => $admin->id,
        ]);

        $notMyCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
        ]);

        $this->actingAs($admin)
            ->get('/campaigns?filter_tab=draft&filter=me')
            ->assertSee($myCampaign->name)
            ->assertDontSee($notMyCampaign->name);
    }

    public function test_admin_can_see_filter_proposals_by_search_title()
    {
        $findThis = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'name' => 'this is a unique title'
        ]);

        $dontFindThis = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'name' => 'cannot find this'
        ]);

        $this->actingAs($this->createUserAsRole('admin'))
            ->get('/campaigns?filter_tab=draft&q=unique')
            ->assertSee($findThis->name)
            ->assertDontSee($dontFindThis->name);
    }

    public function test_admin_can_see_filter_accepted_campaigns_by_brand()
    {
        $findThis = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_ACCEPTED,
            'brand' => 'cool brand',
        ]);

        $dontFindThis = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_ACCEPTED,
            'brand' => 'wont find this brand',
        ]);

        $this->actingAs($this->createUserAsRole('admin'))
            ->get('/campaigns?filter_tab=campaigns&q=cool')
            ->assertSee($findThis->name)
            ->assertDontSee($dontFindThis->name);
    }

    public function test_managers_can_only_see_proposals_of_their_own_and_countries_they_assigned_to()
    {
        $manager = $this->createUserAsRole('manager');

        $singapore = Country::where('iso_3166_2', 'SG')->first();
        $manager->countries()->attach($singapore);

        $mySGCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'created_by_user_id' => $manager->id,
            'country_code' => 'SG',
        ]);

        $notMySGCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'country_code' => 'SG',
        ]);

        $myAuCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'created_by_user_id' => $manager->id,
            'country_code' => 'AU',
        ]);

        $notMyAuCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'country_code' => 'AU',
        ]);

        $this->actingAs($manager)
            ->get('/campaigns?filter_tab=draft')
            ->assertSee($mySGCampaign->name)
            ->assertSee($notMySGCampaign->name)
            ->assertSee($myAuCampaign->name)
            ->assertDontSee($notMyAuCampaign->name);
    }

    public function test_sales_and_ops_can_only_see_proposals_of_their_own()
    {
        $rolesToTest = ['sales', 'operations'];

        foreach ($rolesToTest as $role) {
            $salesOrOps = $this->createUserAsRole($role);

            $mySGCampaign = factory(Campaign::class)->create([
                'status' => Campaign::STATUS_DRAFT,
                'created_by_user_id' => $salesOrOps->id,
                'country_code' => 'SG',
            ]);

            $notMySGCampaign = factory(Campaign::class)->create([
                'status' => Campaign::STATUS_DRAFT,
                'country_code' => 'SG',
            ]);

            $myAuCampaign = factory(Campaign::class)->create([
                'status' => Campaign::STATUS_DRAFT,
                'created_by_user_id' => $salesOrOps->id,
                'country_code' => 'AU',
            ]);

            $notMyAuCampaign = factory(Campaign::class)->create([
                'status' => Campaign::STATUS_DRAFT,
                'country_code' => 'AU',
            ]);

            $this->actingAs($salesOrOps)
                ->get('/campaigns?filter_tab=draft')
                ->assertSee($mySGCampaign->name)
                ->assertDontSee($notMySGCampaign->name)
                ->assertSee($myAuCampaign->name)
                ->assertDontSee($notMyAuCampaign->name);
        }
    }

    public function test_in_proposals_listing_only_show_proposals()
    {
        $admin = $this->createUserAsRole('admin');

        $proposalCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_DRAFT]);
        $acceptedCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_ACCEPTED]);
        $rejectedCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_REJECTED]);
        $cancelledCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_CANCELLED]);
        $completedCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_COMPLETED]);

        $this->actingAs($admin)
            ->get('/campaigns?filter_tab=draft')
            ->assertSee($proposalCampaign->name)
            ->assertDontSee($acceptedCampaign->name)
            ->assertDontSee($rejectedCampaign->name)
            ->assertDontSee($cancelledCampaign->name)
            ->assertDontSee($completedCampaign->name);
    }

    public function test_in_campaigns_listing_can_see_accepted_rejected_cancelled_campaigns()
    {
        $admin = $this->createUserAsRole('admin');

        $proposalCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_DRAFT]);
        $acceptedCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_ACCEPTED]);
        $rejectedCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_REJECTED]);
        $cancelledCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_CANCELLED]);
        $completedCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_COMPLETED]);

        $this->actingAs($admin)
            ->get('/campaigns?filter_tab=campaigns')
            ->assertDontSee($proposalCampaign->name)
            ->assertSee($acceptedCampaign->name)
            ->assertSee($rejectedCampaign->name)
            ->assertSee($cancelledCampaign->name)
            ->assertDontSee($completedCampaign->name);
    }

    public function test_in_completed_listing_can_see_only_completed_campaigns()
    {
        $admin = $this->createUserAsRole('admin');

        $proposalCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_DRAFT]);
        $acceptedCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_ACCEPTED]);
        $rejectedCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_REJECTED]);
        $cancelledCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_CANCELLED]);
        $completedCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_COMPLETED]);

        $this->actingAs($admin)
            ->get('/campaigns?filter_tab=completed')
            ->assertDontSee($proposalCampaign->name)
            ->assertDontSee($acceptedCampaign->name)
            ->assertDontSee($rejectedCampaign->name)
            ->assertDontSee($cancelledCampaign->name)
            ->assertSee($completedCampaign->name);
    }

    public function test_in_campaigns_listing_can_see_filter_each_accepted_rejected_cancelled_campaigns()
    {
        $admin = $this->createUserAsRole('admin');

        $acceptedCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_ACCEPTED]);
        $rejectedCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_REJECTED]);
        $cancelledCampaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_CANCELLED]);

        $this->actingAs($admin)
            ->get('/campaigns?filter_tab=campaigns&status=' . Campaign::STATUS_ACCEPTED)
            ->assertSee($acceptedCampaign->name)
            ->assertDontSee($rejectedCampaign->name)
            ->assertDontSee($cancelledCampaign->name);

        $this->actingAs($admin)
            ->get('/campaigns?filter_tab=campaigns&status=' . Campaign::STATUS_REJECTED)
            ->assertDontSee($acceptedCampaign->name)
            ->assertSee($rejectedCampaign->name)
            ->assertDontSee($cancelledCampaign->name);

        $this->actingAs($admin)
            ->get('/campaigns?filter_tab=campaigns&status=' . Campaign::STATUS_CANCELLED)
            ->assertDontSee($acceptedCampaign->name)
            ->assertDontSee($rejectedCampaign->name)
            ->assertSee($cancelledCampaign->name);
    }

    public function test_can_filter_by_country_in_campaigns_listing()
    {
        $sales = $this->createUserAsRole('sales');

        $SGCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_ACCEPTED,
            'created_by_user_id' => $sales->id,
            'country_code' => 'SG',
        ]);

        $AuCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_ACCEPTED,
            'created_by_user_id' => $sales->id,
            'country_code' => 'AU',
        ]);

        $this->actingAs($sales)
            ->get('/campaigns?filter_tab=campaigns&country_code=SG')
            ->assertSee($SGCampaign->name)
            ->assertDontSee($AuCampaign->name);
    }

    public function test_can_create_campaigns_from_proposal()
    {
        $sales = $this->createUserAsRole('sales');

        $proposalCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'created_by_user_id' => $sales->id,
            'country_code' => 'SG',
        ]);

        $data = [
            'status' => Campaign::STATUS_ACCEPTED,
            'country_code' => 'SG', // vaildation error
            'currency_code' => 'SGD', // vaildation error
            'name' => 'new campaign', // vaildation error
            'brand' => 'new brand',
            'interests' => 'Beauty & Cosmetics|Toys, Children & Baby',
            'description' => 'new description',
            'budget' => 1000,
            'start_at' => '05 November 2019',
            'end_at' => '29 November 2019',
        ];

        $this->actingAs($sales)
            ->put($proposalCampaign->getPath(), $data)
            ->assertRedirect($proposalCampaign->getPath());

        $this->actingAs($sales)
            ->get('/campaigns?filter_tab=campaigns')
            ->assertSee($proposalCampaign->refresh()->name);

        $this->assertDatabaseHas('campaigns', [
            'status' => $data['status'],
            'country_code' => $data['country_code'],
            'currency_code' => $data['currency_code'],
            'name' => $data['name'],
            'brand' => $data['brand'],
            'categories' => $data['interests'],
            'description' => $data['description'],
            'budget' => $data['budget'],
            'start_at' => Carbon::parse($data['start_at']),
            'end_at' => Carbon::parse($data['end_at']),
        ]);
    }

    public function test_create_campaigns_from_proposal_validation_errors()
    {
        $sales = $this->createUserAsRole('sales');

        $proposalCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'created_by_user_id' => $sales->id,
            'country_code' => 'SG',
        ]);

        $this->actingAs($sales)
            ->put($proposalCampaign->getPath(), ['status' => Campaign::STATUS_ACCEPTED])
            ->assertSessionHasErrors(['country_code', 'currency_code', 'name']);
    }

    public function test_only_admin_managers_can_see_filter_by_in_proposals_listing_page()
    {
        $rolesThatCanSee = ['admin', 'super_admin', 'manager'];

        foreach ($rolesThatCanSee as $role) {
            $user = $this->createUserAsRole($role);

            $this->actingAs($user)
                ->get('/campaigns?filter_tab=draft')
                ->assertSee('Filter By');
        }

        $rolesThatCannotSee = ['sales', 'operations'];

        foreach ($rolesThatCannotSee as $role) {
            $user = $this->createUserAsRole($role);

            $this->actingAs($user)
                ->get('/campaigns?filter_tab=draft')
                ->assertDontSee('Filter By');
        }
    }

    public function test_all_tab_can_see_search_filter()
    {
        $sales = $this->createUserAsRole('sales');

        $tabs = ['draft', 'campaigns', 'completed'];

        foreach ($tabs as $tab) {
            $this->actingAs($sales)
                ->get('/campaigns?filter_tab='.$tab)
                ->assertSee('Search by campaign name or brand');
        }
    }

    public function test_only_campaign_and_completed_tab_can_see_country_and_category_and_date_filter()
    {
        $sales = $this->createUserAsRole('sales');

        $visbleOntabs = ['campaigns', 'completed'];

        foreach ($visbleOntabs as $tab) {
            $this->actingAs($sales)
                ->get('/campaigns?filter_tab='.$tab)
                ->assertSee('<label for="country_code" class="my-1">Country</label>')
                ->assertSee('<label for="category" class="my-1">Category</label>')
                ->assertSee('<label for="daterange" class="my-1">Date</label>');
        }

        $this->actingAs($sales)
                ->get('/campaigns?filter_tab=draft')
                ->assertDontSee('<label for="country_code" class="my-1">Country</label>')
                ->assertDontSee('<label for="category" class="my-1">Category</label>')
                ->assertDontSee('<label for="daterange" class="my-1">Date</label>');
    }

    public function test_only_campaign_tab_can_see_status_filter()
    {
        $sales = $this->createUserAsRole('sales');

        $notVisbleOntabs = ['draft', 'completed'];

        foreach ($notVisbleOntabs as $tab) {
            $this->actingAs($sales)
                ->get('/campaigns?filter_tab='.$tab)
                ->assertDontSee('Campaign Status');
        }

        $this->actingAs($sales)
                ->get('/campaigns?filter_tab=campaigns')
                ->assertSee('Campaign Status');
    }
}
