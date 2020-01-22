<div>
    @foreach (App\Models\Deliverable::PLATFORMS as $platform)
    <div class="d-flex justify-content-center align-items-center flex-wrap">
        @foreach ($campaignDeliverables->where('platform', $platform) as $deliverable)
        @foreach ($deliverable->links as $link)
        @if ($link->url)
        <div class="m-2">
            @if ($deliverable->platform === 'Facebook')
            {{-- <div class="row"> --}}
                <div class="col-md-6s">
                    <div class="{{ $deliverable->type == 'Post' ? 'fb-post' : 'fb-video'}}" data-href="{{ $link->url }}"
                        data-width="{{ \App\Models\Link::EMBEDDED_WIDTH }}" data-show-text="true"></div>
                </div>
                {{-- <div class="col-md-6">
                    <h4>Performance</h4>
                    <table class="table table-striped table-condensed">
                        @php
                        $fields = $link->getProperties();
                        $insights = $link->getInsights();
                        @endphp
                        @if ($link->deliverable->record->facebook_page_access_token && $fields && $insights && ($link->isFacebookPost() || $link->isFacebookPhoto() || $link->isFacebookVideo()))
                        <tr>
                            <td>
                                <span class="helptip" data-toggle="tooltip" data-placement="right"
                                    title="{{ $insights['post_impressions']->getField('description') }}">No. of impressions
                                </span>
                            </td>
                            <td>
                                {{ $insights['post_impressions']->getField('values')[0]->getField('value') }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="helptip" data-toggle="tooltip" data-placement="right"
                                    title="{{ $insights['post_clicks']->getField('description') }}">No. of clicks
                                </span>
                            </td>
                            <td>
                                {{ $insights['post_clicks']->getField('values')[0]->getField('value') }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="helptip" data-toggle="tooltip" data-placement="right"
                                    title="{{ $insights['post_reactions_like_total']->getField('description') }}">No. of likes (Lifetime)
                                </span>
                            </td>
                            <td>
                                {{ $insights['post_reactions_like_total']->getField('values')[0]->getField('value') }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                No. of likes
                            </td>
                            <td>
                                {{ $fields['likes']['summary']['total_count'] }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                No. of shares
                            </td>
                            <td>
                                {{ array_key_exists('shares', $fields) ? $fields['shares']['count'] : 0 }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                No. of comments
                            </td>
                            <td>
                                {{ $fields['comments']['summary']['total_count'] }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="helptip" data-toggle="tooltip" data-placement="right"
                                    title="{{ $insights['post_impressions_unique']->getField('description') }}">People reached
                                </span>
                            </td>
                            <td>
                                {{ $insights['post_impressions_unique']->getField('values')[0]->getField('value') }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="helptip" data-toggle="tooltip" data-placement="right"
                                    title="{{ $insights['post_activity']->getField('description') }}">Total reactions + comments + shares (Lifetime)
                                </span>
                            </td>
                            <td>
                                {{ $insights['post_activity']->getField('values')[0]->getField('value') }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="helptip" data-toggle="tooltip" data-placement="right"
                                    title="{{ $insights['post_activity']->getField('description') }} + {{ $insights['post_clicks']->getField('description') }}">Total engagement
                                </span>
                            </td>
                            <td>
                                {{ $insights['post_activity']->getField('values')[0]->getField('value') + $insights['post_clicks']->getField('values')[0]->getField('value') }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                URL
                            </td>
                            <td>
                                <div style="width: 400px; overflow-wrap: break-word; text-align: center;">
                                    <a href="{{ $fields['permalink_url'] }}">{{ $fields['permalink_url'] }}</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Live date
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($fields['created_time'])->format('d M Y, H:ia') }}<br>
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="2">Cannot get stats</td>
                        </tr>
                        @endif
                    </table>
                </div> --}}
            {{-- </div> --}}
            @elseif ($deliverable->platform === 'Instagram')
            {{-- <div class="row"> --}}
                <div class="col-md-6s">
                    {!! $link->getInstagramEmbedHtmlCode() !!}
                </div>
                {{-- <div class="col-md-6">
                    <h4>Performance</h4>
                    <table class="table table-striped table-condensed">
                        @php
                        $fields = $link->getInstagramPostProperties();
                        $insights = $link->getInstagramInsights();
                        @endphp
                        @if ($link->deliverable->record->facebook_page_access_token && $fields && $insights && !array_key_exists('error', $insights))
                        <tr>
                            <td>
                                <span class="helptip" data-toggle="tooltip" data-placement="right"
                                    title="{{ $insights['impressions']->getField('description') }}">No. of impressions
                                </span>
                            </td>
                            <td>
                                {{ $insights['impressions']->getField('values')[0]->getField('value') }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                No. of clicks
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                No. of likes
                            </td>
                            <td>
                                {{ $fields['like_count'] }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                No. of saves/shares
                            </td>
                            <td>
                                {{ $insights['saved']->getField('values')[0]->getField('value') }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                No. of comments
                            </td>
                            <td>
                                {{ $fields['comments_count'] }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="helptip" data-toggle="tooltip" data-placement="right"
                                    title="{{ $insights['engagement']->getField('description') }}">Total engagement
                                </span>
                            </td>
                            <td>
                                {{ $insights['engagement']->getField('values')[0]->getField('value') }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="helptip" data-toggle="tooltip" data-placement="right"
                                    title="{{ $insights['reach']->getField('description') }}">People reached
                                </span>
                            </td>
                            <td>
                                {{ $insights['reach']->getField('values')[0]->getField('value') }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                URL
                            </td>
                            <td>
                                <div style="width: 400px; overflow-wrap: break-word; text-align: center;">
                                    <a href="{{ $fields['permalink'] }}">{{ $fields['permalink'] }}</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Live date
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($fields['timestamp'])->format('d M Y, H:ia') }}<br>
                            </td>
                        </tr>
                        @elseif (array_key_exists('error', $insights))
                        <tr>
                            <td colspan="2">{{ $insights['error'] }}</td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="2">Cannot get stats</td>
                        </tr>
                        @endif
                    </table>
                </div> --}}
            {{-- </div> --}}
            @elseif ($deliverable->platform === 'Twitter')
            {!! $link->getTwitterEmbedHtmlCode() !!}
            @elseif ($deliverable->platform === 'YouTube')
            <iframe id="ytplayer" type="text/html" src="{{ $link->getYoutubeEmbedUrl() }}" frameborder="0"></iframe>
            @else
            Platform not supported yet: {{ $link->url }}
            @endif
        </div>
        @endif
        @endforeach
        @endforeach
    </div>
    @endforeach
</div>