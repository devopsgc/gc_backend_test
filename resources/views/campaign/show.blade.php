@extends('layouts.app')

@section('style')
<style>
    /* .deliverables span { float:left; background:#eee; margin:0 5px 5px 0; padding:5px; border-radius:3px; }
.deliverables span img { width:20px; } */
    .custom-label {
        font-weight: 700;
    }

    .custom-row {
        margin-top: 8px;
        margin-bottom: 8px;
    }
</style>
@endsection

@section('content')
<div class="container py-3">
    @include('components.messageAlert')
    @include('components.warningAlert')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>{{ $campaign->name }}</h4>
                        <div class="d-flex justify-content-md-end align-items-center flex-grow-1">
                            @include('campaign.statusBadge', ['campaign' => $campaign])
                            @if ($campaign->canEdit())
                            {{ Html::link($campaign->getPath().'/edit', 'Edit Campaign', ['class' => 'text-nowrap btn btn-outline-secondary ml-1']) }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row custom-row">
                        <div class="col-sm-4 custom-label">Country</div>
                        <div class="col-sm-8">{{ $campaign->country->name ?? '-' }}</div>
                    </div>
                    <div class="row custom-row">
                        <div class="col-sm-4 custom-label">Brand</div>
                        <div class="col-sm-8">{{ $campaign->brand ?? '-' }}</div>
                    </div>
                    <div class="row custom-row">
                        <div class="col-sm-4 custom-label">Budget ({{ $campaign->currency_code ?: '$' }})</div>
                        <div class="col-sm-8">{{ $campaign->budget ? number_format($campaign->budget) : '-' }}</div>
                    </div>
                    <div class="row custom-row">
                        <div class="col-sm-4 custom-label">Categories</div>
                        <div class="col-sm-8">{{ $campaign->categories ? implode(' | ', explode('|', $campaign->categories)) : '-' }}</div>
                    </div>
                    <div class="row custom-row">
                        <div class="col-sm-4 custom-label">Description</div>
                        <div class="col-sm-8">{!! $campaign->description ? nl2br(htmlspecialchars($campaign->description)) : '-' !!}</div>
                    </div>
                    <div class="row custom-row">
                        <div class="col-sm-4 custom-label">Start Date</div>
                        <div class="col-sm-8">{{ $campaign->start_at ? $campaign->start_at->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="row custom-row">
                        <div class="col-sm-4 custom-label">End Date</div>
                        <div class="col-sm-8">{{ $campaign->end_at ? $campaign->end_at->format('d M Y') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="m-0">Deliverables</h4>
                        @if (config('featureToggle.campaignReport'))
                        {{ Html::link($campaign->getPath().'/links', 'Report Analysis', ['class' => 'btn btn-primary'.($campaign->canAccessLinks() ? '' : ' disabled')]) }}
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @foreach (App\Models\Deliverable::PLATFORMS as $platform)
                    @foreach (App\Models\Deliverable::TYPES as $type)
                    @include('campaign.deliverable', ['platform' => $platform, 'type' => $type])
                    @endforeach
                    @endforeach
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="m-0">Influencers</h4>
                        @if ($campaign->canEdit())
                        {{ Html::link($campaign->getPath().'/shortlist', 'Edit Shortlist', ['class' => 'btn btn-outline-secondary']) }}
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <p>
                        Total Followers: {{ number_format($campaign->total_influencer_followers) }}<br />
                        Post Engagement Rate: {{ number_format($campaign->influencer_post_engagement_rate, 2) }}%<br />
                        Video Engagement Rate: {{ number_format($campaign->influencer_video_engagement_rate, 2) }}%
                    </p>
                    <div style="max-height:300px; overflow:auto;">
                        <table class="table">
                            <tbody>
                                @foreach ($campaign->records as $record)
                                <tr>
                                    <td width="15%">
                                        @can('update', Auth::user(), App\Models\Record::class)
                                        <a class="openRecordModal" href="{{ url('records/'.$record->id.'/edit') }}">
                                            <img width="100%" src="{{ $record->display_photo }}" alt="" />
                                        </a>
                                        @else
                                        <img width="100%" src="{{ $record->display_photo }}" alt="" />
                                        @endcan
                                    </td>
                                    <td>
                                        <strong>{{ $record->name }}</strong>
                                        <small>
                                            {{ $record->second_name ? '('.$record->second_name.')' : '' }}
                                        </small>
                                        <br />
                                        @if ($record->country)
                                        {{ Html::image('flags/'.$record->country->iso_3166_2.'.png', $record->country->name, ['style' => 'margin-right:5px; margin-bottom:5px; width:20px;']) }}
                                        {{ $record->country->name }}
                                        @endif
                                        @if ($record->gender)
                                        | {{ $record->gender }}
                                        @endif
                                        @if (isset($record->date_of_birth))
                                        | {{ $record->date_of_birth->format('d M Y') }},
                                        {{ $record->date_of_birth->diffInYears(Carbon\Carbon::now()) }} yrs old
                                        @endif
                                        <br />
                                        @if ($record->professionsCore->count() > 0)
                                        Professions:
                                        {{ implode(', ', $record->professionsCore->pluck('name')->toArray()) }}<br />
                                        @endif
                                        @if ($record->interestsCore->count() > 0)
                                        Interests:
                                        {{ implode(', ', $record->interestsCore->pluck('name')->toArray()) }}<br />
                                        @endif
                                        @if ($record->affiliations)
                                        Affiliations: {{ $record->affiliations }}<br />
                                        @endif
                                    </td>
                                    <td width="15%" style="text-align:center;">
                                        <?php $affiliations = array_map(function($affiliation) {
                                                return strtolower($affiliation);
                                            }, explode(', ', $record->affiliations)); ?>
                                        @if (array_search('atelier', $affiliations) !== false)
                                        <p><img src="{{ url('img/Atelier-logo.png') }}" width="100%" /></p>
                                        @endif
                                        @if (array_search('gushcloud', $affiliations) !== false)
                                        <p><img src="{{ url('img/logo-gushcloud.png') }}" width="100%" /></p>
                                        @endif
                                        @if (array_search('gcs', $affiliations) !== false)
                                        <p><img src="{{ url('img/logo-gcs.png') }}" width="100%" /></p>
                                        @endif
                                        @if (array_search('gcx', $affiliations) !== false)
                                        <p><img src="{{ url('img/logo-gcx.png') }}" width="100%" /></p>
                                        @endif
                                        @if (array_search('gta', $affiliations) !== false)
                                        <p><img src="{{ url('img/logo-gta.png') }}" width="100%" /></p>
                                        @endif
                                        @if (array_search('made', $affiliations) !== false)
                                        <p><img src="{{ url('img/logo-made.png') }}" width="100%" /></p>
                                        @endif
                                        @if (array_search('ribbit', $affiliations) !== false)
                                        <p><img src="{{ url('img/logo-ribbit.png') }}" width="100%" /></p>
                                        @endif
                                        @if (array_search('summer', $affiliations) !== false)
                                        <p><img src="{{ url('img/logo-summer.png') }}" width="100%" /></p>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 offset-lg-6">
            {{ Form::open(['url' => $campaign->getDownloadPptPath(), 'method' => 'post']) }}
            <div class="d-flex align-items-center my-3">
                {{ Form::select('net_costing',
                    ['yes' => 'With Net Costing', 'no' => 'No Net Costing'],
                    old('net_costing', 'yes'),
                    ['class' => 'form-control']) }}
                {{ Form::select('language',
                    $languages->mapWithKeys(function ($language) {
                        return [$language['iso_3166_2'] ? $language['iso_639_1'].'_'.$language['iso_3166_2'] : $language['iso_639_1'] => $language['name']];
                    }),
                    old('language', 'en'),
                    ['class' => 'form-control ml-2']) }}
                <button type="submit" name="submit" value="pptx" class="btn btn-primary ml-2">
                    <div class="d-flex">
                        <i class="fa fa-download"></i>
                        <div class="text-nowrap ml-2">Download PowerPoint</div>
                    </div>
                </button>
            </div>
            {{ Form::close() }}
            @can('download_excel', App\Models\Record::class)
            {{ Form::open(['url' => $campaign->getDownloadExcelPath(), 'method' => 'post']) }}
            <div class="d-flex align-items-center justify-content-end my-3">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fa fa-download" style="margin-right:5px;"></i>
                    Download Excel
                </button>
            </div>
            {{ Form::close() }}
            @endcan
        </div>
    </div>
</div>
@include('campaign.deleteModal', ['campaignId' => $campaign->id])
@include('components.recordModal')
@endsection

@section('script')
@include('components.recordModalScript')
<script>
    function updateStatus(campaignId, status) {
        axios.post('{{ url('campaigns') }}/'+campaignId+'/status', {status: status}).then(function(response) {
            if (response.status === 200) {
                location.reload();
            }
        });
    }
</script>
@endsection
