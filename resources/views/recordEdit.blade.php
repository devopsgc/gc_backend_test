@extends('layouts.app')

@section('content')
{{ Form::open(['url' => 'records/'.$record->id, 'method' => 'put', 'files' => true, 'id' => 'recordForm']) }}
<div class="ajaxModalHeader">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <h5 class="py-3">{{ $record->name }}</h5>
            </div>
            <div class="col-6 text-right">
                <p>{{ Form::submit('Save', ['class' => 'btn btn-primary']) }}</p>
            </div>
        </div>
    </div>
</div>
<div class="ajaxModalBody">
    <div class="container-fluid">
        @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <h5>Please correct the following errors before trying to save again.</h5>
            <ul class="m-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        @endif
        <div class="row">
            <div class="col-lg-5">
                <div class="d-flex justify-content-between">
                    <h4>Personal Info</h4>
                    <div class="text-right"><sup class="text-danger"><small>&#10033;</small></sup> required fields</div>
                </div>
                <table class="table table-striped table-sm">
                    <tr class="form-group">
                        <td>
                            <label for="name" class="col-form-label">
                                Name <sup class="text-danger"><small>&#10033;</small></sup>
                            </label>
                        </td>
                        <td>
                            {{ Form::text('name', old('name', $record->name), ['id' => 'name', 'class' => 'form-control'.($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            @if ($errors->has('name'))
                            <span class="invalid-feedback d-block">{{ $errors->first('name') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td>
                            {{ Form::label('second_name', 'Second Name', ['class' => 'col-form-label']) }}
                        </td>
                        <td>
                            {{ Form::text('second_name', old('second_name', $record->second_name), ['class' => 'form-control'.($errors->has('second_name') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            @if ($errors->has('second_name'))
                            <span class="invalid-feedback d-block">{{ $errors->first('second_name') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            <label for="country_code" class="col-form-label">
                                Country <sup class="text-danger"><small>&#10033;</small></sup>
                            </label>
                        </td>
                        <td>
                            {{ Form::select('country_code', $countries->mapWithKeys(function ($country) {
                                    return [$country['iso_3166_2'] => $country['name']];
                                }), old('country_code', $record->country ? $record->country->iso_3166_2 : 'SG'), ['id' => 'country_code', 'class' => 'form-control'.($errors->has('country_code') ? ' is-invalid' : '')]) }}
                            @if ($errors->has('country_code'))
                            <span class="invalid-feedback d-block">{{ $errors->first('country_code') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            {{ Form::label('state', 'State', ['class' => 'col-form-label']) }}
                        </td>
                        <td>
                            {{ Form::text('state', old('state', $record->state), ['class' => 'form-control'.($errors->has('state') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            @if ($errors->has('state'))
                            <span class="invalid-feedback d-block">{{ $errors->first('state') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            {{ Form::label('city', 'City', ['class' => 'col-form-label']) }}
                        </td>
                        <td>
                            {{ Form::text('city', old('city', $record->city), ['class' => 'form-control'.($errors->has('city') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            @if ($errors->has('city'))
                            <span class="invalid-feedback d-block">{{ $errors->first('city') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            <label for="interests" class="col-form-label">
                                Interests <sup class="text-danger"><small>&#10033;</small></sup>
                            </label>
                        </td>
                        <td>
                            {{ Form::text('interests', old('interests', $record->interestsDisplayForSelect), ['id' => 'interests', 'class' => 'form-control interests'.($errors->has('interests') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            @if ($errors->has('interests'))
                            <span class="invalid-feedback d-block">{{ $errors->first('interests') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            <label for="professions" class="col-form-label">
                                Professions <sup class="text-danger"><small>&#10033;</small></sup>
                            </label>
                        </td>
                        <td>
                            {{ Form::text('professions', old('professions', $record->professionsDisplayForSelect), ['id' => 'professions', 'class' => 'form-control professions'.($errors->has('professions') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            @if ($errors->has('professions'))
                            <span class="invalid-feedback d-block">{{ $errors->first('professions') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            <label for="gender" class="col-form-label">
                                Gender <sup class="text-danger"><small>&#10033;</small></sup>
                            </label>
                        </td>
                        <td>
                            {{ Form::select('gender', ['' => 'Choose', 'F' => 'Female', 'M' => 'Male', 'N' => 'Non-binary'], old('gender', $record->getOriginal('gender')), ['id' => 'gender', 'class' => 'form-control'.($errors->has('gender') ? ' is-invalid' : '')]) }}
                            @if ($errors->has('gender'))
                            <span class="invalid-feedback d-block">{{ $errors->first('gender') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            {{ Form::label('race', 'Race', ['class' => 'col-form-label']) }}
                        </td>
                        <td>
                            {{ Form::text('race', old('race', $record->race), ['class' => 'form-control'.($errors->has('race') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            @if ($errors->has('race'))
                            <span class="invalid-feedback d-block">{{ $errors->first('race') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            {{ Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-form-label']) }}
                        </td>
                        <td>
                            <div class="input-group date" data-provide="datepicker" data-date-format="dd MM yyyy">
                                {{ Form::text('date_of_birth', old('date_of_birth', $record->date_of_birth ? $record->date_of_birth->format('d F Y') : null), ['class' => 'form-control datepicker'.($errors->has('date_of_birth') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            @if ($errors->has('date_of_birth'))
                            <span class="invalid-feedback d-block">{{ $errors->first('date_of_birth') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            {{ Form::label('private_notes', 'Private Notes', ['class' => 'col-form-label']) }}
                        </td>
                        <td>
                            {{ Form::textarea('private_notes', old('private_notes', $record->private_notes), ['class' => 'form-control'.($errors->has('private_notes') ? ' is-invalid' : ''), 'placeholder' => '', 'rows' => 5, 'maxlength' => 4000]) }}
                            <small class="form-text text-muted word-counter">
                                {{ strlen(old('private_notes', $record->private_notes)) }} out of 4000 characters
                            </small>
                            @if ($errors->has('private_notes'))
                            <span class="invalid-feedback d-block">{{ $errors->first('private_notes') }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
                <h4>Contact Info</h4>
                <table class="table table-striped table-sm">
                    <tr class="form-group">
                        <td style="width:200px;">
                            {{ Form::label('email', 'Email', ['class' => 'col-form-label']) }}
                        </td>
                        <td>
                            <div class="input-group">
                                {{ Form::email('email', old('email', $record->email), ['class' => 'form-control'.($errors->has('email') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-at"></i>
                                    </span>
                                </div>
                            </div>
                            @if ($errors->has('email'))
                            <span class="invalid-feedback d-block">{{ $errors->first('email') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            {{ Form::label('phone', 'Phone', ['class' => 'col-form-label']) }}
                        </td>
                        <td>
                            <div class="input-group">
                                {{ Form::text('phone', old('phone', $record->phone), ['class' => 'form-control'.($errors->has('phone') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-mobile"></i>
                                    </span>
                                </div>
                            </div>
                            @if ($errors->has('phone'))
                            <span class="invalid-feedback d-block">{{ $errors->first('phone') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            {{ Form::label('phone_remarks', 'Phone Remarks', ['class' => 'col-form-label']) }}
                        </td>
                        <td>
                            {{ Form::textarea('phone_remarks', old('phone_remarks', $record->phone_remarks), ['class' => 'form-control'.($errors->has('phone_remarks') ? ' is-invalid' : ''), 'placeholder' => '', 'rows' => 2]) }}
                            @if ($errors->has('phone_remarks'))
                            <span class="invalid-feedback d-block">{{ $errors->first('phone_remarks') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            {{ Form::label('line', 'LINE', ['class' => 'col-form-label']) }}
                        </td>
                        <td>
                            <div class="input-group">
                                {{ Form::text('line', old('line', $record->line), ['class' => 'form-control'.($errors->has('line') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fab fa-line"></i>
                                    </span>
                                </div>
                            </div>
                            @if ($errors->has('line'))
                            <span class="invalid-feedback d-block">{{ $errors->first('line') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            {{ Form::label('wechat', 'WeChat', ['class' => 'col-form-label']) }}
                        </td>
                        <td>
                            <div class="input-group">
                                {{ Form::text('wechat', old('wechat', $record->wechat), ['class' => 'form-control'.($errors->has('wechat') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fab fa-weixin"></i>
                                    </span>
                                </div>
                            </div>
                            @if ($errors->has('wechat'))
                            <span class="invalid-feedback d-block">{{ $errors->first('wechat') }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
                <h4>Tags</h4>
                <table class="table table-striped table-sm">
                    <tr class="form-group">
                        <td style="width:200px;">
                            {{ Form::label('campaigns', 'Campaigns', ['class' => 'col-form-label']) }}
                        </td>
                        <td class="align-middle">
                            @if ($record->campaignsAuthUserCanView()->count() > 0)
                            @foreach ($record->campaignsAuthUserCanView()->get() as $campaign)
                            <div><a href="{{ $campaign->getPath() }}">{{ $campaign->name }}</a></div>
                            @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td style="width:200px;">
                            {{ Form::label('affiliations', 'Affiliations', ['class' => 'col-form-label']) }}
                        </td>
                        <td>
                            {{ Form::text('affiliations', old('affiliations', $record->affiliationsDisplayForSelect), ['class' => 'form-control affiliations'.($errors->has('affiliations') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            @if ($errors->has('affiliations'))
                            <span class="invalid-feedback d-block">{{ $errors->first('affiliations') }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-5">
                <h4>Description</h4>
                <div class="form-group">
                    <label for="description_ppt" class="col-form-label">
                        PowerPoint description in
                        {{ Form::select('language', $languages->mapWithKeys(function ($language) {
                                return [$language['iso_3166_2'] ? $language['iso_639_1'].'_'.$language['iso_3166_2'] : $language['iso_639_1'] => $language['name']];
                            }), old('language', 'en'), ['class' => ($errors->has('language') ? 'is-invalid' : ''), 'id' => 'description_ppt']) }}
                        <sup class="text-danger"><small>&#10033;</small></sup>
                        @if ($errors->has('language'))
                        <span class="invalid-feedback d-block">{{ $errors->first('language') }}</span>
                        @endif
                    </label>
                    {{ Form::textarea('description_ppt', old('description_ppt', $record->getDescriptionPpt()), ['class' => 'form-control'.($errors->has('description_ppt') ? ' is-invalid' : ''), 'placeholder' => '', 'rows' => 5, 'maxlength' => 240]) }}
                    <small class="form-text text-muted word-counter">
                        {{ strlen(old('description_ppt', $record->getDescriptionPpt())) }} out of 240 characters
                    </small>
                    @if ($errors->has('description_ppt'))
                    <span class="invalid-feedback d-block">{{ $errors->first('description_ppt') }}</span>
                    @endif
                </div>
                @include('components.recordEditInstagram')
                @include('components.recordEditYouTube')
                @include('components.recordEditFacebook')
                @include('components.recordEditTwitter')
                @include('components.recordEditTikTok')
                @include('components.recordEditWeibo')
                @include('components.recordEditXiaohongshu')
                <h4>Blog</h4>
                <div class="form-group">
                    <div class="input-group">
                        {{ Form::text('blog_url', old('blog_url', $record->blog_url), ['class' => 'form-control'.($errors->has('blog_url') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                        @if ($record->blog_url)
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <a target="_blank" href="{{ $record->blog_url }}">
                                    <i class="fas fa-link"></i>
                                </a>
                            </span>
                        </div>
                        @else
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fas fa-link"></i>
                            </span>
                        </div>
                        @endif
                    </div>
                    @if ($errors->has('blog_url'))
                    <span class="invalid-feedback d-block">{{ $errors->first('blog_url') }}</span>
                    @endif
                </div>
                @if ($record->blog_url)
                <table class="table table-striped table-sm">
                    <tr class="form-group">
                        <td style="width:200px;">{{ Form::label('blog_external_rate_post', 'Internal Rate Blog', ['class' => 'col-form-label']) }}</td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                {{ Form::text('blog_external_rate_post', old('blog_external_rate_post', $record->blog_external_rate_post), ['class' => 'form-control'.($errors->has('blog_external_rate_post') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            </div>
                            @if ($errors->has('blog_external_rate_post'))
                            <span class="invalid-feedback d-block">{{ $errors->first('blog_external_rate_post') }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
                @endif
                <h4>Wikipedia</h4>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">wikipedia.org/wiki/</span>
                        </div>
                        {{ Form::text('wikipedia_id', old('wikipedia_id', $record->wikipedia_id), ['class' => 'form-control'.($errors->has('wikipedia_id') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                        @if ($record->wikipedia_id)
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <a target="_blank" href="https://en.wikipedia.org/wiki/{{ $record->wikipedia_id }}"><i class="fab fa-wikipedia-w"></i></a>
                            </span>
                        </div>
                        @else
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fab fa-wikipedia-w"></i>
                            </span>
                        </div>
                        @endif
                    </div>
                    @if ($errors->has('wikipedia_id'))
                    <span class="invalid-feedback d-block">{{ $errors->first('wikipedia_id') }}</span>
                    @endif
                </div>
            </div>
            <div class="col-lg-2">
                <h4>Photos</h4>
                <div class="form-group">
                    <p>
                        <img class="img-fluid profile-photo"
                            src="{{ old('photo_default') ? $record->{old('photo_default').'_photo_url'} : url($record->display_photo) }}" alt="" />
                    </p>
                    <div class="overflow-hidden">{{ Form::file('photo') }}</div>
                    @if ($errors->has('photo'))
                    <span class="invalid-feedback d-block">{{ $errors->first('photo') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    {{ Form::label('photo', 'Select Default Photo', ['class' => 'col-form-label']) }}
                    {{ Form::hidden('photo_default', old('photo_default', $record->photo_default), ['id' => 'photo_default']) }}
                    <div class="row">
                        <div class="col-4">
                            <img class="img-fluid profile-photo photo-select<?php if ( ! $record->photo_default) echo ' photo-selected'; ?>"
                                src="{{ $record->photo_url }}" alt="GC" />
                            <div class="text-center"><small>GC</small></div>
                        </div>
                        @foreach($socialMediaPhotos as $photo)
                        <div class="col-4">
                            <img class="img-fluid profile-photo photo-select<?php if (old('photo_default', $record->photo_default) === $photo['type']) echo ' photo-selected'; ?>"
                                src="{{ $record->{$photo['photoAttribute']} }}" alt="{{ $photo['tag'] }}" />
                            <div class="text-center"><small>{{ $photo['tag'] }}</small></div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <p>Created on {{ $record->created_at->format('d M Y, H:ia') }} and last updated {{ $record->updated_at->format('d M Y, H:ia') }}</p>
                <p>
                    <span class="btn btn-sm btn-outline-danger btn-delete" data-toggle="modal" data-target="#recordDeleteModal"
                        data-url="{{ url('records/'.$record->id) }}">Delete Influencer</span>
                </p>
                @can('socapi', $record)
                @if ($record->instagramSocapiData)
                <h3>Socapi</h3>
                <p>Last updated on {{ $record->instagram_socapi_updated_at->format('d M Y, H:ia') }}</p>
                {{ Html::link('records/'.$record->id.'/socapi', 'View Data', ['class' => 'btn btn-sm btn-outline-primary', 'target' => '_blank']) }}
                @endif
                @endcan
            </div>
        </div>
        @if ($record->instagramSocapiData)
        @include('recordEdit.instagramSocialData')
        @endif
    </div>
</div>
{{ Form::close() }}
@include('components.recordDeleteModal')
@endsection

@section('script')
<div>
    <script>
        $('#recordDeleteModal').on('show.bs.modal', function (e) {
                $('#recordDeleteModal form').attr('action', $('.btn-delete').attr('data-url'));
            });
        $(".alert").alert();
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.profile-photo:first').attr('src', e.target.result);
                    $('.photo-select:first').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $('.profile-photo:first').on('click', function(e) {
            $("input[type=file]").click();
        });
        $('input[type=file]').change(function() {
            readURL(this);
        });
        var languageSelect = $('select[name=language]');
        var description = `{{ $record->getDescriptionPpt() }}`;
        var language = languageSelect.val();
        languageSelect.change(function() {
            var textarea = $('textarea[name=description_ppt]');
            if (description != textarea.val()) {
                if (!confirm("You will lose unsave description changes! Are you sure you?")) {
                    $(languageSelect).val(language);
                    return;
                }
            }
            textarea.val('Loading...');
            axios.get('{{ url('records/'.$record->id.'/edit') }}?language='+$(languageSelect).val()).then(function(response) {
                textarea.val(response.data);
                description = textarea.val();
                language = $(languageSelect).val();
                updateCount(textarea);
            });
        });
        $('textarea').on("input", function() {
            updateCount(this);
        });
        function updateCount(obj) {
            var maxlength = $(obj).attr("maxlength");
            var currentLength = $(obj).val().length;
            $(obj).closest('.form-group').find('.word-counter')
                .text(currentLength+' out of '+maxlength+' characters');
        }
        $('.photo-select').on('click', function(e) {
            var obj = this;
            $('.photo-select').each(function(index) {
                $(this).removeClass('photo-selected');
                $(obj).addClass('photo-selected');
            });
            $('.profile-photo:first').attr('src', $(obj).attr('src'));
            $('#photo_default').val('');
            if ($('.photo-select').index(obj) == 1) $('#photo_default').val('instagram');
            if ($('.photo-select').index(obj) == 2) $('#photo_default').val('youtube');
            if ($('.photo-select').index(obj) == 3) $('#photo_default').val('facebook');
            if ($('.photo-select').index(obj) == 4) $('#photo_default').val('twitter');
            if ($('.photo-select').index(obj) == 5) $('#photo_default').val('tiktok');
            if ($('.photo-select').index(obj) == 6) $('#photo_default').val('weibo');
            if ($('.photo-select').index(obj) == 7) $('#photo_default').val('xiaohongshu');
        });
        $(function() {
            $('.interests').selectize({
                options: {!! json_encode($interests) !!},
                delimiter: '|',
                create: false,
                persist: true
            });
            $('.professions').selectize({
                options: {!! json_encode($professions) !!},
                delimiter: '|',
                create: false,
                persist: true
            });
            $('.affiliations').selectize({
                options: {!! json_encode($affiliations) !!},
                delimiter: ':: ',
                create: false,
                persist: true
            });
        });
    </script>
</div>
@endsection
