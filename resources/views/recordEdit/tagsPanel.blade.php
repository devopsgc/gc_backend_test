<div class="card my-2">
    <div class="card-header">
        <h5 class="m-0">{{ $title }}</h5>
    </div>
    <div class="card-body">
        @forelse($data as $data)
        <span class="badge badge-{{ $labelType }}">{{ $data[$key] }}</span>
        @empty
        No Data.
        @endforelse
    </div>
</div>