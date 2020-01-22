@if ($campaigns->total() > 0)
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th width="5%">Country</th>
                <th width="20%">Campaign</th>
                <th width="30%">Influencers</th>
                <th width="30%">Deliverables</th>
                <th width="15%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($campaigns as $campaign)
            <tr>
                <td>{{ $campaign->country_code }}</td>
                <td>
                    <strong>
                        {{ Html::link($campaign->getPath(), $campaign->name.' / '.($campaign->brand ?? '-')) }}
                    </strong>
                    <br />
                    {{ $campaign->categories }}<br />
                    @if ($campaign->start_at && $campaign->end_at)
                    {{ $campaign->start_at->format('d M Y') }} - {{ $campaign->end_at->format('d M Y') }}<br />
                    @endif
                    <small class="text-muted">{{ 'Created by: ' . (isset($campaign->createdBy) ? $campaign->createdBy->full_name : '-') }}</small>
                </td>
                <td>
                    <p>
                        Total Followers: {{ number_format($campaign->total_influencer_followers) }}<br />
                        Post Engagement Rate: {{ number_format($campaign->influencer_post_engagement_rate, 2) }}%<br />
                        Video Engagement Rate: {{ number_format($campaign->influencer_video_engagement_rate, 2) }}%
                    </p>
                </td>
                <td>
                    <div class="d-flex flex-wrap">
                        @foreach (App\Models\Deliverable::PLATFORMS as $platform)
                        @foreach (App\Models\Deliverable::TYPES as $type)
                        @include('campaign.deliverable', ['platform' => $platform, 'type' => $type])
                        @endforeach
                        @endforeach
                    </div>
                </td>
                <td>@include('campaign.statusBadge', ['campaign' => $campaign])</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    @include('campaign.index.notFound')
</div>
@endif
