@forelse ($records as $record)
<div class="row pb-2 mb-3 border-bottom">
    <div class="col-3 col-sm-2">
        @can('update', $record, App\Models\Record::class)
        <a class="openRecordModal" href="{{ url('records/'.$record->id.'/edit') }}">
            <img class="img-fluid" src="{{ $record->display_photo }}" alt="" />
        </a>
        @else
        <img class="img-fluid" src="{{ $record->display_photo }}" alt="" />
        @endcan
    </div>
    <div class="col-9 col-sm-8">
        @can('update', $record, App\Models\Record::class)
        <strong>{{ Html::link('records/'.$record->id.'/edit', $record->name, ['class' => 'openRecordModal']) }}</strong>
        @else
        <strong>{{ $record->name }}</strong>
        @endcan
        <small> {{ $record->second_name ? '('.$record->second_name.')' : '' }}</small><br />
        @if ($record->country)
        {{ Html::image('flags/'.$record->country->iso_3166_2.'.png', $record->country->name, ['class' => 'mr-1 icon-sm']) }}
        {{ $record->country->name }}
        @endif
        @if ($record->gender)
        &sdot; {{ $record->gender }}
        @endif
        @if (isset($record->date_of_birth))
        &sdot; {{ $record->date_of_birth->format('d M Y') }}, {{ $record->date_of_birth->diffInYears(Carbon\Carbon::now()) }} yrs old
        @endif
        <br />
        Email: {{ $record->email ? $record->email : '-' }}
        <br />
        @if ($record->country)
        Contact No: {{ '(+'.$record->country->calling_code.') '.($record->phone ? $record->phone : '-') }}
        <br />
        @endif
        @if ($record->professionsCore->count() > 0)
        Professions: {{ $record->professionsCore->pluck('name')->implode(', ') }}<br />
        @endif
        @if ($record->interestsCore->count() > 0)
        Interests: {{ $record->interestsCore->pluck('name')->implode(', ') }}<br />
        @endif
        @if ($record->campaigns)
        Campaigns: {{ $record->campaigns }}<br />
        @endif
        @if ($record->affiliationTags->count() > 0)
        Affiliations: {{ $record->affiliationTags->pluck('name')->implode(', ') }}<br />
        @endif
        @if ($record->instagram_id)
        <img src="{{ url('img/icon-instagram.png') }}" alt="instagram" class="icon-sm" />
        @if ($record->instagram_update_disabled_at)
        <span class="badge badge-danger">Error</span><br />
        @elseif ($record->instagram_update_succeeded_at)
        @if ($record->instagram_followers)
        <span title="Last updated on {{ $record->instagram_update_succeeded_at->format('d M Y') }}">
            <strong>{{ number_format($record->instagram_followers) }}</strong>
            PER: {{ number_format($record->instagram_engagement_rate_post) }}
            ({{ number_format($record->instagram_engagement_rate_post/$record->instagram_followers*100, 2) }}%)
            VER: {{ number_format($record->instagram_engagement_rate_video) }}
            ({{ number_format($record->instagram_engagement_rate_video/$record->instagram_followers*100, 2) }}%)<br />
        </span>
        @else
        <span class="badge badge-default">No Followers</span><br />
        @endif
        @else
        <span class="badge badge-warning">Processing</span><br />
        @endif
        @endif
        @if ($record->youtube_id)
        <img src="{{ url('img/icon-youtube.png') }}" alt="youtube" class="icon-sm" />
        @if ($record->youtube_update_disabled_at)
        <span class="badge badge-danger">Error</span><br />
        @elseif ($record->youtube_update_succeeded_at)
        @if ($record->youtube_subscribers)
        <span title="Last updated on {{ $record->youtube_update_succeeded_at->format('d M Y') }}">
            <strong>{{ number_format($record->youtube_subscribers) }}</strong>
            VER: {{ number_format($record->youtube_view_rate) }}
            ({{ number_format($record->youtube_view_rate/$record->youtube_subscribers*100, 2) }}%)<br />
        </span>
        @else
        <span class="badge badge-default">No Subscribers</span><br />
        @endif
        @else
        <span class="badge badge-warning">Processing</span><br />
        @endif
        @endif
        @if ($record->facebook_id)
        <img src="{{ url('img/icon-facebook.png') }}" alt="facebook" class="icon-sm" />
        @if ($record->facebook_user_page)
        <span class="badge badge-warning">Unavailable</span><br />
        @elseif ($record->facebook_update_disabled_at)
        <span class="badge badge-danger">Error</span><br />
        @elseif ($record->facebook_update_succeeded_at)
        @if ($record->facebook_followers)
        <span title="Last updated on {{ $record->facebook_update_succeeded_at->format('d M Y') }}">
            <strong>{{ number_format($record->facebook_followers) }}</strong>
            PER: {{ number_format($record->facebook_engagement_rate_post) }}
            ({{ number_format($record->facebook_engagement_rate_post/$record->facebook_followers*100, 2) }}%)
            VER: {{ number_format($record->facebook_engagement_rate_video) }}
            ({{ number_format($record->facebook_engagement_rate_video/$record->facebook_followers*100, 2) }}%)<br />
        </span>
        @else
        <span class="badge badge-default">No Followers</span><br />
        @endif
        @else
        <span class="badge badge-warning">Processing</span><br />
        @endif
        @endif
        @if ($record->twitter_id)
        <img src="{{ url('img/icon-twitter.png') }}" alt="twitter" class="icon-sm" />
        @if ($record->twitter_update_disabled_at)
        <span class="badge badge-danger">Error</span><br />
        @elseif ($record->twitter_update_succeeded_at)
        @if ($record->twitter_followers)
        <span title="Last updated on {{ $record->twitter_update_succeeded_at->format('d M Y') }}">
            <strong>{{ number_format($record->twitter_followers) }}</strong>
            TER: {{ number_format($record->twitter_engagement_rate) }}
            ({{ number_format($record->twitter_engagement_rate/$record->twitter_followers*100, 2) }}%)<br />
        </span>
        @else
        <span class="badge badge-default">No Followers</span><br />
        @endif
        @else
        <span class="badge badge-warning">Processing</span><br />
        @endif
        @endif
        @if ($record->tiktok_id)
        <img src="{{ url('img/icon-tiktok.png') }}" alt="tiktok" class="icon-sm" />
        @if ($record->tiktok_update_disabled_at)
        <span class="badge badge-danger">Error</span><br />
        @elseif ($record->tiktok_update_succeeded_at)
        @if ($record->tiktok_followers)
        <span title="Last updated on {{ $record->tiktok_update_succeeded_at->format('d M Y') }}">
            <strong>{{ number_format($record->tiktok_followers) }}</strong>
            TER: {{ number_format($record->tiktok_engagement_rate_post) }}
            ({{ number_format($record->tiktok_engagement_rate_post/$record->tiktok_followers*100, 2) }}%)<br />
        </span>
        @else
        <span class="badge badge-default">No Followers</span><br />
        @endif
        @else
        <span class="badge badge-warning">Processing</span><br />
        @endif
        @endif
        @if ($record->weibo_id)
        <img src="{{ url('img/icon-weibo.png') }}" alt="weibo" class="icon-sm" />
        @if ($record->weibo_update_disabled_at)
        <span class="badge badge-danger">Error</span><br />
        @elseif ($record->weibo_update_succeeded_at)
        @if ($record->weibo_followers)
        <span title="Last updated on {{ $record->weibo_update_succeeded_at->format('d M Y') }}">
            <strong>{{ number_format($record->weibo_followers) }}</strong>
            PER: {{ number_format($record->weibo_engagement_rate_post) }}
            ({{ number_format($record->weibo_engagement_rate_post/$record->weibo_followers*100, 2) }}%)<br />
        </span>
        @else
        <span class="badge badge-default">No Followers</span><br />
        @endif
        @else
        <span class="badge badge-warning">Processing</span><br />
        @endif
        @endif
        @if ($record->xiaohongshu_id)
        <img src="{{ url('img/icon-xiaohongshu.png') }}" alt="xiaohongshu" class="icon-sm" />
        @if ($record->xiaohongshu_update_disabled_at)
        <span class="badge badge-danger">Error</span><br />
        @elseif ($record->xiaohongshu_update_succeeded_at)
        @if ($record->xiaohongshu_followers)
        <span title="Last updated on {{ $record->xiaohongshu_update_succeeded_at->format('d M Y') }}">
            <strong>{{ number_format($record->xiaohongshu_followers) }}</strong>
            PER: {{ number_format($record->xiaohongshu_engagement_rate) }}
            ({{ number_format($record->xiaohongshu_engagement_rate/$record->xiaohongshu_followers*100, 2) }}%)<br />
        </span>
        @else
        <span class="badge badge-default">No Followers</span><br />
        @endif
        @else
        <span class="badge badge-warning">Processing</span><br />
        @endif
        @endif
        @if ($record->miaopai_followers)
        MP: {{ number_format($record->miaopai_followers) }}
        ER: {{ number_format($record->miaopai_engagement_rate) }}
        ({{ number_format($record->miaopai_engagement_rate/$record->miaopai_followers*100, 2) }}%)<br />
        @endif
        @if ($record->yizhibo_followers)
        MP: {{ number_format($record->yizhibo_followers) }}
        ER: {{ number_format($record->yizhibo_engagement_rate) }}
        ({{ number_format($record->yizhibo_engagement_rate/$record->yizhibo_followers*100, 2) }}%)<br />
        @endif
        <p class="py-2 m-0">
            <span class="text-muted text-break">
                {{ $record->description_ppt ? $record->description_ppt : ($record->description ? mb_strimwidth($record->description, 0, 240, "...") : '- No description -') }}
            </span>
        </p>
    </div>
    <div class="col-12 col-sm-2 text-center my-1">
        <div class="shortlist">
            <?php $selected = session('selected'); ?>
            @if (is_array($selected) && array_search($record->id, $selected) !== false)
            <div data-record-id="{{ $record->id }}" class="btn btn-success btn-sm mb-3">Shortlisted</div>
            @else
            <div data-record-id="{{ $record->id }}" class="btn btn-primary btn-sm mb-3 text-nowrap">Add to Shortlist</div>
            @endif
        </div>
        <?php
            $affiliations = array_map(function($affiliation) {
                return strtolower($affiliation);
            }, $affiliations = $record->affiliationTags->pluck('name')->toArray());
            ?>
        @if (array_search('atelier', $affiliations) !== false)
        <p><img src="{{ url('img/logo-atelier.png') }}" class="affiliation-logo" /></p>
        @endif
        @if (array_search('gcs', $affiliations) !== false)
        <p><img src="{{ url('img/logo-gcs.png') }}" class="affiliation-logo" /></p>
        @endif
        @if (array_search('gcx', $affiliations) !== false)
        <p><img src="{{ url('img/logo-gcx.png') }}" class="affiliation-logo" /></p>
        @endif
        @if (array_search('gta', $affiliations) !== false || $record->recommended)
        <p><img src="{{ url('img/logo-gta.png') }}" class="affiliation-logo" /></p>
        @endif
        @if (array_search('gushcloud', $affiliations) !== false)
        <p><img src="{{ url('img/logo-gushcloud.png') }}" class="affiliation-logo" /></p>
        @endif
        @if (array_search('made', $affiliations) !== false)
        <p><img src="{{ url('img/logo-made.png') }}" class="affiliation-logo" /></p>
        @endif
        @if (array_search('ribbit', $affiliations) !== false)
        <p><img src="{{ url('img/logo-ribbit.png') }}" class="affiliation-logo" /></p>
        @endif
        @if (array_search('summer', $affiliations) !== false)
        <p><img src="{{ url('img/logo-summer.png') }}" class="affiliation-logo" /></p>
        @endif
    </div>
</div>
@empty
No records found
@endforelse

