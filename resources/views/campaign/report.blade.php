@extends('layouts.app')

@section('style')
<style>
    /* .influencer img { width:70px; margin-right:10px; border-radius:50px; }
.table-striped th { background:#000; color:#fff; text-align:center; border:1px solid #fff; vertical-align: middle !important; }
.table-record-deliverable { margin-bottom: 5px; background-color: transparent !important; }
.table-record-deliverable td { border-top-width: 0px !important; line-height: 1rem !important; vertical-align: baseline !important }
.deliverables span img { width:20px; }
.deliverables table td:first-child { width: 1%; white-space: nowrap; }
.table-deliverable { margin:0; }
.table-deliverable select { width:100%; height: 36px; background-color: #fff; }
.table-deliverable input { width:100%; border-radius:5px; padding:5px; }
.table-deliverable td { border: none !important; }
.table { background-color: inherit !important; }
input::placeholder { opacity:0.5 !important;} */
    .table td {
        padding: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    @include('components.messageAlert')
    @include('components.warningAlert')

    {{ Form::open(['url' => $campaign->getPath().'/links', 'method' => 'post']) }}
    <div class="row mb-3">
        <div class="col-md-8">
            <h2>{{ $campaign->name }} | Social Media Tracking</h2>
        </div>
        <div class="col-md-4 text-right">
            <h2>{{ $campaign->brand }}</h2>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0" width="100%" style="min-width:1200px;">
            <thead>
                <tr>
                    <th class="text-center align-middle" width="20%">Influencer</th>
                    <th class="text-center align-middle" width="40%">Postings / Link Tracking</th>
                    <th class="text-center align-middle" width="40%">Postings / Link Tracking (Value Added)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($campaign->records as $record)
                {{ Form::hidden('selected[]', $record->id) }}
                <tr>
                    <td>
                        <div class="d-flex">
                            <div class="mr-2">
                                @can('update', Auth::user(), App\Models\Record::class)
                                <a class="openRecordModal" href="{{ url('records/'.$record->id.'/edit') }}">
                                    <img class="rounded-circle img-fluid icon-lg" src="{{ $record->display_photo }}"
                                        alt="{{ $record->name }}'s Profile Photo" />
                                </a>
                                @else
                                <img class="rounded-circle img-fluid icon-lg" src="{{ $record->display_photo }}"
                                    alt="{{ $record->name }}'s Profile Photo" />
                                @endcan
                            </div>
                            <div class="d-flex flex-column justify-content-center flex-fill">
                                <div>
                                    <div>
                                        @can('update', Auth::user(), App\Models\Record::class)
                                        <strong>{{ Html::link('records/'.$record->id.'/edit', $record->name, ['class' => 'openRecordModal']) }}</strong>
                                        @else
                                        <strong>{{ $record->name }}</strong>
                                        @endcan
                                    </div>
                                    @if ($record->second_name)
                                    <small>{{ $record->second_name }}</small>
                                    @endif
                                    @if ($record->interestsDisplay)
                                    <small>{{ $record->interestsDisplay }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            @forelse ($campaign->deliverables()->where('record_id', $record->id)->get() as $deliverable)
                            <div>
                                @for ($deliverableLinks = 0; $deliverableLinks < $deliverable->quantity; $deliverableLinks++)
                                    @php $uniqueId = 'url.'.$deliverable->id.'.'.$deliverableLinks @endphp
                                    <div class="d-flex align-items-center justify-content-center w-100 flex-wrap">
                                        <div class="m-1">
                                            {{ '1 x ' }}
                                            {{ Html::image('img/icon-'.strtolower($deliverable->platform).'.png', $deliverable->platform, ['class' => 'icon-sm']) }}
                                        </div>
                                        <div class="m-1 flex-fill">
                                            {{ Form::text('url['.$deliverable->id.'][]',
                                                old('url['.$deliverable->id.']['.$deliverableLinks.']', isset($deliverable->links->toArray()[$deliverableLinks]) ? $deliverable->links->toArray()[$deliverableLinks]['url'] : ''),
                                                ['class' => 'form-control'.($errors->has($uniqueId) ? ' is-invalid' : '')]) }}
                                            @error($uniqueId)<span class="invalid-feedback d-block">{{ $errors->first($uniqueId) }}</span>@enderror
                                        </div>
                                    </div>
                                    @endfor
                            </div>
                            @empty
                            <div class="d-flex justify-content-center">
                                No Links.
                            </div>
                            @endforelse
                        </div>
                    </td>
                    <td>
                        <?php
                            $added_url = [];
                            $added_deliverables = [];
                            $valueAddeds = $campaign->valueAddedPosts->where('record_id', $record->id);
                            $added_url[$record->id] = old('added_url.'.$record->id) ?:
                                $valueAddeds->map(function ($valueAdded) { return $valueAdded->links[0]->url; })->toArray();
                            $added_deliverables[$record->id] = old('added_deliverables.'.$record->id) ?:
                                $valueAddeds->map(function ($valueAdded) {
                                    return strtolower($valueAdded['platform']).'_'.strtolower($valueAdded['type']);
                                })->toArray();
                        ?>
                        <div class="d-flex flex-column align-items-center justify-content-center container-add-link">
                            <div class="list-add-link w-100">
                                @if ($added_url[$record->id])
                                @foreach ($added_url[$record->id] as $key => $valueAddedLink)
                                <div class="d-flex align-items-top justify-content-center w-100 flex-wrap item-add-link">
                                    <div class="m-1">
                                        <select class="form-control" name="added_deliverables[{{ $record->id }}][]">
                                            <option value="facebook_post"
                                                {{ $added_deliverables[$record->id][$key] == "facebook_post" ? ' selected="selected"' : '' }}>Facebook
                                                Post
                                            </option>
                                            <option value="facebook_video"
                                                {{ $added_deliverables[$record->id][$key] == "facebook_video" ? ' selected="selected"' : '' }}>Facebook
                                                Video</option>
                                            <option value="instagram_post"
                                                {{ $added_deliverables[$record->id][$key] == "instagram_post" ? ' selected="selected"' : '' }}>
                                                Instagram
                                                Post</option>
                                            <option value="instagram_video"
                                                {{ $added_deliverables[$record->id][$key] == "instagram_video" ? ' selected="selected"' : '' }}>
                                                Instagram
                                                Video</option>
                                            <option value="instagram_story"
                                                {{ $added_deliverables[$record->id][$key] == "instagram_story" ? ' selected="selected"' : '' }}>
                                                Instagram
                                                Story</option>
                                            <option value="youtube_video"
                                                {{ $added_deliverables[$record->id][$key] == "youtube_video" ? ' selected="selected"' : '' }}>YouTube
                                                Video
                                            </option>
                                            <option value="twitter_post"
                                                {{ $added_deliverables[$record->id][$key] == "twitter_post" ? ' selected="selected"' : '' }}>Twitter
                                                Post
                                            </option>
                                            <option value="twitter_video"
                                                {{ $added_deliverables[$record->id][$key] == "twitter_video" ? ' selected="selected"' : '' }}>Twitter
                                                Video
                                            </option>
                                        </select>
                                    </div>
                                    <div class="m-1 flex-fill">
                                        {{ Form::text('added_url['.$record->id.'][]', $added_url[$record->id][$key], ['class' => 'form-control']) }}
                                        @error('added_url.'.$record->id.'.'.$key)
                                        <span class="invalid-feedback d-block">{{ $errors->first('added_url.'.$record->id.'.'.$key) }}</span>
                                        @enderror
                                    </div>
                                    <div class="m-1">
                                        <button type="button" class="btn btn-default btn-sm btn-delete-link">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                            <div class="template-add-link d-none">
                                <div class="d-flex align-items-center justify-content-center w-100 flex-wrap item-add-link">
                                    <div class="m-1">
                                        {{ Form::select('added_deliverables['.$record->id.'][]', [
                                            'facebook_post' => 'Facebook Post',
                                            'facebook_video' => 'Facebook Video',
                                            'instagram_post' => 'Instagram Post',
                                            'instagram_video' => 'Instagram Video',
                                            'instagram_story' => 'Instagram Story',
                                            'youtube_video' => 'YouTube Video',
                                            'twitter_post' => 'Twitter Post',
                                            'twitter_video' => 'Twitter Video',
                                            ],
                                            null,
                                            ['class' => 'form-control', 'style' => 'width: auto;', 'disabled']) }}
                                    </div>
                                    <div class="m-1 flex-fill">
                                        {{ Form::text('added_url['.$record->id.'][]', '', ['class' => 'form-control', 'disabled']) }}
                                    </div>
                                    <div class="m-1">
                                        <button type="button" class="btn btn-default btn-sm btn-delete-link">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center my-2">
                                <button type="button" class="btn btn-success btn-sm btn-add-link">+ Add New Link</button>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-6 my-3">
            {{ Html::link($campaign->getPath(), 'Back to Campaign Overview', ['class' => 'btn btn-primary']) }}
        </div>
        <div class="col-md-6 my-3 text-md-right">
            {{ Html::link($campaign->getPath().'/links-tracking', 'Tracking Results', ['class' => 'btn btn-primary', 'id' => 'track-result-btn']) }}
            <button type="submit" class="btn btn-primary">Update Links</button>
        </div>
    </div>
    {{ Form::close() }}
</div>
@include('components.recordModal')
@endsection

@section('script')
@include('components.recordModalScript')
<script>
    $(document).on('click', '.btn-add-link', function(e) {
        var toCopyElement = $(this).closest('.container-add-link').find('.template-add-link').clone();
        $(toCopyElement).find('select').removeAttr('disabled');
        $(toCopyElement).find('input').removeAttr('disabled');
        $(this).closest('.container-add-link').find('.list-add-link').append($(toCopyElement).html());
    });
    $(document).on('click', '.btn-delete-link', function(e) {
        $(this).closest('.item-add-link').remove();
    });
    $(document).on('keyup', 'input', function(e) {
        $('#track-result-btn').addClass('disabled');
    });
</script>
@endsection