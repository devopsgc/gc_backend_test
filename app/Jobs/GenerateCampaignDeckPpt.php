<?php

namespace App\Jobs;

use App\Helpers\PptConfigGenerator;
use App\Models\Deliverable;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpPresentation\Shape\Drawing\Base64;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

class GenerateCampaignDeckPpt extends GenerateDeckPpt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Report $report)
    {
        parent::__construct($report);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();
    }

    private function createNewSlideWithTableHeader()
    {
        $campaign = $this->report->campaign;
        $objPhpPresentation = $this->objPhpPresentation;

        // create slide
        $this->activeSlideIndex++;
        if ($this->activeSlideIndex) {
            $objPhpPresentation->createSlide();
            $objPhpPresentation->setActiveSlideIndex($this->activeSlideIndex);
        }
        $activeSlide = $objPhpPresentation->getActiveSlide();

        $this->add_gushcloud_header($activeSlide);
        $this->add_footer($activeSlide);

        // create table
        $tableShape = $activeSlide->createTableShape(4);

        $tableShape->setHeight(20);
        $tableShape->setWidth(940);
        $tableShape->setOffsetX(10);
        $tableShape->setOffsetY(55);

        // create table headers
        $row = $tableShape->createRow();
        $row->getFill()->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color(Color::COLOR_BLACK))
            ->setEndColor(new Color(Color::COLOR_BLACK));
        $headers = ['Influencer', 'Social Media Stats', 'Deliverables & External Cost', 'Nett Costing ('.$campaign->currency_code.')'];
        foreach ($headers as $key => $header) {
            $cell = $row->nextCell();
            $cell->createTextRun($header)->getFont()->setBold(true)->setSize($this->primaryFont)->setColor(new Color(Color::COLOR_WHITE));
            $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            if ($key != 0) {
                $cell->getBorders()->getLeft()->setLineWidth(1)
                    ->setLineStyle(Border::LINE_SINGLE)
                    ->setColor(new Color(Color::COLOR_WHITE));
            }
            if ($key != count($headers) - 1) {
                $cell->getBorders()->getRight()->setLineWidth(1)
                    ->setLineStyle(Border::LINE_SINGLE)
                    ->setColor(new Color(Color::COLOR_WHITE));
            }

            switch ($key) {
                case 0:
                    $cell->setWidth(200);
                    break;
                case 1:
                    $cell->setWidth(400);
                    break;
                case 2:
                    $cell->setWidth(200);
                    break;
                case 3:
                    $cell->setWidth(140);
                    break;
            }
            if ($key == 1) {
                $cell->setWidth(400);
            }
        }
        return $tableShape;
    }

    protected function generateCampaignSlides()
    {
        $campaign = $this->report->campaign;
        $objPhpPresentation = $this->objPhpPresentation;
        if (!$campaign) {
            return;
        }
        $activeSlide = $objPhpPresentation->getActiveSlide();

        foreach ($campaign->records as $key => $record) {
            $recordsPerSlide = 4;
            if ($key % $recordsPerSlide == 0) {
                $tableShape = $this->createNewSlideWithTableHeader();
                $activeSlide = $objPhpPresentation->getActiveSlide();
            }
            $row = $tableShape->createRow();

            // influencer
            $influencerCell = $row->getCell(0);
            $influencerCell->getActiveParagraph()->getAlignment()->setMarginTop(50);

            $shape = new Base64();
            if (env('APP_ENV') === 'production') {
                $shape->setData('data:image/jpeg;base64,'.base64_encode(file_get_contents($record->display_photo)))
                    ->setResizeProportional(false)->setHeight(50)->setWidth(50)
                    ->setOffsetX(30)->setOffsetY(($key % $recordsPerSlide) * 80 + 105);
                $shape = $activeSlide->addShape($shape);
            } else {
                // disable the ssl peer verification for `no`n-prod environment for testing till https is setup
                $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );
                $shape->setData('data:image/jpeg;base64,'.base64_encode(file_get_contents($record->display_photo, false, stream_context_create($arrContextOptions))))
                    ->setResizeProportional(false)->setHeight(50)->setWidth(50)
                    ->setOffsetX(30)->setOffsetY(($key % $recordsPerSlide) * 80 + 105);
            }

            $influencerText = $activeSlide->createRichTextShape();
            $influencerText->setOffsetX(90)->setOffsetY(($key % $recordsPerSlide) * 80 + 100)->setWidth(100)->setHeight(50);

            $influencerText->createTextRun($record->name)->getFont()->setSize($this->primaryFont);
            $influencerText->createTextRun("\n");

            if ($record->interestsDisplay) {
                $influencerText->createTextRun($record->interestsDisplay)->getFont()->setSize(5);
            }

            // social media stats
            $socialStatsCell = $row->getCell(1);
            // get unique deliverable platform and type
            $totalFollowers = 0;
            $postEngagementRate = 0;
            $videoEngagementRate = 0;
            $postFollowers = 0;
            $videoFollowers = 0;

            $platforms = [];
            foreach (Deliverable::PLATFORMS as $platform) {
                if ($campaign->deliverables->where('record_id', $record->id)->where('platform', $platform)->count()) {
                    // display icon + followers

                    $followersAttribute = strtolower($platform) == 'youtube' ? strtolower($platform).'_subscribers' : strtolower($platform).'_followers';
                    $totalFollowers += $record[$followersAttribute];
                    $postFollowers += strtolower($platform) == 'youtube' || strtolower($platform) == 'twitter' ? 0 : $record[$followersAttribute];
                    $videoFollowers += strtolower($platform) == 'twitter' ? 0 : $record[$followersAttribute];

                    $postEngagementAttribute = '_engagement_rate_post';
                    $videoEngagementAttribute = strtolower($platform) == 'youtube' ? '_view_rate' : '_engagement_rate_video';
                    $postEngagementRate += strtolower($platform) == 'youtube' || strtolower($platform) == 'twitter' ? 0 : $record[strtolower($platform).$postEngagementAttribute];
                    $videoEngagementRate += strtolower($platform) == 'twitter' ? 0 : $record[strtolower($platform).$videoEngagementAttribute];

                    $platforms[] = [
                        'platform' => strtolower($platform),
                        'followers' => $record[$followersAttribute] ? number_format($record[$followersAttribute]) : '-',
                    ];
                }
            }

            $totalPlatforms = count($platforms);
            foreach ($platforms as $index => $platform) {
                // platform icon
                $platformIcon = $activeSlide->createDrawingShape();
                if ($totalPlatforms % 2 == 1) { // odd number of platforms
                    $offsetX = 395 + ($index - ($totalPlatforms - 1) / 2) * 60;
                } else { // even number of platforms
                    $offsetX = 395 + 30 + ($index - $totalPlatforms / 2) * 60;
                }
                $platformIcon->setPath(public_path('img/icon-'.$platform['platform'].'.png'))
                    ->setOffsetX($offsetX)->setOffsetY((($key % $recordsPerSlide) * 80) + 100)->setWidth(30)->setHeight(30);

                // follower count
                $followerCount = $activeSlide->createRichTextShape();
                $followerCount->setOffsetX($offsetX - 25)->setOffsetY(($key % $recordsPerSlide) * 80 + 100 + 25)->setWidth(80)->setHeight(30);
                $followerCount->createTextRun($platform['followers'])->getFont()->setBold(true)->setSize($this->smallFont);
                $followerCount->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $followerCount->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            }

            $socialStatsCell->createTextRun("\n");
            $socialStatsCell->createTextRun("\n");
            $socialStatsCell->createTextRun("\n");
            $socialStatsCell->createTextRun('Total Followers - ')->getFont()->setSize($this->smallFont);
            $socialStatsCell->createTextRun(number_format($totalFollowers))->getFont()->setBold(true)->setSize($this->smallFont);
            $socialStatsCell->createTextRun(' | ')->getFont()->setSize($this->smallFont);

            $socialStatsCell->createTextRun('Post ER - ')->getFont()->setSize($this->smallFont);
            $socialStatsCell->createTextRun(($postFollowers > 0 ? number_format($postEngagementRate / $postFollowers * 100, 2).'%' : 'N/A'))->getFont()->setBold(true)->setSize($this->smallFont);
            $socialStatsCell->createTextRun(' | ')->getFont()->setSize($this->smallFont);

            $socialStatsCell->createTextRun('Video ER - ')->getFont()->setSize($this->smallFont);
            $socialStatsCell->createTextRun(($videoFollowers > 0 ? number_format($videoEngagementRate / $videoFollowers * 100, 2).'%' : 'N/A'))->getFont()->setBold(true)->setSize($this->smallFont);

            $socialStatsCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $socialStatsCell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // deliverables
            $deliverablesCell = $row->getCell(2);
            $deliverables = $campaign->deliverables()->where('record_id', $record->id)->get();
            foreach ($deliverables as $key => $deliverable) {
                $deliverablesCell->createTextRun($deliverable->quantity.'x '.$deliverable->platform.' '.$deliverable->type);
                if ($deliverable->price) {
                    $deliverablesCell->createTextRun(' ($'.$deliverable->price.')');
                }
                if ($key != $deliverables->count() - 1) {
                    $deliverablesCell->createTextRun("\n");
                }
            }
            $deliverablesCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $deliverablesCell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // nett cost
            $nettCostCell = $row->getCell(3);
            $nettCostCell->createTextRun('$'.number_format($record->pivot->package_price));
            $nettCostCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $nettCostCell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        // grand total (net) and grand total (package)
        $row = $tableShape->createRow();
        $grandTotalCell = $row->getCell(2);
        $grandTotalCell->createTextRun('Grand Total (Nett)')->getFont()->setBold(true)->setSize($this->primaryFont);
        $grandTotalCell->createTextRun("\n");
        $grandTotalCell->createTextRun('Grand Total (Package)')->getFont()->setBold(true)->setSize($this->primaryFont);
        $grandTotalCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $grandTotalCell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $grandTotalCell->getActiveParagraph()->getAlignment()->setMarginTop(10)->setMarginBottom(10)->setMarginRight(10);

        $grandTotalValuesCell = $row->getCell(3);
        $grandTotalValuesCell->createTextRun('$'.number_format($campaign->records->sum('pivot.package_price')))->getFont()->setBold(true)->setSize($this->primaryFont);
        $grandTotalValuesCell->createTextRun("\n");
        $grandTotalValuesCell->createTextRun('$'.number_format($campaign->total_price))->getFont()->setBold(true)->setSize($this->primaryFont);
        $grandTotalValuesCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $grandTotalValuesCell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    protected function generateSlides()
    {
        if (PptConfigGenerator::hasNetCostingConfig($this->report->config)) {
            $this->generateCampaignSlides();
        }
        $this->generateInfluencerSlides();
    }
}
