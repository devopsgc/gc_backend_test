<h4>
    Instagram
    @if ($record->instagram_id)
    @if ($record->instagram_update_disabled_at)
    <span class="badge badge-danger">Auto Update Failed: To Investigate</span>
    @else
    <span class="badge badge-success">Auto Update Active</span>
    @endif
    @endif
</h4>
<div class="form-group">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                instagram.com/
            </span>
        </div>
        {{ Form::text('instagram_id', old('instagram_id', $record->instagram_id), ['class' => 'form-control'.($errors->has('instagram_id') ? ' is-invalid' : '')]) }}
        @if ($record->instagram_id)
        <div class="input-group-append">
            <span class="input-group-text">
                <a target="_blank" href="https://www.instagram.com/{{ $record->instagram_id }}">
                    <i class="fab fa-instagram"></i>
                </a>
            </span>
        </div>
        @else
        <div class="input-group-append">
            <span class="input-group-text">
                <i class="fab fa-instagram"></i>
            </span>
        </div>
        @endif
    </div>
    @if ($errors->has('instagram_id'))
    <span class="invalid-feedback d-block">{{ $errors->first('instagram_id') }}</span>
    @endif
</div>
@if ($record->instagram_id && ! $record->instagram_update_disabled_at)
<table class="table table-striped table-sm">
    <tr>
        <td width="200">Name</td>
        <td>{{ $record->instagram_name ?: '-' }}</td>
    </tr>
    <tr>
        <td>Followers</td>
        @if ($record->instagram_followers)
        <td>{{ number_format($record->instagram_followers) }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Post Engagements</td>
        @if ($record->instagram_engagement_rate_post)
        <td>{{ number_format($record->instagram_engagement_rate_post) }}
            @if ($record->instagram_followers)
            ({{ number_format($record->instagram_engagement_rate_post/$record->instagram_followers*100, 2) }}%)
            @endif
        </td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Video Engagements</td>
        @if ($record->instagram_engagement_rate_video)
        <td>{{ number_format($record->instagram_engagement_rate_video) }}
            @if ($record->instagram_followers)
            ({{ number_format($record->instagram_engagement_rate_video/$record->instagram_followers*100, 2) }}%)
            @endif
        </td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Last Updated</td>
        @if ($record->instagram_update_succeeded_at)
        <td>{{ $record->instagram_update_succeeded_at->format('d M Y, H:ia') }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr class="form-group">
        <td>{{ Form::label('instagram_external_rate_post', 'Internal Rate Post', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('instagram_external_rate_post', old('instagram_external_rate_post', $record->instagram_external_rate_post), ['class' => 'form-control'.($errors->has('instagram_external_rate_post') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('instagram_external_rate_post'))
            <span class="invalid-feedback d-block">{{ $errors->first('instagram_external_rate_post') }}</span>
            @endif
        </td>
    </tr>
    <tr class="form-group">
        <td>{{ Form::label('instagram_external_rate_video', 'Internal Rate Video', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('instagram_external_rate_video', old('instagram_external_rate_video', $record->instagram_external_rate_video), ['class' => 'form-control'.($errors->has('instagram_external_rate_video') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('instagram_external_rate_video'))
            <span class="invalid-feedback d-block">{{ $errors->first('instagram_external_rate_video') }}</span>
            @endif
        </td>
    </tr>
    <tr class="form-group">
        <td>{{ Form::label('instagram_external_rate_story', 'Internal Rate Story', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('instagram_external_rate_story', old('instagram_external_rate_story', $record->instagram_external_rate_story), ['class' => 'form-control'.($errors->has('instagram_external_rate_story') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('instagram_external_rate_story'))
            <span class="invalid-feedback d-block">{{ $errors->first('instagram_external_rate_story') }}</span>
            @endif
        </td>
    </tr>
</table>
@endif