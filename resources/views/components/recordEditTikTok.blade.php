<h4>
    TikTok
    @if ($record->tiktok_id)
    @if ($record->tiktok_update_disabled_at)
    <span class="label label-danger">Auto Update Failed: To Investigate</span>
    @else
    <span class="label label-success">Auto Update Active</span>
    @endif
    @endif
</h4>
<div class="form-group">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">tiktok.com/@</span>
        </div>
        {{ Form::text('tiktok_id', old('tiktok_id', $record->tiktok_id), ['class' => 'form-control'.($errors->has('tiktok_id') ? ' is-invalid' : '')]) }}
        @if ($record->tiktok_id)
        <div class="input-group-append">
            <span class="input-group-text">
                <a target="_blank" href="https://www.tiktok.com/{{ '@'.$record->tiktok_id }}">
                    <i class="fas fa-music"></i>
                </a>
            </span>
        </div>
        @else
        <div class="input-group-append">
            <span class="input-group-text">
                <i class="fas fa-music"></i>
            </span>
        </div>
        @endif
    </div>
    @if ($errors->has('tiktok_id'))
    <span class="invalid-feedback d-block">{{ $errors->first('tiktok_id') }}</span>
    @endif
</div>
@if ($record->tiktok_id && ! $record->tiktok_update_disabled_at)
<table class="table table-striped table-sm">
    <tr>
        <td width="200">Name</td>
        <td>{{ $record->tiktok_name ?: '-' }}</td>
    </tr>
    <tr>
        <td>Followers</td>
        @if ($record->tiktok_followers)
        <td>{{ number_format($record->tiktok_followers) }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Engagements</td>
        @if ($record->tiktok_engagements)
        <td>{{ number_format($record->tiktok_engagements) }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Post Engagements</td>
        @if ($record->tiktok_engagement_rate_post)
        <td>{{ number_format($record->tiktok_engagement_rate_post) }}
            @if ($record->tiktok_followers)
            ({{ number_format($record->tiktok_engagement_rate_post/$record->tiktok_followers*100, 2) }}%)
            @endif
        </td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Last Updated</td>
        @if ($record->tiktok_update_succeeded_at)
        <td>{{ $record->tiktok_update_succeeded_at->format('d M Y, H:ia') }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr class="form-group">
        <td>{{ Form::label('tiktok_external_rate_post', 'Internal Rate Post', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('tiktok_external_rate_post', old('tiktok_external_rate_post', $record->tiktok_external_rate_post), ['class' => 'form-control'.($errors->has('tiktok_external_rate_post') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('tiktok_external_rate_post'))
            <span class="invalid-feedback d-block">{{ $errors->first('tiktok_external_rate_post') }}</span>
            @endif
        </td>
    </tr>
</table>
@endif