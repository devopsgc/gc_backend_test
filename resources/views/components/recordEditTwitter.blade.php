<h4>
    Twitter
    @if ($record->twitter_id)
    @if ($record->twitter_update_disabled_at)
    <span class="label label-danger">Auto Update Failed: To Investigate</span>
    @else
    <span class="label label-success">Auto Update Active</span>
    @endif
    @endif
</h4>
<div class="form-group">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">twitter.com/</span>
        </div>
        {{ Form::text('twitter_id', old('twitter_id', $record->twitter_id), ['class' => 'form-control'.($errors->has('twitter_id') ? ' is-invalid' : '')]) }}
        @if ($record->twitter_id)
        <div class="input-group-append">
            <span class="input-group-text">
                <a target="_blank" href="https://www.twitter.com/{{ $record->twitter_id }}">
                    <i class="fab fa-twitter"></i>
                </a>
            </span>
        </div>
        @else
        <div class="input-group-append">
            <span class="input-group-text">
                <i class="fab fa-twitter"></i>
            </span>
        </div>
        @endif
    </div>
    @if ($errors->has('twitter_id'))
    <span class="invalid-feedback d-block">{{ $errors->first('twitter_id') }}</span>
    @endif
</div>
@if ($record->twitter_id && ! $record->twitter_update_disabled_at)
<table class="table table-striped table-sm">
    <tr>
        <td width="200">Name</td>
        <td>{{ $record->twitter_name ?: '-' }}</td>
    </tr>
    <tr>
        <td>Followers</td>
        @if ($record->twitter_followers)
        <td>{{ number_format($record->twitter_followers) }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Tweets</td>
        @if ($record->twitter_tweets)
        <td>{{ number_format($record->twitter_tweets) }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Post Engagements</td>
        @if ($record->twitter_engagement_rate)
        <td>{{ number_format($record->twitter_engagement_rate) }}
            @if ($record->twitter_followers)
            ({{ number_format($record->twitter_engagement_rate/$record->twitter_followers*100, 2) }}%)
            @endif
        </td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Last Updated</td>
        @if ($record->twitter_update_succeeded_at)
        <td>{{ $record->twitter_update_succeeded_at->format('d M Y, H:ia') }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
</table>
@endif