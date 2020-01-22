<h4>
    YouTube
    @if ($record->youtube_id)
    @if ($record->youtube_update_disabled_at)
    <span class="label label-danger">Auto Update Failed: To Investigate</span>
    @else
    <span class="label label-success">Auto Update Active</span>
    @endif
    @endif
</h4>
<div class="form-group">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">youtube.com/channel/</span>
        </div>
        {{ Form::text('youtube_id', old('youtube_id', $record->youtube_id), ['class' => 'form-control'.($errors->has('youtube_id') ? ' is-invalid' : '')]) }}
        @if ($record->youtube_id)
        <div class="input-group-append">
            <span class="input-group-text">
                <a target="_blank" href="https://www.youtube.com/channel/{{ $record->youtube_id }}">
                    <i class="fab fa-youtube"></i>
                </a>
            </span>
        </div>
        @else
        <div class="input-group-append">
            <span class="input-group-text">
                <i class="fab fa-youtube"></i>
            </span>
        </div>
        @endif
    </div>
    @if ($errors->has('youtube_id'))
    <span class="invalid-feedback d-block">{{ $errors->first('youtube_id') }}</span>
    @endif
</div>
@if ($record->youtube_id && ! $record->youtube_update_disabled_at)
<table class="table table-striped table-sm">
    <tr>
        <td width="200">Name</td>
        <td>{{ $record->youtube_name ?: '-' }}</td>
    </tr>
    <tr>
        <td>Subscribers</td>
        @if ($record->youtube_subscribers)
        <td>{{ number_format($record->youtube_subscribers) }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Views</td>
        @if ($record->youtube_views)
        <td>{{ number_format($record->youtube_views) }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>View Rate</td>
        @if ($record->youtube_view_rate)
        <td>{{ number_format($record->youtube_view_rate) }}
            @if ($record->youtube_subscribers)
            ({{ number_format($record->youtube_view_rate/$record->youtube_subscribers*100, 2) }}%)
            @endif
        </td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Last Updated</td>
        @if ($record->youtube_update_succeeded_at)
        <td>{{ $record->youtube_update_succeeded_at->format('d M Y, H:ia') }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr class="form-group">
        <td>{{ Form::label('youtube_external_rate_video', 'Internal Rate Video', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('youtube_external_rate_video', old('youtube_external_rate_video', $record->youtube_external_rate_video), ['class' => 'form-control'.($errors->has('youtube_external_rate_video') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('youtube_external_rate_video'))
            <span class="invalid-feedback d-block">{{ $errors->first('youtube_external_rate_video') }}</span>
            @endif
        </td>
    </tr>
</table>
@endif