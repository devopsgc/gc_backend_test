<h4>Miaopai</h4>
<div class="form-group">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">miaopai.com/u/</span>
        </div>
        {{ Form::text('miaopai_id', old('miaopai_id', $record->miaopai_id), ['class' => 'form-control'.($errors->has('miaopai_id') ? ' is-invalid' : '')]) }}
        @if ($record->miaopai_id)
        <div class="input-group-append">
            <span class="input-group-text">
                <a target="_blank" href="http://www.miaopai.com/u/{{ $record->miaopai_id }}">
                    <i class="fas fa-camera"></i>
                </a>
            </span>
        </div>
        @else
        <div class="input-group-append">
            <span class="input-group-text">
                <i class="fas fa-camera"></i>
            </span>
        </div>
        @endif
    </div>
    @if ($errors->has('miaopai_id'))
    <span class="invalid-feedback">{{ $errors->first('miaopai_id') }}</span>
    @endif
</div>
@if ($record->miaopai_id)
<table class="table table-striped table-sm">
    <tr class="form-group">
        <td width="200">{{ Form::label('miaopai_external_rate_livestream', 'Internal Rate Livestream', ['class' => 'col-form-label']) }}</td>
        <td>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                {{ Form::text('miaopai_external_rate_livestream', old('miaopai_external_rate_livestream', $record->miaopai_external_rate_livestream), ['class' => 'form-control'.($errors->has('miaopai_external_rate_livestream') ? ' is-invalid' : '')]) }}
            </div>
            @if ($errors->has('miaopai_external_rate_livestream'))
            <span class="invalid-feedback">{{ $errors->first('miaopai_external_rate_livestream') }}</span>
            @endif
        </td>
    </tr>
</table>
@endif