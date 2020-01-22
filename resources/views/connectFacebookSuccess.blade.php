@extends('layouts.app')

@section('style')
<style>
    table { table-layout: fixed; width: 100% }
    td { overflow-wrap: break-word; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row wrap">
        <div class="col-md-12">
            <h2>{{ $user->first_name.' '.$user->last_name }}</h2>
            @foreach($pages as $page)
            <div class="row">
                <div class="col-md-6">
                    <h3>Page Info</h3>
                    <table class="table table-striped table-condensed">
                        <tr>
                            <td class="w-50 text-right">Name</td>
                            <td>{{ $page->name }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Photo</td>
                            <td><img src="{{ $page->picture->url }}" alt="" /></td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">ID</td>
                            <td>{{ $page->id }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Username</td>
                            <td>{{ $page->username ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Link</td>
                            <td><a href="{{ $page->link ?? '-' }}">{{ $page->link ?? '-' }}</a></td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">About</td>
                            <td>{{ $page->about ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Fan Count</td>
                            <td>{{ $page->fan_count ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Rating Count</td>
                            <td>{{ $page->rating_count ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Engagement</td>
                            <td>{{ $page->engagement->count }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Emails</td>
                            <td>{{ isset($page->emails) ? join(', ', $page->emails) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Phone</td>
                            <td>{{ $page->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Country</td>
                            <td>{{ isset($page->location->country) ? $page->location->country : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">State</td>
                            <td>{{ isset($page->location->state) ? $page->location->state : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">City</td>
                            <td>{{ isset($page->location->city) ? $page->location->city : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Latitude</td>
                            <td>{{ isset($page->location->latitude) ? $page->location->latitude : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Longitude</td>
                            <td>{{ isset($page->location->longitude) ? $page->location->longitude : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Street</td>
                            <td>{{ isset($page->location->street) ? $page->location->street : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Zip</td>
                            <td>{{ isset($page->location->zip) ? $page->location->zip : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 text-right">Categories</td>
                            <td>{{ join(', ', array_map(function($category) { return $category->name; }, $page->category_list)) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h3>Page Insights</h3>
                    <table class="table table-striped table-condensed">
                        @foreach($page->insights as $insight)
                        <tr>
                            <td class="w-50 text-right"><span class="helptip" data-toggle="tooltip" data-placement="right" title="{{ $insight->getField('description') }}">{{ $insight->getField('title') }}</span></td>
                            <td>
                                @foreach($insight->getField('values') as $insightValue)
                                {{ number_format($insightValue->getField('value')) }} <small class="text-muted">({{ $insightValue->getField('end_time')->format('d M Y h:ia') }})</small>
                                <br />
                                @endforeach
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @foreach ($page->posts as $index => $post)
                    @if ($index)
                        @continue
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <h3>Post Info</h3>
                            <table class="table table-striped table-condensed" style="table-layout: fixed; width: 100%">
                                <tr>
                                    <td class="w-50 text-right">Created Date</td>
                                    <td>{{ date('d M Y h:ia', strtotime($post->created_time->date)) }}</td>
                                </tr>
                                <tr>
                                    <td class="w-50 text-right">Post ID</td>
                                    <td>{{ $post->id }}</td>
                                </tr>
                                <tr>
                                    <td class="w-50 text-right">Link</td>
                                    <td><a href="{{ $post->permalink_url ?? '-' }}">{{ $post->permalink_url ?? '-' }}</a></td>
                                </tr>
                                @if (isset($post->message))
                                <tr>
                                    <td class="w-50 text-right">Message</td>
                                    <td>{{ $post->message }}</td>
                                </tr>
                                @endif
                                @if (isset($post->story))
                                <tr>
                                    <td class="w-50 text-right">Story</td>
                                    <td>{{ $post->story }}</td>
                                </tr>
                                @endif
                                @if (isset($post->picture))
                                <tr>
                                    <td class="w-50 text-right">Photo</td>
                                    <td><img src="{{ $post->picture }}" alt="" /></td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="w-50 text-right">Shares</td>
                                    <td>{{ isset($post->shares) ? $post->shares->count : 0 }}</td>
                                </tr>
                                <tr>
                                    <td class="w-50 text-right">Comments</td>

                                    <td>{{ isset($post->comments) ? count($post->comments) : 0 }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h3>Post Insights</h3>
                            <table class="table table-striped table-condensed">
                                @foreach($post->insights as $insight)
                                <tr>
                                    <td class="w-50 text-right"><span class="helptip" data-toggle="tooltip" data-placement="right" title="{{ $insight->getField('description') }}">{{ $insight->getField('title') }}</span></td>
                                    <td>
                                        @foreach($insight->getField('values') as $insightValue)
                                        {{ number_format($insightValue->getField('value')) }}
                                        <br />
                                        @endforeach
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$('[data-toggle="tooltip"]').tooltip();
</script>
@endsection
