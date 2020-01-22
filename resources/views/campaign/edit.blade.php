@extends('layouts.app')

@section('content')
{{ Form::open(['url' => $campaign->getPath(), 'method' => 'put']) }}
<input type="hidden" name="status" value="{{ \App\Models\Campaign::STATUS_ACCEPTED }}" />

<div class="container py-4">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h2 class="m-0">Create Campaign #{{ $campaign->id }}</h2>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="text-right mb-2">
                        <sup class="text-red"><small>&#10033;</small></sup> required fields
                    </div>
                    <div class="form-group row">
                        <label for="country_code" class="col-sm-4 col-form-label">
                            Country <sup class="text-red"><small>&#10033;</small></sup>
                        </label>
                        <div class="col-sm-8">
                            {{ Form::select('country_code',
                                $countries->sortBy('name')->mapWithKeys(function ($country) { return [$country['iso_3166_2'] => $country['name']]; }),
                                old('country_code', $campaign->country->iso_3166_2),
                                ['class' => 'form-control']) }}
                            @error('country_code')<span class="invalid-feedback d-block">{{ $errors->first('country_code') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 col-form-label">
                            Campaign Name <sup class="text-red"><small>&#10033;</small></sup>
                        </label>
                        <div class="col-sm-8">
                            {{ Form::text('name', old('name', $campaign->name), ['class' => 'form-control'.($errors->has('name') ? ' is-invalid' : '')]) }}
                            @error('name')<span class="invalid-feedback d-block">{{ $errors->first('name') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label('brand', 'Brand', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            {{ Form::text('brand', old('brand', $campaign->brand), ['class' => 'form-control'.($errors->has('brand') ? ' is-invalid' : '')]) }}
                            @error('brand')<span class="invalid-feedback d-block">{{ $errors->first('brand') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label('interests', 'Categories', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            {{ Form::text('interests', old('interests', $campaign->categories), ['class' => 'interests form-control'.($errors->has('interests') ? ' is-invalid' : '')]) }}
                            @error('interests')<span class="invalid-feedback d-block">{{ $errors->first('interests') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label('description', 'Description', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            {{ Form::textarea('description', old('description', $campaign->description), ['class' => 'form-control'.($errors->has('interests') ? ' is-invalid' : ''), 'rows' => 3]) }}
                            @error('description')<span class="invalid-feedback d-block">{{ $errors->first('description') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label('currency_code', 'Currency', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            {{ Form::select('currency_code',
                                $countries->sortBy('currency_code')->mapWithKeys(function ($country) { return [$country['currency_code'] => $country['currency_code']]; }),
                                old('currency_code', $campaign->currency_code),
                                ['id' => 'currency_code', 'class' => 'form-control'.($errors->has('currency_code') ? ' is-invalid' : '')]) }}
                            @error('currency_code')<span class="invalid-feedback d-block">{{ $errors->first('currency_code') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label('budget', 'Budget', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            {{ Form::number('budget', old('budget', $campaign->budget), ['class' => 'form-control'.($errors->has('budget') ? ' is-invalid' : ''), 'rows' => 3]) }}
                            @error('budget')<span class="invalid-feedback d-block">{{ $errors->first('budget') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label('start_at', 'Start Date', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            <div class="input-group date" data-provide="datepicker" data-date-format="dd MM yyyy">
                                {{ Form::text('start_at',
                                    old('start_at', isset($campaign) && $campaign->start_at ? $campaign->start_at->format('d F Y') : null),
                                    ['class' => 'form-control datepicker'.($errors->has('start_at') ? ' is-invalid' : ''), 'autocomplete' => 'off', 'placeholder' => '']) }}
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-light"><i class="far fa-calendar-alt"></i></div>
                                </div>
                            </div>
                            @error('start_at')<span class="invalid-feedback d-block">{{ $errors->first('start_at') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label('end_at', 'End Date', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            <div class="input-group date" data-provide="datepicker" data-date-format="dd MM yyyy">
                                {{ Form::text('end_at',
                                    old('end_at', isset($campaign) && $campaign->end_at ? $campaign->end_at->format('d F Y') : null),
                                    ['class' => 'form-control datepicker'.($errors->has('end_at') ? ' is-invalid' : ''), 'autocomplete' => 'off', 'placeholder' => '']) }}
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-light"><i class="far fa-calendar-alt"></i></div>
                                </div>
                            </div>
                            @error('end_at')<span class="invalid-feedback d-block">{{ $errors->first('end_at') }}</span>@enderror
                        </div>
                    </div>
                    <div class="text-right">
                        {{ Form::submit('Save', ['class' => 'btn btn-primary btn-lg']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{ Form::close() }}
@endsection

@section('script')
<script>
    $(function() {
    $('.interests').selectize({
        options: {!! json_encode($interests) !!},
        delimiter: '|',
        create: false,
        persist: true
    });
});
</script>
@endsection