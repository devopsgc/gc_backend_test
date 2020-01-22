<h4>
    Weibo
    @if ($record->weibo_id)
    @if ($record->weibo_update_disabled_at)
    <span class="label label-danger">Auto Update Failed: To Investigate</span>
    @else
    <span class="label label-success">Auto Update Active</span>
    @endif
    @endif
</h4>
<div class="form-group">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">weibo.com/</span>
        </div>
        {{ Form::text('weibo_id', old('weibo_id', $record->weibo_id), ['class' => 'form-control'.($errors->has('weibo_id') ? ' is-invalid' : '')]) }}
        @if ($record->weibo_id)
        <div class="input-group-append">
            <span class="input-group-text">
                <a target="_blank" href="https://www.weibo.com/{{ $record->weibo_id }}">
                    <i class="fab fa-weibo"></i>
                </a>
            </span>
        </div>
        @else
        <div class="input-group-append">
            <span class="input-group-text">
                <i class="fab fa-weibo"></i>
            </span>
        </div>
        @endif
    </div>
    @if ($errors->has('weibo_id'))
    <span class="invalid-feedback d-block">{{ $errors->first('weibo_id') }}</span>
    @endif
</div>
@if ($record->weibo_id && ! $record->weibo_update_disabled_at)
<table class="table table-striped table-sm">
    <tr>
        <td width="200">Followers</td>
        @if ($record->weibo_followers)
        <td>{{ number_format($record->weibo_followers) }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Post Engagements</td>
        @if ($record->weibo_engagement_rate_post)
        <td>{{ number_format($record->weibo_engagement_rate_post) }}
            @if ($record->weibo_followers)
            ({{ number_format($record->weibo_engagement_rate_post/$record->weibo_followers*100, 2) }}%)
            @endif
        </td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Last Updated</td>
        @if ($record->weibo_update_succeeded_at)
        <td>{{ $record->weibo_update_succeeded_at->format('d M Y, H:ia') }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr class="form-group">
        <td>{{ Form::label('weibo_external_rate_post', 'Internal Rate Post', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('weibo_external_rate_post', old('weibo_external_rate_post', $record->weibo_external_rate_post), ['class' => 'form-control'.($errors->has('weibo_external_rate_post') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('weibo_external_rate_post'))
            <span class="invalid-feedback d-block">{{ $errors->first('weibo_external_rate_post') }}</span>
            @endif
        </td>
    </tr>
    <tr class="form-group">
        <td>{{ Form::label('weibo_external_rate_livestream', 'Internal Rate Livestream', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('weibo_external_rate_livestream', old('weibo_external_rate_livestream', $record->weibo_external_rate_livestream), ['class' => 'form-control'.($errors->has('weibo_external_rate_livestream') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('weibo_external_rate_livestream'))
            <span class="invalid-feedback d-block">{{ $errors->first('weibo_external_rate_livestream') }}</span>
            @endif
        </td>
    </tr>
</table>
@endif