@if (count($campaign->getValidNextStatuses()) == 0)
<button type="button" class="btn btn-default" disabled>
    {{ $campaign->status }}
</button>
@else
<div class="dropdown">
    <button class="btn btn-{{ \App\Helpers\CampaignStatusHelper::getBtnStyle($campaign->status) }}
        dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{ $campaign->status }}
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        @foreach ($campaign->getValidNextStatuses() as $key => $status)
        @if ($status == \App\Models\Campaign::STATUS_DELETED && count($campaign->getValidNextStatuses()) > 1)
        <div class="dropdown-divider"></div>
        @endif
        @if ($status == App\Models\Campaign::STATUS_DELETED)
        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#campaignDeleteModal" data-id="{{ $campaign->id }}" style="color: #a94442;'">
            {{ $status }}
        </a>
        @else
        <a class="dropdown-item" href="#" onclick="javascript:updateStatus({{ $campaign->id }},'{{ $status }}')">
            {{ $status }}
        </a>
        @endif
        @endforeach
    </div>
</div>
@endif