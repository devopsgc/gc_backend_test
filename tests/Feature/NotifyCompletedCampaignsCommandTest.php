<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Notifications\NotifyCampaignCompleted;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotifyCompletedCampaignsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_campaigns_that_are_passed_the_campaign_time()
    {
        Notification::fake();

        $endedCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_ACCEPTED,
            'start_at' => Carbon::now()->subMonth(),
            'end_at' => Carbon::now()->subWeek(),
        ]);

        $runningCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_ACCEPTED,
            'start_at' => Carbon::now()->subMonth(),
            'end_at' => Carbon::now()->addWeek(),
        ]);

        $noEndingCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_ACCEPTED,
            'start_at' => Carbon::now()->subMonth(),
            'end_at' => null,
        ]);

        $this->artisan('notify:users-campaign-completed');

        Notification::assertSentTo(
            [$endedCampaign->createdBy], NotifyCampaignCompleted::class
        );

        Notification::assertNotSentTo(
            [$runningCampaign->createdBy], NotifyCampaignCompleted::class
        );

        Notification::assertNotSentTo(
            [$noEndingCampaign->createdBy], NotifyCampaignCompleted::class
        );
    }

    public function test_only_accepted_campaigns_sent_notification()
    {
        Notification::fake();

        $acceptedCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_ACCEPTED,
            'start_at' => Carbon::now()->subMonth(),
            'end_at' => Carbon::now()->subWeek(),
        ]);

        $draftCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'start_at' => Carbon::now()->subMonth(),
            'end_at' => Carbon::now()->subWeek(),
        ]);

        $this->artisan('notify:users-campaign-completed');

        Notification::assertSentTo(
            [$acceptedCampaign->createdBy], NotifyCampaignCompleted::class
        );

        Notification::assertNotSentTo(
            [$draftCampaign->createdBy], NotifyCampaignCompleted::class
        );
    }

    public function test_only_notify_email_once()
    {
        Notification::fake();

        $acceptedCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_ACCEPTED,
            'start_at' => Carbon::now()->subMonth(),
            'end_at' => Carbon::now()->subWeek(),
        ]);

        $this->artisan('notify:users-campaign-completed');

        Notification::assertSentTo(
            [$acceptedCampaign->createdBy], NotifyCampaignCompleted::class
        );

        Notification::fake();

        $this->artisan('notify:users-campaign-completed');

        Notification::assertNotSentTo(
            [$acceptedCampaign->createdBy], NotifyCampaignCompleted::class
        );
    }
}
