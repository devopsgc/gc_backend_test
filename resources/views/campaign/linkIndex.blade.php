@extends('layouts.app')

@section('style')
<style>
    /* .deliverables span {
        float: left;
        background: #eee;
        margin: 0 5px 5px 0;
        padding: 5px;
        border-radius: 3px;
    }

    .deliverables span img {
        width: 20px;
    }

    .instagram-media {
        margin: auto !important;
    } */
</style>
@endsection

@section('content')
<div class="container py-3">
    <h2 class="mb-3">Campaign #{{ $campaign->id }} Links</h2>
    @php $recordIds = array_unique($campaign->links()->pluck('record_id')->toArray()); @endphp
    @foreach ($recordIds as $recordId)
    <div class="card mb-2">
        <div class="card-body">
            <div class="d-flex flex-column align-items-center justify-content-center mb-2">
                <h3>{{ $campaign->links()->where('record_id', $recordId)->first()->record->name }} Deliverables</h3>
                <div>
                    @foreach (App\Models\Deliverable::PLATFORMS as $platform)
                    @foreach (App\Models\Deliverable::TYPES as $type)
                    @if ($count = $campaign->deliverables->where('record_id', $recordId)->where('platform',
                    $platform)->where('type', $type)->sum('quantity'))
                    <span>
                        {{ $count }} x {{ Html::image('img/icon-'.strtolower($platform).'.png', $platform, ['class' => 'icon-sm']) }}
                        {{ $type }}
                    </span>
                    @endif
                    @endforeach
                    @endforeach
                </div>
            </div>
            @php $campaignDeliverables = $campaign->deliverables->where('record_id', $recordId) @endphp
            <h4 class="my-2">Posts</h4>
            <div class="row">
                <div class="col-md-12">
                    @include('campaign.linkIndexLinkList', ['campaignDeliverables' => $campaignDeliverables])
                </div>
            </div>
            @php $campaignValueAddedDeliverables = $campaign->valueAddedPosts->where('record_id', $recordId); @endphp
            @if ($campaignValueAddedDeliverables->count() > 0)
            <h4 class="my-2">Value Added</h4>
            <div class="row">
                <div class="col-md-12">
                    @include('campaign.linkIndexLinkList', ['campaignDeliverables' => $campaignValueAddedDeliverables])
                </div>
            </div>
            @endif
        </div>
    </div>
    @endforeach
    <div class="row">
        <div class="col-md-12">
            {{ Html::link($campaign->getPath().'/links', 'Back to Campaign Links', ['class' => 'btn btn-primary']) }}
        </div>
    </div>
</div>
@endsection

@section('script')
<script async src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>
<script async src="//www.instagram.com/embed.js"></script>
<script async src="https://platform.twitter.com/widgets.js"></script>
@endsection