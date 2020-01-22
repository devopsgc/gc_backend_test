<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GetYoutubeStats as Job;
use App\Models\Record;
use Carbon\Carbon;

class GetYouTubeStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:youtube {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get YouTube stats';

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
            $record->youtube_updated_at = Carbon::now();
            $record->save();
            Job::dispatch($record->id);
        }
        elseif ($records = Record::whereNotNull('youtube_id')
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->where('youtube_updated_at', '<', Carbon::now()->subWeeks(1)->format('Y-m-d H:i:s'));
                $query->orWhereNull('youtube_updated_at');
            })
            ->orderBy('youtube_updated_at', 'asc')
            ->take(1)
            ->get())
        {
            foreach ($records as $record)
            {
                $record->youtube_updated_at = Carbon::now();
                $record->save();
                Job::dispatch($record->id);
            }
        }
    }
}
