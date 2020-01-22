<?php

use Illuminate\Database\Seeder;
use App\Models\Campaign;
use App\Models\Deliverable;
use App\Models\User;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $campaign = Campaign::create([
            'name' => '#BareSkinProject',
            'created_by_user_id' => User::first()->id,
            'brand' => 'SK-II',
            'budget' => 30000,
            'categories' => 'Beauty',
            'description' => '6 global celebrities take on the bold challenge to reveal their bare skin for the first time with one ally: Facial Treatment Essence.',
            'start_at' => '2019-06-01 00:00:00',
            'end_at' => '2019-06-30 00:00:00',
            'total_following' => 800700,
            'engagement_rate' => 21.05,
            'status' => 'Pending'
        ]);
        $campaign->records()->attach([3337, 8805, 9607]);
        Deliverable::create([
            'campaign_id' => $campaign->id,
            'record_id' => 3337,
            'platform' => 'Facebook',
            'type' => 'Post',
            'price' => 1000,
        ]);
        Deliverable::create([
            'campaign_id' => $campaign->id,
            'record_id' => 3337,
            'platform' => 'Instagram',
            'type' => 'Video',
            'price' => 2000,
        ]);
        Deliverable::create([
            'campaign_id' => $campaign->id,
            'record_id' => 8805,
            'platform' => 'Instagram',
            'type' => 'Story',
            'price' => 1000,
        ]);
        Deliverable::create([
            'campaign_id' => $campaign->id,
            'record_id' => 9607,
            'platform' => 'YouTube',
            'type' => 'Video',
            'price' => 1000,
        ]);
        Deliverable::create([
            'campaign_id' => $campaign->id,
            'record_id' => 9607,
            'platform' => 'Twitter',
            'type' => 'Post',
            'price' => 1000,
        ]);
    }
}
