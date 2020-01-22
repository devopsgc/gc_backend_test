<?php

namespace App\Jobs;

use App\Helpers\SocialDataHelper;
use App\Helpers\SocialDataApi;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Shape\Hyperlink;
use PhpOffice\PhpPresentation\Shape\Chart\Type\Bar;
use PhpOffice\PhpPresentation\Shape\Chart\Type\Pie;
use PhpOffice\PhpPresentation\Shape\Chart\Series;
use PhpOffice\PhpPresentation\Shape\Drawing\Base64;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Color;
use App\Mail\ReportReady;
use App\Models\Record;
use App\Models\Report;
use Carbon\Carbon;
use Storage;
use Mail;
use Image;

class GenerateDeckPpt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report;
    protected $objPhpPresentation;

    protected $image = [
        'instagram' => 'icon-instagram.png',
        'twitter' => 'icon-twitter.png',
        'youtube' => 'icon-youtube.png',
        'facebook' => 'icon-facebook.png',
    ];

    protected $primaryFont = 10;
    protected $smallFont = 8;
    protected $maxWidth = 960;
    protected $maxHeight = 540;
    protected $segmentYPosition = [0, 60, 120, 170, 290, 520];
    protected $segmentYPositionDetailSlide = [0, 50, 70, 220, 370, 510];
    protected $segmentXPosition = [0, 10, 380, 670];

    protected $activeSlideIndex = -1;
    protected $debug = false;

    protected $socialDataApi;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Report $report)
    {
        $this->socialDataApi = new SocialDataApi;
        $this->report = $report;
        $this->objPhpPresentation = new PhpPresentation();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);

        $this->setupDocument();
        $this->generateSlides();
        $this->writePptToFile();

        $this->report->generated_at = Carbon::now();
        $this->report->save();

        Mail::to($this->report->user)->send(new ReportReady($this->report));
    }

    protected function generateInfluencerSlides()
    {
        foreach (explode("\n", $this->report->records) as $index => $id)
        {
            $record = Record::withTrashed()->find($id);

            // If record does not have instagram socapi data
            // or the data is more than 30 days old, we will call socapi
            $this->updateRecordData($record);
            $this->activeSlideIndex++;
            $this->create_profile_slide($record, $this->objPhpPresentation);

            if ($record->instagramSocapiData)
            {
                $this->activeSlideIndex++;
                $this->create_demographic_slide($record, $this->objPhpPresentation);
            }
        }
    }

    protected function generateSlides()
    {
        $this->generateInfluencerSlides();
    }

    protected function writePptToFile()
    {
        $oWriterPPTX = IOFactory::createWriter($this->objPhpPresentation, 'PowerPoint2007');
        $oWriterPPTX->save(storage_path('app/'.$this->report->file));
        $file = Storage::disk('local')->get($this->report->file);
        $storagePath = Storage::put($this->report->relative_file_url, $file);
        Storage::disk('local')->delete($this->report->file);
    }

    protected function setupDocument()
    {
        // Set document properties...
        $this->objPhpPresentation->getDocumentProperties()
            ->setCreator($this->report->user->first_name.' '.$this->report->user->last_name)
            ->setTitle('Document Title')
            ->setSubject('Document Subject')
            ->setDescription('Document Description')
            ->setCompany('Gushcloud')
            ->setCategory('Sales Deck');

        // Set document layout to 16:9...
        $this->objPhpPresentation->getLayout()
            ->setDocumentLayout(DocumentLayout::LAYOUT_SCREEN_16X9);
    }

    protected function updateRecordData($record)
    {
        if ($record->instagram_id && ! $record->instagram_update_disabled_at && SocialDataHelper::canUpdateForDownloadSlide($record))
        {
            $this->socialDataApi->update($record);
            $record->refresh();
        }
    }

    private function create_profile_slide($record, $objPhpPresentation)
    {
        if ($this->activeSlideIndex)
        {
            $objPhpPresentation->createSlide();
            $objPhpPresentation->setActiveSlideIndex($this->activeSlideIndex);
        }

        $activeSlide = $objPhpPresentation->getActiveSlide();

        if ($this->debug) {
            $this->drawLines($activeSlide, $this->segmentYPosition, $this->segmentXPosition);
        }

        $this->add_gushcloud_header($activeSlide);
        $this->add_footer($activeSlide);

        // COUNTRY
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX($this->segmentXPosition[1])->setOffsetY($this->segmentYPosition[1]-5)->setWidth(40)->setHeight(20);
        $shape->setInsetLeft(0);
        $shape->setInsetRight(0);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $shape->getBorder()
            ->setLineStyle(Border::LINE_SINGLE)
            ->setDashStyle(Border::DASH_SOLID)
            ->setColor(new Color('FF666666'));
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX($this->segmentXPosition[1])->setOffsetY($this->segmentYPosition[1]-9)->setWidth(40)->setHeight(20);
        $shape->setInsetLeft(0);
        $shape->setInsetRight(0);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun($record->country->iso_3166_2);
        $textRun->getFont()->setBold(true)->setSize(12)->setName('Helvetica')->setColor(new Color('FF666666'));

        // NAME
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX(10)->setOffsetY($this->segmentYPosition[1]+30)->setWidth(820)->setHeight(40);
        $shape->setInsetLeft(0);
        $shape->setInsetRight(0);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $textRun = $shape->createTextRun(mb_strimwidth(mb_strtolower($record->name), 0, 48, "..."));
        $textRun->getFont()->setBold(true)->setSize(26)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

        // TOTAL REACH TILTE
        $reach_title_width = 150;
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX($this->maxWidth-$reach_title_width-20)->setOffsetY($this->segmentYPosition[1])->setWidth($reach_title_width)->setHeight(20);
        $shape->setInsetLeft(0);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $textRun = $shape->createTextRun('Followers');
        $textRun->getFont()->setBold(false)->setSize(14)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

        // TOTAL REACH NUMBER
        $reach_number_width = 150;
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX($this->maxWidth-$reach_number_width-20)->setOffsetY($this->segmentYPosition[1]+30)->setWidth($reach_number_width)->setHeight(40);
        $shape->setInsetLeft(0);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $total_followers = $record->facebook_followers + $record->instagram_followers + $record->twitter_followers + $record->blog_followers + $record->youtube_subscribers + $record->weibo_followers + $record->xiaohongshu_followers + $record->miaopai_followers + $record->yizhibo_followers;
        $textRun = $shape->createTextRun($this->formatNumber($total_followers));
        $textRun->getFont()->setBold(true)->setSize(26)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

        // VERTICALS
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX(10)->setOffsetY($this->segmentYPosition[2] + 15)->setWidth($this->maxWidth-20)->setHeight(20);
        $shape->setInsetLeft(0);
        $shape->setInsetRight(0);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $verticals = ucwords(implode(', ', $record->pptVertical->pluck('name')->toArray()));
        $textRun = $shape->createTextRun($verticals ? $verticals : '-');
        $textRun->getFont()->setBold(false)->setSize(14)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

        // PROFILE PICTURE
        $shape = new Base64();
        // todo: remove after https is setup
        if (env('APP_ENV') === 'production') {
            $shape->setData('data:image/jpeg;base64,'.base64_encode(file_get_contents($record->display_photo)))
                ->setResizeProportional(false)->setHeight(350)->setWidth(350)
                ->setOffsetX(10)->setOffsetY($this->segmentYPosition[3]-10);
            $shape = $activeSlide->addShape($shape);
        } else if(env('APP_ENV') !== 'testing') {
            // disable the ssl peer verification for non-prod environment for testing till https is setup
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );
            $shape->setData('data:image/jpeg;base64,'.base64_encode(file_get_contents($record->display_photo, false, stream_context_create($arrContextOptions))))
                ->setResizeProportional(false)->setHeight(350)->setWidth(350)
                ->setOffsetX(10)->setOffsetY($this->segmentYPosition[3]-10);
            $shape = $activeSlide->addShape($shape);
        }

        // DESCRIPTION
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX($this->segmentXPosition[2])->setOffsetY($this->segmentYPosition[3])->setWidth($this->maxWidth-$this->segmentXPosition[2]-40)->setHeight(80);
        $shape->setInsetLeft(0);
        $shape->setInsetRight(0);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $description = $record->getDescriptionPpt($this->report->language) ? mb_strimwidth($record->getDescriptionPpt($this->report->language), 0, 253, "...") : '-';
        $textRun = $shape->createTextRun($description);
        $textRun->getFont()->setBold(false)->setSize(12)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

        $this->social_box($activeSlide, $record, $this->segmentXPosition[2], $this->segmentYPosition[4], 230, $this->maxWidth - $this->segmentXPosition[2] - 30);
    }

    function create_piechart_slide(PhpPresentation $objPhpPresentation, $socapi) {

        $currentSlide = $objPhpPresentation->getActiveSlide();

        $seriesData = array();
        if (isset($socapi->audience_followers->data->audience_genders))
        {
            foreach ($socapi->audience_followers->data->audience_genders as $data)
            {
                if (strtolower($data->code) == 'male') {
                    $seriesData['Men'] = $data->weight;
                } elseif (strtolower($data->code) == 'female') {
                    $seriesData['Women'] = $data->weight;
                } else {
                    $seriesData[$data->code] = $data->weight;
                }
            }
            ksort($seriesData);

            // Create a pie chart
            $pieChart = new Pie();
            $pieChart->setExplosion(15);
            $series = new Series('Downloads', $seriesData);
            $series->setShowPercentage(true);
            $series->setShowValue(false);
            $series->setShowSeriesName(false);
            $series->setShowCategoryName(false);
            $series->setDlblNumFormat('%d');
            $series->getFont()->setSize($this->smallFont);
            $pieChart->addSeries($series);

            // Create a shape (chart)
            $shape = $currentSlide->createChartShape();
            $shape->setName('Gender Split')
                  ->setResizeProportional(false)
                  ->setHeight(110)
                  ->setWidth(240)
                  ->setOffsetX($this->segmentXPosition[2])
                  ->setOffsetY($this->segmentYPositionDetailSlide[2] + 20);
            $shape->getTitle()->setVisible(false);
            $shape->getPlotArea()->setType($pieChart);
            $shape->getLegend()->getBorder()->setLineStyle(Border::LINE_NONE);
        }
    }

    private function create_barchart_slide($record, $objPhpPresentation, $socapi)
    {
        $currentSlide = $objPhpPresentation->getActiveSlide();

        $series1Data = array();
        $series2Data = array();
        if (isset($socapi->audience_followers->data->audience_genders_per_age))
        {
            foreach ($socapi->audience_followers->data->audience_genders_per_age as $data)
            {
                $series1Data[$data->code] = $data->male;
                $series2Data[$data->code] = $data->female;
            }

            // Create a bar chart
            $barChart = new Bar();
            $barChart->setGapWidthPercent(158);
            $series1 = new Series('Men', $series1Data);
            $series1->setShowSeriesName(false);
            $series1->setLabelPosition(Series::LABEL_OUTSIDEEND);
            $series1->setShowPercentage(false);
            $series1->getFont()->setSize($this->smallFont);
            $series1->setDlblNumFormat('#%');
            $series2 = new Series('Women', $series2Data);
            $series2->setShowSeriesName(false);
            $series2->setLabelPosition(Series::LABEL_OUTSIDEEND);
            $series2->setShowPercentage(false);
            $series2->getFont()->setSize($this->smallFont);
            $series2->setDlblNumFormat('#%');
            $barChart->addSeries($series1);
            $barChart->addSeries($series2);

            // Create a shape (chart)
            $shape = $currentSlide->createChartShape();
            $shape->setName('Age & Gender Split')
                ->setResizeProportional(false)
                ->setHeight(120)
                ->setWidth(280)
                ->setOffsetX($this->segmentXPosition[2])
                ->setOffsetY($this->segmentYPositionDetailSlide[3] + 20);
            $shape->getTitle()->setVisible(false);
            $shape->getPlotArea()->getAxisX()->setTitle(null);
            $shape->getPlotArea()->getAxisY()->setIsVisible(false);
            $shape->getPlotArea()->getAxisY()->setTitle(null);
            $shape->getPlotArea()->setType($barChart);
            $shape->getLegend()->setVisible(false);
            $shape->getLegend()->getFont()->setItalic(true);
        }
    }

    private function create_demographic_slide($record, $objPhpPresentation)
    {
        $socapi = json_decode(json_encode($record->instagramSocapiData));

        $objPhpPresentation->createSlide();
        $objPhpPresentation->setActiveSlideIndex($this->activeSlideIndex);

        $activeSlide = $objPhpPresentation->getActiveSlide();

        if ($this->debug) {
            $this->drawLines($activeSlide, $this->segmentYPositionDetailSlide, $this->segmentXPosition);
        }

        $this->add_gushcloud_header($activeSlide);
        $this->add_footer($activeSlide);

        // TOP POSTS
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX(10)->setOffsetY($this->segmentYPosition[1]-20)->setWidth($this->segmentXPosition[2])->setHeight(20);
        $shape->setInsetLeft(0);
        $shape->setInsetRight(0);
        $textRun = $shape->createTextRun('Top Posts');
        $textRun->getFont()->setBold(true)->setSize(12)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

        // TOP 6 POSTS
        if (isset($socapi->user_profile->top_posts))
        {
            $posts = $socapi->user_profile->top_posts;
            foreach ($posts as $index => $post)
            {
                if ($index == 6) break;

                $widthOfTopPostBox = 145;
                $offsetX = 10;
                $offsetY = 10;
                $startBoxX = ($offsetX + ($index%2 * 5)) + ($widthOfTopPostBox * ($index%2));
                $startBoxY = $this->segmentYPositionDetailSlide[2 + floor($index/2)];

                // TOP POST THUMBNAIL
                $shape = new Base64();
                if ($image = @file_get_contents(str_replace('https://imgp.socapi.icu', 'http://socapi.gush.co', $post->thumbnail)))
                {
                    $image = Image::make($image)->fit(140)->encode('data-url');
                    $shape->setData($image)
                            ->setResizeProportional(false)->setHeight(140)->setWidth(140)
                            ->setOffsetX($startBoxX)->setOffsetY($startBoxY);
                    $shape = $activeSlide->addShape($shape);
                }

                // LIKES AND COMMENTS
                $shape = $activeSlide->createRichTextShape();
                $shape->setOffsetX($startBoxX)->setOffsetY($startBoxY)->setWidth(140)->setHeight(140);
                $shape->setInsetLeft(0);
                $shape->setInsetRight(10);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
                $textRun = $shape->createTextRun(
                    'Likes: '.number_format($post->stat->likes)."\n".
                    'Comments: '.number_format($post->stat->comments)
                );
                $textRun->getFont()->setSize($this->smallFont)->setName('Helvetica')->setColor(new Color(Color::COLOR_WHITE));
            }
        }

        // GENDER SPLIT
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX($this->segmentXPosition[2])->setOffsetY($this->segmentYPositionDetailSlide[2])->setWidth(280)->setHeight(140);
        $shape->getBorder()
            ->setLineStyle(Border::LINE_SINGLE)
            ->setDashStyle(Border::DASH_SOLID)
            ->setLineWidth(4)
            ->setColor(new Color('F0F4F2'));
        $shape->setInsetLeft(10);
        $shape->setInsetRight(10);
        $textRun = $shape->createTextRun('Gender Split'."\n");
        $textRun->getFont()->setBold(true)->setSize(12)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));
        $this->create_piechart_slide($objPhpPresentation, $socapi);

        // TOP COUNTRY
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX($this->segmentXPosition[3])->setOffsetY($this->segmentYPositionDetailSlide[2])->setWidth(280)->setHeight(140);
        $shape->getBorder()
            ->setLineStyle(Border::LINE_SINGLE)
            ->setDashStyle(Border::DASH_SOLID)
            ->setLineWidth(4)
            ->setColor(new Color('F0F4F2'));
        $shape->setInsetLeft(10);
        $shape->setInsetRight(10);
        $textRun = $shape->createTextRun('Top Country'."\n");
        $textRun->getFont()->setBold(true)->setSize(12)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

        for ($index = 0; $index < 3; $index++)
        {
            if (isset($socapi->audience_followers->data->audience_geo->countries[$index]))
            {
                // COUNTRY ICON
                $shape = $activeSlide->createDrawingShape();
                $shape->setPath(public_path('flags2/'.$socapi->audience_followers->data->audience_geo->countries[$index]->code.'.png'))
                    ->setOffsetX($this->segmentXPosition[3] + 18)->setOffsetY($this->segmentYPositionDetailSlide[2] + 40 + $index * 30)->setWidth(22)->setHeight(22);

                // DATA
                $shape = $activeSlide->createRichTextShape();
                $shape->setOffsetX($this->segmentXPosition[3] + 52)->setOffsetY($this->segmentYPositionDetailSlide[2] + 46 + $index * 30)->setWidth(105)->setHeight(20);
                $shape->setInsetLeft(0);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $textRun = $shape->createTextRun($socapi->audience_followers->data->audience_geo->countries[$index]->name);
                $textRun->getFont()->setBold(true)->setSize(10)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

                // VALUE
                $shape = $activeSlide->createRichTextShape();
                $shape->setOffsetX($this->segmentXPosition[3] + 162)->setOffsetY($this->segmentYPositionDetailSlide[2] + 46 + $index * 30)->setWidth(105)->setHeight(20);
                $shape->setInsetLeft(0);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $textRun = $shape->createTextRun($this->formatWeight($socapi->audience_followers->data->audience_geo->countries[$index]->weight));
                $textRun->getFont()->setBold(true)->setSize(10)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));
            }
        }

        // AGE & GENDER SPLIT
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX($this->segmentXPosition[2])->setOffsetY($this->segmentYPositionDetailSlide[3])->setWidth(280)->setHeight(140);
        $shape->getBorder()
            ->setLineStyle(Border::LINE_SINGLE)
            ->setDashStyle(Border::DASH_SOLID)
            ->setLineWidth(4)
            ->setColor(new Color('F0F4F2'));
        $shape->setInsetLeft(10);
        $shape->setInsetRight(10);
        $textRun = $shape->createTextRun('Age & Gender Split'."\n");
        $textRun->getFont()->setBold(true)->setSize(12)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));
        $this->create_barchart_slide($record, $objPhpPresentation, $socapi);

        // TOP CITY
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX($this->segmentXPosition[3])->setOffsetY($this->segmentYPositionDetailSlide[3])->setWidth(280)->setHeight(140);
        $shape->getBorder()
            ->setLineStyle(Border::LINE_SINGLE)
            ->setDashStyle(Border::DASH_SOLID)
            ->setLineWidth(4)
            ->setColor(new Color('F0F4F2'));
        $shape->setInsetLeft(10);
        $shape->setInsetRight(10);
        $textRun = $shape->createTextRun('Top City'."\n");
        $textRun->getFont()->setBold(true)->setSize(12)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

        for ($index = 0; $index < 3; $index++)
        {
            if (isset($socapi->audience_followers->data->audience_geo->cities[$index]))
            {
                // COUNTRY ICON
                $shape = $activeSlide->createDrawingShape();
                $shape->setPath(public_path('flags2/'.$socapi->audience_followers->data->audience_geo->cities[$index]->country->code.'.png'))
                    ->setOffsetX($this->segmentXPosition[3] + 18)->setOffsetY($this->segmentYPositionDetailSlide[3] + 40 + $index * 30)->setWidth(22)->setHeight(22);

                // DATA
                $shape = $activeSlide->createRichTextShape();
                $shape->setOffsetX($this->segmentXPosition[3] + 52)->setOffsetY($this->segmentYPositionDetailSlide[3] + 46 + $index * 30)->setWidth(105)->setHeight(20);
                $shape->setInsetLeft(0);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $textRun = $shape->createTextRun($socapi->audience_followers->data->audience_geo->cities[$index]->name);
                $textRun->getFont()->setBold(true)->setSize(10)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

                // VALUE
                $shape = $activeSlide->createRichTextShape();
                $shape->setOffsetX($this->segmentXPosition[3] + 162)->setOffsetY($this->segmentYPositionDetailSlide[3] + 46 + $index * 30)->setWidth(105)->setHeight(20);
                $shape->setInsetLeft(0);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $textRun = $shape->createTextRun($this->formatWeight($socapi->audience_followers->data->audience_geo->cities[$index]->weight));
                $textRun->getFont()->setBold(true)->setSize(10)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));
            }
        }

        // ETHNICITY
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX($this->segmentXPosition[2])->setOffsetY($this->segmentYPositionDetailSlide[4])->setWidth(280)->setHeight(140);
        $shape->getBorder()
            ->setLineStyle(Border::LINE_SINGLE)
            ->setDashStyle(Border::DASH_SOLID)
            ->setLineWidth(4)
            ->setColor(new Color('F0F4F2'));
        $shape->setInsetLeft(10);
        $shape->setInsetRight(10);
        $textRun = $shape->createTextRun('Ethnicity'."\n");
        $textRun->getFont()->setBold(true)->setSize(12)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

        $index = 0;
        if (isset($socapi->audience_followers->data->audience_ethnicities))
        foreach ($socapi->audience_followers->data->audience_ethnicities as $data)
        {
            // DATA
            $shape = $activeSlide->createRichTextShape();
            $shape->setOffsetX($this->segmentXPosition[2] + 20)->setOffsetY($this->segmentYPositionDetailSlide[4] + 40 + $index * 20)->setWidth(145)->setHeight(20);
            $shape->setInsetLeft(0);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $textRun = $shape->createTextRun($data->name);
            $textRun->getFont()->setBold(true)->setSize(10)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

            // VALUE
            $shape = $activeSlide->createRichTextShape();
            $shape->setOffsetX($this->segmentXPosition[2] + 160)->setOffsetY($this->segmentYPositionDetailSlide[4] + 40 + $index * 20)->setWidth(105)->setHeight(20);
            $shape->setInsetLeft(0);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $textRun = $shape->createTextRun($this->formatWeight($data->weight));
            $textRun->getFont()->setBold(true)->setSize(10)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

            $index++;
        }

        // AUDIENCE LANGUAGES
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX($this->segmentXPosition[3])->setOffsetY($this->segmentYPositionDetailSlide[4])->setWidth(280)->setHeight(140);
        $shape->getBorder()
            ->setLineStyle(Border::LINE_SINGLE)
            ->setDashStyle(Border::DASH_SOLID)
            ->setLineWidth(4)
            ->setColor(new Color('F0F4F2'));
        $shape->setInsetLeft(10);
        $shape->setInsetRight(10);
        $textRun = $shape->createTextRun('Audience Languages'."\n");
        $textRun->getFont()->setBold(true)->setSize(12)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

        for ($index = 0; $index < 3; $index++)
        {
            if (isset($socapi->audience_followers->data->audience_languages[$index]))
            {
                // DATA
                $shape = $activeSlide->createRichTextShape();
                $shape->setOffsetX($this->segmentXPosition[3] + 20)->setOffsetY($this->segmentYPositionDetailSlide[4] + 40 + $index * 30)->setWidth(105)->setHeight(20);
                $shape->setInsetLeft(0);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $textRun = $shape->createTextRun($socapi->audience_followers->data->audience_languages[$index]->name);
                $textRun->getFont()->setBold(true)->setSize(10)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

                // VALUE
                $shape = $activeSlide->createRichTextShape();
                $shape->setOffsetX($this->segmentXPosition[3] + 160)->setOffsetY($this->segmentYPositionDetailSlide[4] + 40 + $index * 30)->setWidth(105)->setHeight(20);
                $shape->setInsetLeft(0);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $textRun = $shape->createTextRun($this->formatWeight($socapi->audience_followers->data->audience_languages[$index]->weight));
                $textRun->getFont()->setBold(true)->setSize(10)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));
            }
        }
    }

    private function build_social($record)
    {
        $platforms = [];

        if ($record->instagram_id && $record->instagram_followers)
        {
            $platforms[] = [
                'platform' => 'instagram',
                'social_id' => $record->instagram_id,
                'url' => 'https://www.instagram.com/'.$record->instagram_id,
                'followers' => $record->instagram_followers,
                'avg_engagement' => number_format($record->instagram_engagement_rate_post),
                'engagement' => $this->formatNumber(number_format($record->instagram_engagement_rate_post/$record->instagram_followers*100, 2)).'%'
            ];
        }
        if ($record->facebook_id && $record->facebook_followers)
        {
            $platforms[] = [
                'platform' => 'facebook',
                'social_id' => $record->facebook_id,
                'url' => 'https://www.facebook.com/'.$record->facebook_id,
                'followers' => $record->facebook_followers,
                'avg_engagement' => number_format($record->facebook_engagement_rate_post),
                'engagement' => $this->formatNumber(number_format($record->facebook_engagement_rate_post/$record->facebook_followers*100, 2)).'%'];
        }
        if ($record->youtube_id && $record->youtube_subscribers)
        {
            $platforms[] = [
                'platform' => 'youtube',
                'social_id' => $record->youtube_name,
                'url' => 'https://www.youtube.com/channel/'.$record->youtube_id,
                'followers' => $record->youtube_subscribers,
                'avg_engagement' => $this->formatNumber($record->youtube_view_rate),
                'engagement' => null
            ];
        }
        if ($record->twitter_id && $record->twitter_followers)
        {
            $platforms[] = [
                'platform' => 'twitter',
                'social_id' => $record->twitter_id,
                'url' => 'https://www.twitter.com/'.$record->twitter_id,
                'followers' => $record->twitter_followers,
                'avg_engagement' => '-',
                'engagement' => '-'
            ];
        }

        return $platforms;
    }

    private function social_box($activeSlide, $record, $startX, $startY, $heightOfMainBox, $widthOfMainBox)
    {
        // SOCIAL STATS
        $platforms = $this->build_social($record);

        $widthOfSocialBox = floor($widthOfMainBox/3);
        $heightOfSocialBox = floor($heightOfMainBox/2);

        $offsetX1 = 55;
        $offsetX2 = 110;

        foreach ($platforms as $key => $platform) {
            $startBoxX = $startX + ($widthOfSocialBox * ($key%3));
            $startBoxY = $startY + ($heightOfSocialBox * floor($key/3));

            $shape = $activeSlide->createDrawingShape();
            $shape->setPath(public_path('img/'.$this->image[$platform['platform']]))
                  ->setOffsetX($startBoxX)->setOffsetY($startBoxY + 22)->setWidth(30)->setHeight(30);
            $shape->setHyperlink(new Hyperlink($platform['url']));

            $shape = $activeSlide->createRichTextShape();
            $shape->setOffsetX($startBoxX + $offsetX1)->setOffsetY($startBoxY + 10)->setWidth(105)->setHeight(20);
            $shape->setInsetLeft(0);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $textRun = $shape->createTextRun($this->formatNumber($platform['followers']));
            $textRun->getFont()->setBold(true)->setSize($this->primaryFont)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

            $shape = $activeSlide->createRichTextShape();
            $shape->setOffsetX($startBoxX + $offsetX1)->setOffsetY($startBoxY + 22)->setWidth(105)->setHeight(20);
            $shape->setInsetLeft(0);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $textRun = $shape->createTextRun($this->formatNumber('followers'));
            $textRun->getFont()->setBold(false)->setSize($this->smallFont)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

            $shape = $activeSlide->createRichTextShape();
            $shape->setOffsetX($startBoxX + $offsetX1)->setOffsetY($startBoxY + 40)->setWidth(105)->setHeight(20);
            $shape->setInsetLeft(0);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $textRun = $shape->createTextRun($this->formatNumber($platform['avg_engagement']));
            $textRun->getFont()->setBold(true)->setSize($this->primaryFont)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

            $shape = $activeSlide->createRichTextShape();
            $shape->setOffsetX($startBoxX + $offsetX1)->setOffsetY($startBoxY + 52)->setWidth(60)->setHeight(20);
            $shape->setInsetLeft(0);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $textRun = $shape->createTextRun($platform['platform'] == 'youtube' ? 'avg. views' : 'avg. eng.');
            $textRun->getFont()->setBold(false)->setSize($this->smallFont)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

            if ($platform['engagement'])
            {
                $shape = $activeSlide->createRichTextShape();
                $shape->setOffsetX($startBoxX + $offsetX2)->setOffsetY($startBoxY + 30)->setWidth(60)->setHeight(20);
                $shape->setInsetLeft(0);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $textRun = $shape->createTextRun($this->formatNumber($platform['engagement']));
                $textRun->getFont()->setBold(true)->setSize($this->primaryFont)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

                $shape = $activeSlide->createRichTextShape();
                $shape->setOffsetX($startBoxX + $offsetX2)->setOffsetY($startBoxY + 42)->setWidth(60)->setHeight(20);
                $shape->setInsetLeft(0);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $textRun = $shape->createTextRun('eng.');
                $textRun->getFont()->setBold(false)->setSize($this->smallFont)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));
            }

            $shape = $activeSlide->createRichTextShape();
            $shape->setOffsetX($startBoxX)->setOffsetY($startBoxY + 80)->setWidth(150)->setHeight(20);
            $shape->setInsetLeft(0);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $textRun = $shape->createTextRun($platform['platform'] == 'instagram' || $platform['platform'] == 'twitter' ? '@'.$platform['social_id'] : $platform['social_id']);
            $textRun->setHyperlink(new Hyperlink($platform['url']));
            $textRun->getFont()->setBold(false)->setSize($this->primaryFont)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));
        }
    }

    protected function add_gushcloud_header($activeSlide)
    {
        // GUSHCLOUD LOGO IMAGE
        $logo_width = 100;
        $shape = $activeSlide->createDrawingShape();

        $shape->setPath(public_path('img/logo-gushcloud.png'))
                ->setOffsetX($this->maxWidth-$logo_width-20)
                ->setOffsetY($this->segmentYPosition[0]+10)->setWidth($logo_width)->setHeight(20);

        // BLACK LINE
        $shape = $activeSlide->createLineShape($this->segmentXPosition[1], $this->segmentYPosition[0] + 40, $this->maxWidth-10, $this->segmentYPosition[0] + 40);
        $shape->getBorder()
            ->setLineStyle(Border::LINE_SINGLE)
            ->setDashStyle(Border::DASH_SOLID)
            ->setColor(new Color(Color::COLOR_BLACK));
    }

    protected function add_footer($activeSlide)
    {
        // LAST UPDATED DATE
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX(10)->setOffsetY($this->segmentYPosition[5])->setWidth(450)->setHeight(20);
        $shape->setInsetLeft(0);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $textRun = $shape->createTextRun('Last Updated: '.Carbon::now()->format('d M Y'));
        $textRun->getFont()->setBold(false)->setSize($this->smallFont)->setName('Helvetica')->setColor(new Color(Color::COLOR_BLACK));

        // PRIVATE & CONFIDENTIAL
        $shape = $activeSlide->createRichTextShape();
        $shape->setOffsetX(470)->setOffsetY($this->segmentYPosition[5])->setWidth(480)->setHeight(20);
        $shape->setInsetRight(0);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $textRun = $shape->createTextRun('Private & Confidential');
        $textRun->getFont()->setBold(true)->setSize($this->smallFont)->setName('Helvetica')->setColor(new Color('FF999999'));
    }

    private function formatNumber($number)
    {
        // 0 - 999
        if ($number < 1000) return $number;
        // 1,000 - 99,999
        if ($number >= 1000 && $number < 100000) return round($number/1000, 1).'K';
        // 100,000 - 999,999
        if ($number >= 100000 && $number < 1000000) return round($number/1000).'K';
        // 1,000,000 - 99,999,999
        if ($number >= 1000000 && $number < 100000000) return round($number/1000000, 1).'M';
        // 100,000,000 - 999,999,999
        if ($number >= 100000000 && $number < 1000000000) return round($number/1000000).'M';
        // 1,000,000,000
        if ($number >= 1000000000) return round($number/1000000000, 1).'B';
    }

    private function formatWeight($weight)
    {
        return number_format($weight * 100, 2).'%';
    }

    private function drawLines($activeSlide, $segmentYPosition, $segmentXPosition)
    {
        foreach ($segmentYPosition as $segmentY) {
            $shape = $activeSlide->createLineShape(0, $segmentY, $this->maxWidth, $segmentY);
            $shape->getBorder()
                ->setLineStyle(Border::LINE_SINGLE)
                ->setDashStyle(Border::DASH_SOLID)
                ->setColor(new Color(Color::COLOR_RED));
        }

        foreach ($segmentXPosition as $segmentX) {
            $shape = $activeSlide->createLineShape($segmentX, 0, $segmentX, $this->maxHeight);
            $shape->getBorder()
                ->setLineStyle(Border::LINE_SINGLE)
                ->setDashStyle(Border::DASH_SOLID)
                ->setColor(new Color(Color::COLOR_RED));
        }
    }
}
