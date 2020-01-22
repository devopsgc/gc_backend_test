@extends('layouts.app')

@section('content')
{{ Form::open(['url' => 'records', 'method' => 'post', 'id' => 'createForm']) }}
<div class="container py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2 class="m-0">Add New Influencer</h2>
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
                            {{ Form::select('country_code', $countries->mapWithKeys(function ($country) {
                                    return [$country['iso_3166_2'] => $country['name']];
                                }), old('country_code', 'SG'), ['class' => 'form-control', 'id' => 'country_code']) }}
                            @error('country_code')<span class="invalid-feedback d-block">{{ $errors->first('country_code') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 col-form-label">
                            Name <sup class="text-red"><small>&#10033;</small></sup>
                        </label>
                        <div class="col-sm-8">
                            {{ Form::text('name', old('name'), ['class' => 'form-control'.($errors->has('name') ? ' is-invalid' : ''), 'id' => 'name']) }}
                            @error('name')<span class="invalid-feedback d-block">{{ $errors->first('name') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="gender" class="col-sm-4 col-form-label">
                            Gender <sup class="text-red"><small>&#10033;</small></sup>
                        </label>
                        <div class="col-sm-8">
                            {{ Form::select('gender',
                                ['' => 'Choose', 'F' => 'Female', 'M' => 'Male', 'N' => 'Non-binary'],
                                old('gender'),
                                ['class' => 'form-control'.($errors->has('gender') ? ' is-invalid' : ''), 'id' => 'gender']) }}
                            @error('gender')<span class="invalid-feedback d-block">{{ $errors->first('gender') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="interests" class="col-sm-4 col-form-label">
                            Interests <sup class="text-red"><small>&#10033;</small></sup>
                        </label>
                        <div class="col-sm-8">
                            {{ Form::text('interests', old('interests'), ['class' => 'interests form-control'.($errors->has('interests') ? ' is-invalid' : ''), 'id' => 'interests']) }}
                            @error('interests')<span class="invalid-feedback d-block">{{ $errors->first('interests') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="professions" class="col-sm-4 col-form-label">
                            Professions <sup class="text-red"><small>&#10033;</small></sup>
                        </label>
                        <div class="col-sm-8">
                            {{ Form::text('professions', old('professions'), ['class' => 'professions form-control'.($errors->has('professions') ? ' is-invalid' : ''), 'id' => 'professions']) }}
                            @error('professions')<span class="invalid-feedback d-block">{{ $errors->first('professions') }}</span>@enderror
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center border-top border-bottom py-2 mb-3">
                        <strong>Add at least 1 social media platform</strong>
                    </div>
                    <div class="form-group row">
                        {{ Form::label('instagram_id', 'Instagram ID', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">instagram.com/</span>
                                </div>
                                {{ Form::text('instagram_id', old('instagram_id'), ['class' => 'form-control'.($errors->has('instagram_id') ? ' is-invalid' : ''), 'id' => 'instagram_id']) }}
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                </div>
                            </div>
                            @error('instagram_id')<span class="invalid-feedback d-block">{{ $errors->first('instagram_id') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label('youtube_id', 'YouTube Channel ID', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">youtube.com/channel/</span>
                                </div>
                                {{ Form::text('youtube_id', old('youtube_id'), ['class' => 'form-control'.($errors->has('youtube_id') ? ' is-invalid' : ''), 'id' => 'youtube_id']) }}
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fab fa-youtube"></i></span>
                                </div>
                            </div>
                            @error('youtube_id')<span class="invalid-feedback d-block">{{ $errors->first('youtube_id') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label('facebook_id', 'Facebook ID', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">facebook.com/</span>
                                </div>
                                {{ Form::text('facebook_id', old('facebook_id'), ['class' => 'form-control'.($errors->has('facebook_id') ? ' is-invalid' : ''), 'id' => 'facebook_id']) }}
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                </div>
                            </div>
                            @error('facebook_id')<span class="invalid-feedback d-block">{{ $errors->first('facebook_id') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label('twitter_id', 'Twitter ID', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">twitter.com/</span>
                                </div>
                                {{ Form::text('twitter_id', old('twitter_id'), ['class' => 'form-control'.($errors->has('twitter_id') ? ' is-invalid' : ''), 'id' => 'twitter_id']) }}
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                </div>
                            </div>
                            @error('twitter_id')<span class="invalid-feedback d-block">{{ $errors->first('twitter_id') }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label('tiktok_id', 'TikTok ID', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">tiktok.com/@</span>
                                </div>
                                {{ Form::text('tiktok_id', old('tiktok_id'), ['class' => 'form-control'.($errors->has('tiktok_id') ? ' is-invalid' : '')]) }}
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-music"></i></span>
                                </div>
                            </div>
                            @error('tiktok_id')<span class="invalid-feedback d-block">{{ $errors->first('tiktok_id') }}</span>@enderror
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
        options: @json($interests),
        delimiter: '|',
        create: false,
        persist: true
    });
    $('.professions').selectize({
        options: @json($professions),
        delimiter: '|',
        create: false,
        persist: true
    });
});
</script>
@endsection