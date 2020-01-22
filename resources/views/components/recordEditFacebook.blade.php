<h4>
    Facebook
    @if ($record->facebook_id)
    @if ($record->facebook_update_disabled_at)
    @if ($record->facebook_user_page)
    <span class="label label-danger">[Userpage] Auto Update Disabled</span>
    @else
    <span class="label label-danger">Auto Update Failed: To Investigate</span>
    @endif
    @else
    <span class="label label-success">Auto Update Active</span>
    @endif
    @endif
</h4>
<div class="form-group">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">facebook.com/</span>
        </div>
        {{ Form::text('facebook_id', old('facebook_id', $record->facebook_id), ['class' => 'form-control'.($errors->has('facebook_id') ? ' is-invalid' : '')]) }}
        @if ($record->facebook_id)
        <div class="input-group-append">
            <span class="input-group-text">
                <a target="_blank" href="https://www.facebook.com/{{ $record->facebook_id }}">
                    <i class="fab fa-facebook"></i>
                </a>
            </span>
        </div>
        @else
        <div class="input-group-append">
            <span class="input-group-text">
                <i class="fab fa-facebook"></i>
            </span>
        </div>
        @endif
    </div>
    @if ($errors->has('facebook_id'))
    <span class="invalid-feedback d-block">{{ $errors->first('facebook_id') }}</span>
    @endif
</div>
@if ($record->facebook_id)
@if ($record->facebook_update_disabled_at)
@if ($record->facebook_user_page)
<table class="table table-striped table-sm">
    <tr class="form-group">
        <td width="200">
            {{ Form::label('facebook_name', 'Name', ['class' => 'col-form-label']) }}
        </td>
        <td>
            {{ Form::text('facebook_name', old('facebook_name', $record->facebook_name), ['class' => 'form-control'.($errors->has('facebook_name') ? ' is-invalid' : '')]) }}
            @if ($errors->has('facebook_name'))
            <span class="invalid-feedback d-block">{{ $errors->first('facebook_name') }}</span>
            @endif
        </td>
    </tr>
    <tr class="form-group">
        <td width="200">
            {{ Form::label('facebook_followers', 'Followers', ['class' => 'col-form-label']) }}
        </td>
        <td>
            {{ Form::text('facebook_followers', old('facebook_followers', $record->facebook_followers), ['class' => 'form-control'.($errors->has('facebook_followers') ? ' is-invalid' : '')]) }}
            @if ($errors->has('facebook_followers'))
            <span class="invalid-feedback d-block">{{ $errors->first('facebook_followers') }}</span>
            @endif
        </td>
    </tr>
    <tr class="form-group">
        <td width="200">
            {{ Form::label('facebook_engagement_rate_post', 'Eng. Rate Post', ['class' => 'col-form-label']) }}
        </td>
        <td>
            {{ Form::text('facebook_engagement_rate_post', old('facebook_engagement_rate_post', $record->facebook_engagement_rate_post), ['class' => 'form-control'.($errors->has('facebook_engagement_rate_post') ? ' is-invalid' : '')]) }}
            @if ($errors->has('facebook_engagement_rate_post'))
            <span class="invalid-feedback d-block">{{ $errors->first('facebook_engagement_rate_post') }}</span>
            @endif
        </td>
    </tr>
    <tr class="form-group">
        <td width="200">
            {{ Form::label('facebook_engagement_rate_video', 'Eng. Rate Video', ['class' => 'col-form-label']) }}
        </td>
        <td>
            {{ Form::text('facebook_engagement_rate_video', old('facebook_engagement_rate_video', $record->facebook_engagement_rate_video), ['class' => 'form-control'.($errors->has('facebook_engagement_rate_video') ? ' is-invalid' : '')]) }}
            @if ($errors->has('facebook_engagement_rate_video'))
            <span class="invalid-feedback d-block">{{ $errors->first('facebook_engagement_rate_video') }}</span>
            @endif
        </td>
    </tr>
</table>
@endif
@else
<table class="table table-striped table-sm">
    <tr>
        <td width="200">Name</td>
        <td>{{ $record->facebook_name ?: '-' }}</td>
    </tr>
    <tr>
        <td>Followers</td>
        @if ($record->facebook_followers)
        <td>{{ number_format($record->facebook_followers) }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Post Engagements</td>
        @if ($record->facebook_engagement_rate_post)
        <td>{{ number_format($record->facebook_engagement_rate_post, 2) }}
            @if ($record->facebook_followers)
            ({{ number_format($record->facebook_engagement_rate_post/$record->facebook_followers*100, 2) }}%)
            @endif
        </td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Video Engagements</td>
        @if ($record->facebook_engagement_rate_video)
        <td>{{ number_format($record->facebook_engagement_rate_video, 2) }}
            @if ($record->facebook_followers)
            ({{ number_format($record->facebook_engagement_rate_video/$record->facebook_followers*100, 2) }}%)
            @endif
        </td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Last Updated</td>
        @if ($record->facebook_update_succeeded_at)
        <td>{{ $record->facebook_update_succeeded_at->format('d M Y, H:ia') }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr class="form-group">
        <td>{{ Form::label('facebook_external_rate_post', 'Internal Rate Post', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('facebook_external_rate_post', old('facebook_external_rate_post', $record->facebook_external_rate_post), ['class' => 'form-control'.($errors->has('facebook_external_rate_post') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('facebook_external_rate_post'))
            <span class="invalid-feedback d-block">asdas{{ $errors->first('facebook_external_rate_post') }}</span>
            @endif
        </td>
    </tr>
    <tr class="form-group">
        <td>{{ Form::label('facebook_external_rate_video', 'Internal Rate Video', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('facebook_external_rate_video', old('facebook_external_rate_video', $record->facebook_external_rate_video), ['class' => 'form-control'.($errors->has('facebook_external_rate_video') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('facebook_external_rate_video'))
            <span class="invalid-feedback d-block">{{ $errors->first('facebook_external_rate_video') }}</span>
            @endif
        </td>
    </tr>
    <tr class="form-group">
        <td>{{ Form::label('facebook_external_rate_story', 'Internal Rate Story', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('facebook_external_rate_story', old('facebook_external_rate_story', $record->facebook_external_rate_story), ['class' => 'form-control'.($errors->has('facebook_external_rate_story') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('facebook_external_rate_story'))
            <span class="invalid-feedback d-block">{{ $errors->first('facebook_external_rate_story') }}</span>
            @endif
        </td>
    </tr>
</table>
@endif
@endif