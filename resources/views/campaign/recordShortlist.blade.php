<div class="table-responsive">
    <table id="campaignDeliverables" class="table mb-0" width="100%">
        <thead>
            <tr>
                <th class="text-center align-middle" width="20%">Influencer</th>
                <th class="text-center align-middle" width="30%">Social Media Stats</th>
                <th class="text-center align-middle" width="20%">Deliverables
                    {{ Form::select('currency_code',
                        $countries->mapWithKeys(function ($country) {
                            return [$country['currency_code'] => $country['currency_code']];
                        }),
                        old('currency_code', session('campaign.currency_code', 'SGD')),
                        ['id' => 'currency_code', 'class' => 'form-control']) }}
                </th>
                <th class="text-center align-middle" width="20%">Nett Cost</th>
                <th class="text-center align-middle">Action</th>
            </tr>
        </thead>
        @if ($records->count())
        <tbody>
            @foreach ($records as $record)
            {{ Form::hidden('selected[]', $record->id) }}
            <tr>
                <td>
                    <div class="d-flex">
                        <div class="mr-2">
                            @can('update', Auth::user(), App\Models\Record::class)
                            <a class="openRecordModal" href="{{ url('records/'.$record->id.'/edit') }}">
                                <img class="rounded-circle icon-lg" src="{{ $record->display_photo }}"
                                    alt="{{ $record->name }}'s Profile Photo" />
                            </a>
                            @else
                            <img class="rounded-circle icon-lg" src="{{ $record->display_photo }}" alt="{{ $record->name }}'s Profile Photo" />
                            @endcan
                        </div>
                        <div class="d-flex flex-column justify-content-center flex-fisll">
                            <div>
                                @can('update', Auth::user(), App\Models\Record::class)
                                <strong>{{ Html::link('records/'.$record->id.'/edit', $record->name, ['class' => 'openRecordModal']) }}</strong>
                                @else
                                <strong>{{ $record->name }}</strong>
                                @endcan
                                <?php
                                    $missingFields = $record->getRequiredFieldsForCampaignCreate();
                                    $missingFieldsText = '';
                                    foreach ($missingFields as $key => $message) {
                                        if ($message == 'deliverables')
                                            $missingFieldsText .= '('.($key+1).') Missing '.$message.'<br />';
                                        else
                                            $missingFieldsText .= '('.($key+1).') Missing profile '.$message.'<br />';
                                    }
                                ?>
                                @if ($missingFieldsText)
                                <span class="ml-2 my-3" data-html="true" data-toggle="tooltip" title="{{ $missingFieldsText }}">
                                    <i class="fas fa-exclamation" style="color:red;"></i>
                                </span>
                                @endif
                            </div>
                            @if ($record->second_name)
                            <small>{{ $record->second_name }}</small>
                            @endif
                            @if ($record->interestsDisplay)
                            <small>{{ $record->interestsDisplay }}</small>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-between">
                        <div class="d-flex flex-column align-items-center">
                            {{ Html::image('img/icon-facebook.png', 'Facebook', ['class' => 'icon-sm mb-1']) }}
                            followers
                            <strong>{{ $record->facebook_followers ? number_format($record->facebook_followers) : 'N/A' }}</strong>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            {{ Html::image('img/icon-instagram.png', 'Instagram', ['class' => 'icon-sm mb-1']) }}
                            followers
                            <strong>{{ $record->instagram_followers ? number_format($record->instagram_followers) : 'N/A' }}</strong>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            {{ Html::image('img/icon-twitter.png', 'Twitter', ['class' => 'icon-sm mb-1']) }}
                            followers
                            <strong>{{ $record->twitter_followers ? number_format($record->twitter_followers) : 'N/A' }}</strong>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            {{ Html::image('img/icon-youtube.png', 'YouTube', ['class' => 'icon-sm mb-1']) }}
                            subscribers
                            <strong>{{ $record->youtube_subscribers ? number_format($record->youtube_subscribers) : 'N/A' }}</strong>
                        </div>
                    </div>
                    <div class="d-flex bg-light justify-content-between mt-2">
                        <div class="mx-2">
                            <small>
                                <span data-toggle="tooltip" data-placement="top" title="FB Followers + IG Followers + TW Followers + YT Subscribers">
                                    Total Followers
                                </span>
                                <strong>{{ number_format($record->total_followers) }}</strong>
                            </small>
                        </div>
                        <div class="mx-2">
                            <small>
                                <span data-toggle="tooltip" data-placement="top" title="FB & IG & TW Post Engagement/Followers">
                                    Post ER
                                </span>:
                                <strong>
                                    @if (!is_null($record->post_engagement_rate))
                                    {{ $record->post_engagement_rate }}%
                                    @else
                                    <span data-toggle="tooltip" data-placement="top" title="No FB & IG & TW Followers">N/A</span>
                                    @endif
                                </strong>
                            </small>
                        </div>
                        <div class="mx-2">
                            <small>
                                <span data-toggle="tooltip" data-placement="top" title="FB, IG & YT Video Engagement/Followers">
                                    Video ER
                                </span>:
                                <strong>
                                    @if (!is_null($record->video_engagement_rate))
                                    {{ $record->video_engagement_rate }}%
                                    @else
                                    <span data-toggle="tooltip" data-placement="top" title="No FB, IG & YT Followers/Subscribers">
                                        N/A
                                    </span>
                                    @endif
                                </strong>
                            </small>
                        </div>
                    </div>
                </td>
                <td>
                    @if (session()->has('campaign.selected.'.$record->id.'.deliverables'))
                    <div>
                        <div class="d-flex flex-column">
                            @foreach (session('campaign.selected.'.$record->id.'.deliverables') as $deliverable)
                            <div class="d-flex justify-content-around my-1">
                                <span>
                                    {{ $deliverable['quantity'].' x ' }}
                                    {{ Html::image('img/icon-'.strtolower($deliverable['platform']).'.png', $deliverable['platform'], ['class' => 'icon-sm']) }}
                                    {{ $deliverable['type'] }}
                                </span>
                                <div>{{ $deliverable['price'] ? number_format($deliverable['price']) : '' }}</div>
                            </div>
                            @endforeach
                            @if (count(session('campaign.selected.'.$record->id.'.deliverables')) > 0)
                            <div class="d-flex justify-content-around border-top pt-2">
                                <div class="mx-3">
                                    Total
                                </div>
                                <?php $deliverablePriceTotal = number_format(array_sum(array_column(session('campaign.selected.'.$record->id.'.deliverables'), 'price'))); ?>
                                <strong class="deliverable_total_price">{{ $deliverablePriceTotal ? $deliverablePriceTotal : '' }}</strong>
                            </div>
                            @endif
                            <div class="d-flex justify-content-around pt-2">
                                <a class="openDeliverableModal btn btn-link btn-sm mx-5" href="{{ url('campaigns/'.$record->id.'/deliverables') }}">
                                    Add/Edit
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <div>
                        <div class="d-flex justify-content-center">
                            <a class="openDeliverableModal btn btn-link btn-sm" href="{{ url('campaigns/'.$record->id.'/deliverables') }}">
                                Add/Edit
                            </a>
                        </div>
                    </div>
                    @endif
                </td>
                <td class="text-center">
                    {{ Form::number('package_price['.$record->id.']', old('package_price[0]',
                        session()->has('campaign.selected.'.$record->id.'.package_price') ?
                        session('campaign.selected.'.$record->id.'.package_price') :
                        (session()->has('campaign.selected.'.$record->id.'.deliverables') ?
                        array_sum(array_column(session('campaign.selected.'.$record->id.'.deliverables'), 'price')) : 0)),
                        ['autocomplete' => 'off', 'required' => true, 'min' => 0, 'class' => 'package_price form-control text-center']) }}
                    <div class="mt-3">
                        Discount: <strong><span class="discount">0.00%</span></strong>
                    </div>
                </td>
                <td class="text-center">
                    <button class="btn btn-danger btn-sm remove-selection" data-record-id="{{ $record->id }}">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-center">
                    Grand Total
                </th>
                <th class="text-center">Total Followers: {{ number_format($records->sum('total_followers')) }}</th>
                <?php
                    $deliverablePriceGrandTotal = session('campaign.selected') ?
                        number_format(array_reduce(session('campaign.selected'), function ($total_price, $record) {
                        return isset($record['deliverables']) ? array_sum(array_column($record['deliverables'], 'price')) + $total_price : $total_price;
                        }, 0)) : 0;
                ?>
                <th class="text-center">
                    {{ $deliverablePriceGrandTotal ? 'Price: ' : '' }}
                    <span class="grand_total_price">{{ $deliverablePriceGrandTotal ? $deliverablePriceGrandTotal : '' }}</span>
                </th>
                <th class="text-center">
                    Total Nett Cost: $<span class="total_package_price"></span>
                    <br />
                    Discount: <span class="total_discount">0.00%</span>
                </th>
                <th class="text-center">
                    <span class="btn btn-danger btn-sm" data-toggle="modal" data-target="#recordClearModal">
                        <i class="far fa-trash-alt"></i>
                        <span class="ml-2">Clear All</span>
                    </span>
                </th>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><strong>Grand Total Package Price</strong></td>
                <td class="text-center>
                    {{ Form::number('total_price', old('total_price', session()->has('campaign.total_price') ?
                        session('campaign.total_price') : 0), ['autocomplete' => 'off', 'required' => true, 'min' => 0,
                        'class' => 'total_price form-control text-center'.($errors->has('total_price') ? ' is-invalid' : '')]) }}
                    @error('total_price')<span class="invalid-feedback d-block">{{ $errors->first('total_price') }}</span>@enderror
                </td>
                <td>
                </td>
            </tr>
        </tfoot>
        @else
        <tbody>
            <tr>
                <td colspan="5">
                    <div class="text-center">
                        No influencers selected.
                    </div>
                </td>
            </tr>
        </tbody>
        @endif
    </table>
</div>
@if ($records->count())
<div class="w-100 text-right my-3">
    @if (isset($campaign) && ! $campaign->isDraft())
    <button type="submit" value="{{ session('campaign.campaign_id') }}" class="btn btn-primary">
        Update Campaign
    </button>
    @else
    <input type="hidden" name='with_download' value='1' />
    @isset($campaign)
    <button type="submit" class="btn btn-primary">Save And Download</button>
    @else
    <input id="saveAndCreateProposalName" type="hidden" name='name' value='' />
    <span class="btn btn-primary" data-toggle="modal" data-target="#saveAndCreateProposalNameModal">Save And Download</span>
    @endisset
    @endif

    @if (isset($campaign) && $campaign->isDraft())
    <a class="btn btn-primary" href="{{ url('/campaigns/'.$campaign->id.'/edit') }}">
        Create Campaign
    </a>
    @endif
</div>
@endif
