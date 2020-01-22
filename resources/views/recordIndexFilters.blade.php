<div class="filters">
    Countries
    <div class="row">
        <div class="col-md-12">
            <div class="wrap mb-3">
                @foreach ($countries as $country)
                <div class="form-check">
                    {{ Form::checkbox('country[]', $country->iso_3166_2, false, ['class' => 'form-check-input', 'id' => $country->iso_3166_2]) }}
                    {{ Form::label($country->iso_3166_2, $country->name, ['class' => 'form-check-label', 'title' => $country->name]) }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
    Gender
    <div class="row">
        <div class="col-md-12">
            <div class="wrap mb-3">
                <div class="form-check">
                    {{ Form::checkbox('gender[]', 'F', false, ['class' => 'form-check-input', 'id' => 'gender_female']) }}
                    {{ Form::label('gender_female', 'Female', ['class' => 'form-check-label', 'title' => 'Female']) }}
                </div>
                <div class="form-check">
                    {{ Form::checkbox('gender[]', 'M', false, ['class' => 'form-check-input', 'id' => 'gender_male']) }}
                    {{ Form::label('gender_male', 'Male', ['class' => 'form-check-label', 'title' => 'Male']) }}
                </div>
                <div class="form-check">
                    {{ Form::checkbox('gender[]', 'N', false, ['class' => 'form-check-input', 'id' => 'gender_non_binary']) }}
                    {{ Form::label('gender_non_binary', 'Non-binary', ['class' => 'form-check-label', 'title' => 'Non-binary']) }}
                </div>
            </div>
        </div>
    </div>
    Platforms
    <div class="row">
        <div class="col-md-12">
            @if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <div class="form-group">
                <div class="form-check">
                    {{ Form::checkbox('disabled', 'disabled', false, ['class' => 'form-check-input', 'id' => 'disabled']) }}
                    {{ Form::label('disabled', 'Show records with error', ['class' => 'form-check-label', 'title' => 'Show records with error']) }}
                </div>
            </div>
            @endif
            <div class="wrap mb-3">
                <div class="form-check">
                    {{ Form::checkbox('platform[]', 'instagram', false, ['class' => 'form-check-input', 'id' => 'instagram']) }}
                    {{ Form::label('instagram', 'Instagram', ['class' => 'form-check-label', 'title' => 'Instagram']) }}
                </div>
                <div class="form-check">
                    {{ Form::checkbox('platform[]', 'youtube', false, ['class' => 'form-check-input', 'id' => 'youtube']) }}
                    {{ Form::label('youtube', 'YouTube', ['class' => 'form-check-label', 'title' => 'YouTube']) }}
                </div>
                <div class="form-check">
                    {{ Form::checkbox('platform[]', 'facebook', false, ['class' => 'form-check-input', 'id' => 'facebook']) }}
                    {{ Form::label('facebook', 'Facebook', ['class' => 'form-check-label', 'title' => 'Facebook']) }}
                </div>
                <div class="form-check">
                    {{ Form::checkbox('platform[]', 'twitter', false, ['class' => 'form-check-input', 'id' => 'twitter']) }}
                    {{ Form::label('twitter', 'Twitter', ['class' => 'form-check-label', 'title' => 'Twitter']) }}
                </div>
                <div class="form-check">
                    {{ Form::checkbox('platform[]', 'tiktok', false, ['class' => 'form-check-input', 'id' => 'tiktok']) }}
                    {{ Form::label('tiktok', 'TikTok', ['class' => 'form-check-label', 'title' => 'TikTok']) }}
                </div>
                <div class="form-check">
                    {{ Form::checkbox('platform[]', 'weibo', false, ['class' => 'form-check-input', 'id' => 'weibo']) }}
                    {{ Form::label('weibo', 'Weibo', ['class' => 'form-check-label', 'title' => 'Weibo']) }}
                </div>
                <div class="form-check">
                    {{ Form::checkbox('platform[]', 'xiaohongshu', false, ['class' => 'form-check-input', 'id' => 'xiaohongshu']) }}
                    {{ Form::label('xiaohongshu', 'Xiaohongshu', ['class' => 'form-check-label', 'title' => 'Xiaohongshu']) }}
                </div>
                <div class="form-check">
                    {{ Form::checkbox('platform[]', 'miaopai', false, ['class' => 'form-check-input', 'id' => 'miaopai']) }}
                    {{ Form::label('miaopai', 'Miaopai', ['class' => 'form-check-label', 'title' => 'Miaopai']) }}
                </div>
                <div class="form-check">
                    {{ Form::checkbox('platform[]', 'yizhibo', false, ['class' => 'form-check-input', 'id' => 'yizhibo']) }}
                    {{ Form::label('yizhibo', 'Yizhibo', ['class' => 'form-check-label', 'title' => 'Yizhibo']) }}
                </div>
                <div class="form-check">
                    {{ Form::checkbox('platform[]', 'blog', false, ['class' => 'form-check-input', 'id' => 'blog']) }}
                    {{ Form::label('blog', 'Blog', ['class' => 'form-check-label', 'title' => 'Blog']) }}
                </div>
            </div>
        </div>
    </div>
    Affiliations
    <div class="row">
        <div class="col-md-12">
            <div class="wrap mb-3">
                @foreach ($affiliations as $index => $affiliation)
                <div class="form-check">
                    {{ Form::checkbox('affiliations[]', $affiliation, false, ['class' => 'form-check-input', 'id' => 'affiliations'.$index]) }}
                    {{ Form::label('affiliations'.$index, $affiliation, ['class' => 'form-check-label', 'title' => $affiliation]) }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
    Interest
    <div class="row">
        <div class="col-md-12">
            <div class="wrap mb-3">
                @foreach ($interests as $index => $interest)
                <div class="form-check">
                    {{ Form::checkbox('interests[]', $interest, false, ['class' => 'form-check-input', 'id' => 'interests'.$index]) }}
                    {{ Form::label('interests'.$index, $interest, ['class' => 'form-check-label', 'title' => $interest]) }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
    Profession
    <div class="row">
        <div class="col-md-12">
            <div class="wrap mb-3">
                @foreach ($professions as $index => $profession)
                <div class="form-check">
                    {{ Form::checkbox('professions[]', $profession, false, ['class' => 'form-check-input', 'id' => 'professions'.$index]) }}
                    {{ Form::label('professions'.$index, $profession, ['class' => 'form-check-label', 'title' => $profession]) }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <?php
        $minMaxFilters = [
            'Age' => 'age',
            'Facebook Followers' => 'facebook',
            'Instagram Followers' => 'instagram',
            'YouTube Subscribers' => 'youtube',
            'Weibo Followers' => 'weibo',
            'XiaoHongShu Followers' => 'xiaohongshu',
            'Miaopai Followers' => 'miaopai',
            'Yizhibo Followers' => 'yizhibo',
        ]
    ?>
    @foreach ($minMaxFilters as $label => $key)
    <div class="row min-max">
        <div class="col-12 min-max-label">
            {{ $label }}
        </div>
        <div class="col-6">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">Min.</div>
                </div>
                {{ Form::number($key.'_min', null, ['class' => 'form-control min-max-min']) }}
            </div>
        </div>
        <div class="col-6">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">Max.</div>
                </div>
                {{ Form::number($key.'_max', null, ['class' => 'form-control min-max-max']) }}
            </div>
        </div>
    </div>
    @endforeach
</div>