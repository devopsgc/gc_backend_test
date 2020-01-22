<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GetFacebookStats as Job;
use App\Models\Record;
use Carbon\Carbon;

class GetFacebookStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:facebook {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Facebook stats';

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
        if ($record = Record::find($this->argument('id')))
        {
            $record->facebook_updated_at = Carbon::now();
            $record->save();
            Job::dispatch($record->id);
        }
        elseif ($records = Record::whereNotNull('facebook_id')
            ->whereNull('facebook_user_page')
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->where('facebook_updated_at', '<', Carbon::now()->subWeeks(1)->format('Y-m-d H:i:s'));
                $query->orWhereNull('facebook_updated_at');
            })
            ->orderBy('facebook_updated_at', 'asc')
            ->take(1)
            ->get())
        {
            foreach ($records as $record)
            {
                $record->facebook_updated_at = Carbon::now();
                $record->save();
                Job::dispatch($record->id);
            }
        }
    }
}
