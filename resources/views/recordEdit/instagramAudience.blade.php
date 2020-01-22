<h2>
    <small>{{ $title }}</small>
    <button type="button" class="btn" data-toggle="tooltip" data-placement="top" title="{{ $titleTooltipDescription }}">
        <i class="fas fa-info"></i>
    </button>
</h2>
<div class="row">
    <div class="col-md-6">
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">General</h5>
            </div>
            <div class="card-body">
                @isset($audience['credibility_class'])
                @include('recordEdit.rowData', ['label' => 'Credibility Class', 'data' => $audience['credibility_class'].'
                ('.$audience['audience_credibility'].')' ])
                @endif
                @isset($audience['notable_users_ratio'])
                @include('recordEdit.rowData', ['label' => 'Notable User Ration', 'data' => $audience['notable_users_ratio'] ])
                @endif
            </div>
        </div>
        @isset($audience['audience_ages'])
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Audience Age</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($audience['audience_ages'] as $item)
                    <div class="col-4">{{ $item['code'] }}</div>
                    <div class="col-8">{{ round($item['weight'] * 100, 2) }}%</div>
                    @empty
                    No Stats.
                    @endforelse
                </div>
            </div>
        </div>
        @endif
        @isset($audience['audience_ethnicities'])
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Audience Ethnicities</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($audience['audience_ethnicities'] as $item)
                    <div class="col-4">{{ $item['name'] }}</div>
                    <div class="col-8">{{ round($item['weight'] * 100, 2) }}%</div>
                    @empty
                    No Stats.
                    @endforelse
                </div>
            </div>
        </div>
        @endif
        @isset($audience['audience_languages'])
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Audience Language</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php $languages = collect($audience['audience_languages']) @endphp
                    @forelse($languages->take($countToShow) as $item)
                    <div class="col-4">{{ isset($item['name']) ? $item['name'] : $item['code'] }}</div>
                    <div class="col-8">{{ round($item['weight'] * 100, 2) }}%</div>
                    @empty
                    No Stats.
                    @endforelse
                    @include('recordEdit.totalFound', ['items' => $languages])
                </div>
            </div>
        </div>
        @endif
        @isset($audience['audience_interests'])
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Audience Interest</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-1"><strong>Name</strong></div>
                    <div class="col-2 mb-1"><strong>Weight</strong></div>
                    <div class="col-4 mb-1"><strong>Affinity</strong></div>
                    @php $audienceInterests = collect($audience['audience_interests']) @endphp
                    @forelse($audienceInterests->take($countToShow) as $item)
                    <div class="col-6">{{ isset($item['name']) ? $item['name'] : $item['code'] }}</div>
                    <div class="col-2">{{ round($item['weight'] * 100, 2) }}%</div>
                    <div class="col-4">{{ $item['affinity'] }}</div>
                    @empty
                    No Stats.
                    @endforelse
                    @include('recordEdit.totalFound', ['items' => $audienceInterests])
                </div>
            </div>
        </div>
        @endif
        @isset($audience['audience_geo'])
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Audience Geographic</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($audience['audience_geo'] as $key => $geographics)
                    <div class="col-12 mb-1"><strong>{{ ucfirst($key) }}</strong></div>
                    @php $geographicCollection = collect($geographics)->take($countToShow) @endphp
                    @forelse($geographicCollection as $geographic)
                    <div class="col-6">{{ $geographic['name'] }}</div>
                    <div class="col-3">{{ round($geographic['weight'] * 100, 2) }}%</div>
                    @empty
                    No Stats.
                    @endforelse
                    @include('recordEdit.totalFound', ['items' => $geographicCollection])
                    <div class="col-12 mb-1"></div>
                    @empty
                    No Stats.
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="col-md-6">
        @isset($audience['audience_genders'])
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Audience Reachability</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($audience['audience_genders'] as $item)
                    <div class="col-4">{{ $item['code'] }}</div>
                    <div class="col-8">{{ round($item['weight'] * 100, 2) }}%</div>
                    @empty
                    No Stats.
                    @endforelse
                </div>
            </div>
        </div>
        @endif
        @isset($audience['audience_genders_per_age'])
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Audience Gender Per Age</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4 mb-1"><strong>Age</strong></div>
                    <div class="col-4 mb-1"><strong>Male</strong></div>
                    <div class="col-4 mb-1"><strong>Female</strong></div>
                    @forelse($audience['audience_genders_per_age'] as $item)
                    <div class="col-4">{{ $item['code'] }}</div>
                    <div class="col-4">{{ round($item['male'] * 100, 2) }}%</div>
                    <div class="col-4">{{ round($item['female'] * 100, 2) }}%</div>
                    @empty
                    No Stats.
                    @endforelse
                </div>
            </div>
        </div>
        @endif
        @isset($audience['audience_brand_affinity'])
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Audience Brand Affinity</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-1"><strong>Brand</strong></div>
                    <div class="col-3 mb-1"><strong>Weight</strong></div>
                    <div class="col-3 mb-1"><strong>Affinity</strong></div>
                    @php $audienceBrandAffinity = collect($audience['audience_brand_affinity']) @endphp
                    @forelse($audienceBrandAffinity->take($countToShow) as $item)
                    <div class="col-6">{{ isset($item['name']) ? $item['name'] : $item['code'] }}
                    </div>
                    <div class="col-3">{{ round($item['weight'] * 100, 2) }}%</div>
                    <div class="col-3">{{ $item['affinity'] }}</div>
                    @empty
                    No Stats.
                    @endforelse
                    @include('recordEdit.totalFound', ['items' => $audienceBrandAffinity])
                </div>
            </div>
        </div>
        @endif
        @isset($audience['notable_users'])
        <div class="card my-2">
            <div class="card-header">
                <h5 class="m-0">Notable Users</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4 mb-1"><strong>Username</strong></div>
                    <div class="col-4 mb-1"><strong>Followers</strong></div>
                    <div class="col-4 mb-1"><strong>Engagement</strong></div>
                    @php $notableUsers = collect($audience['notable_users']) @endphp
                    @forelse($notableUsers->take($countToShow) as $user)
                    <div class="col-4"><a href="{{ $user['url'] }}">{{ $user['username'] }}</a></div>
                    <div class="col-4">{{ $user['followers'] }}</div>
                    <div class="col-4">{{ $user['engagements'] }}</div>
                    @empty
                    No Stats.
                    @endforelse
                    @include('recordEdit.totalFound', ['items' => $notableUsers])
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
