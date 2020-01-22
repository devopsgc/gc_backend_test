<hr>
@php $countToShow = 10; @endphp
<h2>Instagram Data</h2>
<p>
    <small>
        {{ '(last updated from social data' }}
        {{ (new Carbon\Carbon($socialData['report_info']['created']))->timezone('Asia/Singapore')->format('d M Y') }}
        {{ ')' }}
    </small>
</p>
<div class="row">
    <div class="col-md-6">
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">General Profile</h5>
            </div>
            <div class="card-body">
                @include('recordEdit.rowData', ['label' => 'Full Name', 'data' => $socialData['user_profile']['fullname'] ])
                @include('recordEdit.rowData', ['label' => 'Post Count', 'data' => $socialData['user_profile']['posts_count'] ])
                @include('recordEdit.rowData', ['label' => 'Engagements', 'data' => $socialData['user_profile']['engagements'] ])
                @include('recordEdit.rowData', ['label' => 'Engagements Rate', 'data' => $socialData['user_profile']['engagement_rate'] ])
                @include('recordEdit.rowData', ['label' => 'Average Likes', 'data' => $socialData['user_profile']['avg_likes'] ])
                @include('recordEdit.rowData', ['label' => 'Average Comments', 'data' => $socialData['user_profile']['avg_comments'] ])
                @include('recordEdit.rowData', ['label' => 'Average Views', 'data' => $socialData['user_profile']['avg_views'] ])
            </div>
        </div>
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Location</h5>
            </div>
            <div class="card-body">
                @forelse($socialData['user_profile']['geo'] as $key => $contact)
                @include('recordEdit.rowData', ['label' => ucfirst($key), 'data' => $contact['name'] ])
                @empty
                No Stats.
                @endforelse
            </div>
        </div>
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Contact</h5>
            </div>
            <div class="card-body">
                @forelse($socialData['user_profile']['contacts'] as $key => $contact)
                @include('recordEdit.contactRowData', ['label' => $contact['type'], 'data' => $contact['value'] ])
                @empty
                No Stats.
                @endforelse
            </div>
        </div>
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Top Hashtags</h5>
            </div>
            <div class="card-body">
                @forelse($socialData['user_profile']['top_hashtags'] as $top_hashtag)
                <span class="badge badge-primary">{{ $top_hashtag['tag'] }}</span>
                @empty
                No Stats.
                @endforelse
            </div>
        </div>
        @include('recordEdit.tagsPanel', [
        'title' => 'Top Mentions',
        'data' => $socialData['user_profile']['top_mentions'],
        'key' => 'tag',
        'labelType' => 'info' ])
        @include('recordEdit.tagsPanel', [
        'title' => 'Brand Affinity',
        'data' => $socialData['user_profile']['brand_affinity'],
        'key' => 'name',
        'labelType' => 'secondary' ])
        @include('recordEdit.tagsPanel', [
        'title' => 'Interests',
        'data' => $socialData['user_profile']['interests'],
        'key' => 'name',
        'labelType' => 'success' ])
        @include('recordEdit.tagsPanel', [
        'title' => 'Relavant Tag',
        'data' => $socialData['user_profile']['relevant_tags'],
        'key' => 'tag',
        'labelType' => 'warning' ])
    </div>
    <div class="col-md-6">
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Similar Users</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php $similarUsers = collect($socialData['user_profile']['similar_users']) @endphp
                    <div class="col-4 mb-1"><strong>Username</strong></div>
                    <div class="col-4 mb-1"><strong>Followers</strong></div>
                    <div class="col-4 mb-1"><strong>Engagement</strong></div>
                    @forelse($similarUsers->take($countToShow) as $simliar_user)
                    <div class="col-4"><a href="{{ $simliar_user['url'] }}">{{ $simliar_user['username'] }}</a></div>
                    <div class="col-4">{{ $simliar_user['followers'] }}</div>
                    <div class="col-4">{{ $simliar_user['engagements'] }}</div>
                    @empty
                    <div class="col-12 mb-1">No Stats.</div>
                    @endforelse
                    @include('recordEdit.totalFound', ['items' => $similarUsers])
                </div class="row">
            </div>
        </div>
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Stats History</h5>
            </div>
            <div class="card-body">
                @forelse($socialData['user_profile']['stat_history'] as $key => $stat)
                @include('recordEdit.rowData', ['label' => 'Month', 'data' => $socialData['user_profile']['stat_history'][$key]['month'] ])
                @include('recordEdit.rowData', ['label' => 'Followers', 'data' => $socialData['user_profile']['stat_history'][$key]['followers'] ])
                @include('recordEdit.rowData', ['label' => 'Following', 'data' => $socialData['user_profile']['stat_history'][$key]['following'] ])
                @include('recordEdit.rowData', ['label' => 'Average Likes', 'data' => $socialData['user_profile']['stat_history'][$key]['avg_likes'] ])
                <hr class="mb-1">
                @empty
                No Stats.
                @endforelse
            </div>
        </div>
    </div>
</div>

@if(isset($socialData['audience_likers']) && $socialData['audience_likers']['success'] == true)
@include('recordEdit.instagramAudience', [
'audience'=> $socialData['audience_likers']['data'],
'title' => 'Audience Likers',
'titleTooltipDescription' => 'Demographics of audience who liked the influencers'
])
@endif

@if(isset($socialData['audience_followers']) && $socialData['audience_followers']['success'] == true)
@include('recordEdit.instagramAudience', [
'audience' => $socialData['audience_followers']['data'],
'title' => 'Audience Followers',
'titleTooltipDescription' => 'Demographics of audience who followed the influencers'
])
@endif