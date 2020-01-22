<div class="row">
    <div class="col-md-12">
        @forelse ($campaigns as $campaign)
        <div class="py-3 px-2 border-top">
            <div class="d-flex justify-content-between">
                <div class="d-flex justify-content-start">
                    <div class="mr-2">
                        <i class="far fa-file-alt"></i>
                    </div>
                    <div>
                        @if ($campaign->isDraft())
                        <a href='{{ url('/campaigns/'.$campaign->id.'/shortlist') }}'>{{ $campaign->name }}</a>
                        @else
                        <a href='{{ url('/campaigns/'.$campaign->id) }}'>{{ $campaign->name }}</a>
                        @endif
                        <div>
                            <small class="text-muted">
                                {{ 'Created ' . $campaign->created_at->format('d M Y') . ' by ' . (isset($campaign->createdBy) ? $campaign->createdBy->full_name : '-') . ' ' }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <div class="d-flex align-items-center">
                        @if ($campaign->isDraft())
                        <a class="btn btn-primary" href="{{ url('/campaigns/'.$campaign->id.'/edit') }}">
                            Create Campaign
                        </a>
                        <a class="mx-3" href="#" data-toggle='modal' data-target='#campaignDeleteModal' data-id="{{ $campaign->id }}">
                            <i class="far fa-trash-alt" style="color:#bf5329;"></i>
                        </a>
                        @else
                        <a class="btn btn-primary" href="{{ url('/campaigns/'.$campaign->id.'/links') }}">
                            Report Analysis
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        @include('campaign.index.notFound')
        @endforelse
    </div>
</div>
