<?php

namespace App\Console\Commands;

use App\Helpers\TagHelper;
use App\Models\Record;
use App\Models\Tag;
use Illuminate\Console\Command;

class UpdateGTAExclusiveFlagToATag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:gta-flag-to-tag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the data of gta exclusive flag to a tag';

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
        $gtaTag = Tag::where('name', 'GTA')->first();
        $count = 0;

        Record::whereNotNull('recommended')->chunk(100, function($records) use ($gtaTag, &$count) {
            foreach($records as $record) {
                TagHelper::restoreOrCreateTagRecordPivot($gtaTag, $record);
                $count++;
                $this->info('update: '.$record->id);
            }
        });

        $this->info($count. ' update complete');
    }
}
