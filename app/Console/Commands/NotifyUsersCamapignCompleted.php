<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Notifications\NotifyCampaignCompleted;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class NotifyUsersCamapignCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:users-campaign-completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users when their campaigns have completed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Campaign::where('status', Campaign::STATUS_ACCEPTED)
            ->where('end_at', '<', Carbon::now())
            ->whereNull('notify_completed_at')
            ->chunk(100, function($campaigns) {
                foreach ($campaigns as $campaign) {
                    $campaign->createdBy->notify(new NotifyCampaignCompleted($campaign));
                    $campaign->update(['notify_completed_at' => Carbon::now()]);
                }
        });
    }
}
