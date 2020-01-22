<?php

namespace App\Console\Commands;

use App\Helpers\SocialDataApi;
use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RunSocAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gush:force-update-socapi {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force update all instagram account with socapi which are not updated within 7 days.';

    protected $socialDataApi;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SocialDataApi $socialDataApi)
    {
        $this->socialDataApi = $socialDataApi;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->argument('id')) {
            $record = Record::find($this->argument('id'));

            if (!$record) {
                $this->info('record not found ' . $this->argument('id'));
                return;
            }
            $this->socialDataApi->update($record);
        } else {
            do {
                $records = Record::whereNull('deleted_at')
                    ->whereNotNull('instagram_id')
                    ->where(function ($query) {
                        $query->where('instagram_socapi_updated_at', '<', Carbon::now()->startOfDay()->subWeek())
                            ->orWhereNull('instagram_socapi_updated_at');
                    })
                    ->take(30)
                    ->get();

                if ($records->count() > 0) {
                    foreach ($records as $record) {
                        $this->socialDataApi->update($record);
                    }
                }
            } while ($records->count() > 0);
        }
    }
}
