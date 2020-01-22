@if ($campaigns->total())
<div class="row mb-3">
    <div class="col-md-12">
        Showing {{ ($campaigns->currentpage()-1)*$campaigns->perpage()+1 }} to
        {{ ($campaigns->currentpage()*$campaigns->perpage()) > $campaigns->total() ? $campaigns->total() : $campaigns->currentpage()*$campaigns->perpage() }}
        of {{ $campaigns->total() }} campaigns
    </div>
    <div class="col-md-12">
        {{ $campaigns->appends([
            'filter_tab' => request('filter_tab'),
            'filter' => request('filter'),
            'q' => request('q'),
            'category' => request('category'),
            'start_at' => request('start_at'),
            'end_at' => request('end_at'),
            'country_code' => request('country_code'),
            'status' => request('status'),
        ])->links() }}
    </div>
</div>
@endif