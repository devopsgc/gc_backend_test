<h4>
    Xiaohongshu
    @if ($record->xiaohongshu_id)
    @if ($record->xiaohongshu_update_disabled_at)
    <span class="label label-danger">Auto Update Failed: To Investigate</span>
    @else
    <span class="label label-success">Auto Update Active</span>
    @endif
    @endif
</h4>
<div class="form-group">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">xiaohongshu.com/user/profile/</span>
        </div>
        {{ Form::text('xiaohongshu_id', old('xiaohongshu_id', $record->xiaohongshu_id), ['class' => 'form-control'.($errors->has('xiaohongshu_id') ? ' is-invalid' : '')]) }}
        @if ($record->xiaohongshu_id)
        <div class="input-group-append">
            <span class="input-group-text">
                <a target="_blank" href="https://xiaohongshu.com/user/profile/{{ $record->xiaohongshu_id }}">
                    <i class="fas fa-book"></i>
                </a>
            </span>
        </div>
        @else
        <div class="input-group-append">
            <span class="input-group-text">
                <i class="fas fa-book"></i>
            </span>
        </div>
        @endif
    </div>
    @if ($errors->has('xiaohongshu_id'))
    <span class="invalid-feedback d-block">{{ $errors->first('xiaohongshu_id') }}</span>
    @endif
</div>
@if ($record->xiaohongshu_id && ! $record->xiaohongshu_update_disabled_at)
<table class="table table-striped table-sm">
    <tr>
        <td width="200">Followers</td>
        @if ($record->xiaohongshu_followers)
        <td>{{ number_format($record->xiaohongshu_followers) }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td width="200">Engagements</td>
        @if ($record->xiaohongshu_engagements)
        <td>{{ number_format($record->xiaohongshu_engagements) }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td width="200">Post Engagements</td>
        @if ($record->xiaohongshu_engagement_rate)
        <td>{{ number_format($record->xiaohongshu_engagement_rate) }}
            @if ($record->xiaohongshu_followers)
            ({{ number_format($record->xiaohongshu_engagement_rate/$record->xiaohongshu_followers*100, 2) }}%)
            @endif
        </td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr>
        <td>Last Updated</td>
        @if ($record->xiaohongshu_update_succeeded_at)
        <td>{{ $record->xiaohongshu_update_succeeded_at->format('d M Y, H:ia') }}</td>
        @else
        <td>-</td>
        @endif
    </tr>
    <tr class="form-group">
        <td>{{ Form::label('xiaohongshu_external_rate', 'Internal Rate Post', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('xiaohongshu_external_rate', old('xiaohongshu_external_rate', $record->xiaohongshu_external_rate), ['class' => 'form-control'.($errors->has('xiaohongshu_external_rate') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('xiaohongshu_external_rate'))
            <span class="invalid-feedback d-block">{{ $errors->first('xiaohongshu_external_rate') }}</span>
            @endif
        </td>
    </tr>
</table>
@endif