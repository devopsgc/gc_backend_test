<h4>Yizhibo</h4>
<div class="form-group">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">yizhibo.com/l/</span>
        </div>
        {{ Form::text('yizhibo_id', old('yizhibo_id', $record->yizhibo_id), ['class' => 'form-control'.($errors->has('yizhibo_id') ? ' is-invalid' : '')]) }}
        @if ($record->yizhibo_id)
        <div class="input-group-append">
            <span class="input-group-text">.html <a target="_blank" href="https://www.yizhibo.com/l/{{ $record->yizhibo_id }}"></a></span>
        </div>
        @else
        <div class="input-group-append">
            <span class="input-group-text">.html</span>
        </div>
        @endif
    </div>
    @if ($errors->has('yizhibo_id'))
    <span class="invalid-feedback">{{ $errors->first('yizhibo_id') }}</span>
    @endif
</div>
@if ($record->yizhibo_id)
<table class="table table-striped table-sm">
    <tr class="form-group">
        <td width="200">{{ Form::label('yizhibo_external_rate_livestream', 'Internal Rate Livestream', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('yizhibo_external_rate_livestream', old('yizhibo_external_rate_livestream', $record->yizhibo_external_rate_livestream), ['class' => 'form-control'.($errors->has('yizhibo_external_rate_livestream') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('yizhibo_external_rate_livestream'))
            <span class="invalid-feedback">{{ $errors->first('yizhibo_external_rate_livestream') }}</span>
            @endif
        </td>
    </tr>
</table>
@endif