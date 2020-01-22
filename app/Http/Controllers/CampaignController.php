<?php

namespace App\Http\Controllers;

use App\Helpers\GenerateDeckPptHelper;
use App\Models\Campaign;
use App\Models\Country;
use App\Models\Language;
use App\Models\Tag;
use App\Traits\CampaignSessionHelper;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    use CampaignSessionHelper;

    public function index(Request $request)
    {
        $this->authorize('index', Campaign::class);
        $this->data['title'] = 'Campaigns';

        $query = Campaign::query();
        $this->buildCampaignlistingQuery($request, $query);
        $this->data['campaigns'] = $query->orderBy('created_at', 'desc')->with(['deliverables', 'createdBy'])->paginate(20);

        $this->data['interests'] = Tag::getAllInterests();
        $this->data['campaigns_countries'] = $this->getAllCampaignsUniqueCountriesArray($query);

        return view('campaign.index', $this->data);
    }

    protected function buildCampaignlistingQuery($request, $query)
    {
        $query = Campaign::addQueryForUserRestriction($query);

        switch ($request->filter_tab) {
            case 'campaigns':
                $query->whereIn('status', [Campaign::STATUS_ACCEPTED, Campaign::STATUS_REJECTED, Campaign::STATUS_CANCELLED]);
                break;
            case 'completed':
                $query->where('status', Campaign::STATUS_COMPLETED);
                break;
            default:
                $query->where('status', Campaign::STATUS_DRAFT);
                break;
        }
        switch ($request->filter) {
            case 'me':
            $query->where('created_by_user_id', auth()->user()->id);
            break;
            default:
            break;
        }
        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->q . '%');
                $query->orWhere('brand', 'LIKE', '%' . $request->q . '%');
            });
        }
        if ($request->status) {
            $query->where('status', '=', $request->status);
        }
        if ($request->country_code) {
            $query->where('country_code', '=', $request->country_code);
        }
        if ($request->category) {
            $categories = explode('|', $request->category);
            $query->where(function ($query) use ($categories) {
                foreach ($categories as $category) {
                    $query->orWhere('categories', 'LIKE', '%' . $category . '%');
                }
            });
        }
        if ($request->start_at || $request->end_at) {
            $query->where(function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('start_at', '>=', $request->start_at)
                        ->where('start_at', '<=', $request->end_at);
                })->orWhere(function ($query) use ($request) {
                    $query->where('end_at', '>=', $request->start_at)
                        ->where('end_at', '<=', $request->end_at);
                });
            });
        }
        return $query;
    }

    public function show(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        if ($campaign->isDraft()) {
            abort(404);
        }

        $this->data['campaign'] = $campaign;
        $this->data['title'] = '[Campaign #' . $campaign->id . '] ' . $campaign->name;
        $this->data['languages'] = Language::get();

        return view('campaign.show', $this->data);
    }

    public function edit(Campaign $campaign)
    {
        if (!$campaign->canEdit()) {
            abort(404);
        }
        $this->authorize('update', $campaign);
        $this->data['campaign'] = $campaign;
        $this->data['title'] = '[Edit Campaign #' . $campaign->id . '] ' . $campaign->name;
        $this->data['interests'] = Tag::getAllInterests();
        $this->data['countries'] = Country::getAllEnabledCountries();

        return view('campaign.edit', $this->data);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        if (!$campaign->canEdit()) {
            abort(404);
        }

        $this->validateCampaign($request);

        $campaign->fill($this->getCampaignAttributes($request));

        if ($request->status === Campaign::STATUS_ACCEPTED) {
            $campaign->status = Campaign::STATUS_ACCEPTED;
        }

        $campaign->save();

        return redirect($campaign->getPath())->with('message', 'The data has been saved.');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Campaign::class);

        if ($failedValidator = Campaign::validateCampaignFails($request)) {
            return redirect('campaigns/shortlist')
                ->withInput()
                ->withErrors($failedValidator);
        }

        $this->updateSessionDeliverables($request);

        session('selected');
        $deliverables = session('campaign.selected');
        $currency_code = session('campaign.currency_code', 'SGD');
        $total_price = session('campaign.total_price', 0);

        $defaultCountry = auth()->user()->countries()->first() ? auth()->user()->countries()->first()->iso_3166_2 : 'SG';

        $campaign = Campaign::create([
            'country_code' => $defaultCountry,
            'name' => 'draft',
            'created_by_user_id' => Auth::user()->id,
            'status' => Campaign::STATUS_DRAFT,
        ]);

        if ($request->name) {
            $campaign->update(['name' => $request->name]);
        } else {
            $campaign->update(['name' => 'Proposal ' . $campaign->id]);
        }

        $this->saveCampaignDeliverables($campaign, $deliverables, $currency_code, $total_price);

        GenerateDeckPptHelper::createCampaignDeckReport($campaign->records->pluck('id')->toArray(), $campaign);
        GenerateDeckPptHelper::createRecordsExcel($campaign->records->pluck('id')->toArray(), $campaign->id);

        return redirect(url('/campaigns'))
            ->with(
                'message',
                'Proposal ' . $campaign->id . ' created. <br />' .
                '<strong>Generating reports.</strong> An email will be send to you once your reports are ready to be downloaded.'
            );
    }

    protected function validateCampaign($request)
    {
        $this->validate($request, [
            'country_code' => 'required|max:2',
            'currency_code' => 'required',
            'name' => 'required|string',
            'brand' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'interests' => 'nullable|string',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date_format:"d F Y"',
            'end_at' => 'nullable|date_format:"d F Y"|after_or_equal:start_at',
        ]);
    }

    protected function getCampaignAttributes($request)
    {
        return [
            'country_code' => $request->country_code,
            'currency_code' => $request->currency_code,
            'name' => $request->name,
            'brand' => $request->brand ?: null,
            'budget' => $request->budget ?: null,
            'categories' => $request->interests ?: null,
            'description' => $request->description ?: null,
            'start_at' => $request->start_at ? Carbon::createFromFormat('d F Y', $request->start_at)->startOfDay() : null,
            'end_at' => $request->end_at ? Carbon::createFromFormat('d F Y', $request->end_at)->startOfDay() : null,
        ];
    }

    protected function getAllCampaignsUniqueCountriesArray($query)
    {
        return $query
            ->distinct()
            ->get()
            ->mapWithKeys(
                function ($campaign) {
                    return [$campaign->country_code => $campaign->country_code];
                }
            )->toArray();
    }
}
