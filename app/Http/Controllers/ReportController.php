<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Report;
use Carbon\Carbon;
use Response;
use Image;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if ( ! $request->url) abort(400);

        if (strpos($request->url, 'youtube.com') !== false)
        {
            $screenshot_url = config('services.screenshotlayer.capture_url').
                '?access_key='.config('services.screenshotlayer.access_key').
                '&url='.$request->url.'&css_url=https://gush.co/css/screenshot10.css&fullpage=1&viewport=1440x500';
            $image = Image::make($screenshot_url)->stream()->__toString();
            Storage::put('screenshots/instagram/test.jpeg', $image);
            $image_url = Storage::temporaryUrl('screenshots/instagram/test.jpeg', now()->addMinutes(5));
            $image = Image::make($image_url);
            $image = $image->crop(900, $image->height()-50, 0, 0)->stream()->__toString();
            Storage::put('screenshots/instagram/cropped/test.jpeg', $image);
            return Storage::temporaryUrl('screenshots/instagram/cropped/test.jpeg', now()->addMinutes(5));
        }

        if (strpos($request->url, 'facebook.com') !== false)
        {
            $screenshot_url = config('services.screenshotlayer.capture_url').
                '?access_key='.config('services.screenshotlayer.access_key').
                '&url='.$request->url.'&css_url=https://gush.co/css/screenshot10.css&fullpage=1&viewport=1440x500';
            $image = Image::make($screenshot_url)->stream()->__toString();
            Storage::put('screenshots/instagram/test.jpeg', $image);
            $image_url = Storage::temporaryUrl('screenshots/instagram/test.jpeg', now()->addMinutes(5));
            $image = Image::make($image_url);
            $image = $image->crop(718, $image->height()-202, 202, 0)->stream()->__toString();
            Storage::put('screenshots/instagram/cropped/test.jpeg', $image);
            return Storage::temporaryUrl('screenshots/instagram/cropped/test.jpeg', now()->addMinutes(5));
        }

        if (strpos($request->url, 'instagram.com') !== false)
        {
            $screenshot_url = config('services.screenshotlayer.capture_url').
                '?access_key='.config('services.screenshotlayer.access_key').
                '&url='.$request->url.'&css_url=https://gush.co/css/screenshot10.css&fullpage=1&viewport=800x400';
            $image = Image::make($screenshot_url)->stream()->__toString();
            Storage::put('screenshots/instagram/test.jpeg', $image);
            $image_url = Storage::temporaryUrl('screenshots/instagram/test.jpeg', now()->addMinutes(5));
            $image = Image::make($image_url);
            $image = Image::make($image_url)->crop(800, $image->height()-50, 0, 20)->stream()->__toString();
            Storage::put('screenshots/instagram/cropped/test.jpeg', $image);
            return Storage::temporaryUrl('screenshots/instagram/cropped/test.jpeg', now()->addMinutes(5));
        }
    }

    public function show($id)
    {
        $report = Report::findOrFail($id);
        $report->downloaded_at = Carbon::now();
        $report->save();

        $fileContentType = $this->getContentType($report->relative_file_url);
        return Response::make(file_get_contents($report->file_url), 200, [
            'Content-Type' => $fileContentType,
            'Content-Disposition' => 'inline; filename="'.$report->file.'"',
        ]);
    }

    private function getContentType($filename)
    {
        $file_parts = pathinfo($filename);
        switch ($file_parts['extension']) {
            case "ppt":
                return 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
            case "csv":
            case "xlsx":
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            default:
                return '';
        }
    }
}
