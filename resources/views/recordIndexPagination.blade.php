<div class="row">
    <div class="col-md-12">
        <p>
            @if ($records->total())
            Showing {{ ($records->currentpage()-1)*$records->perpage()+1 }} to {{ ($records->currentpage()*$records->perpage()) > $records->total() ? $records->total() : $records->currentpage()*$records->perpage() }} of {{ $records->total() }} records
            @endif
            <button type="submit" name="submit" value="xls" class="btn btn-link btn-sm">
                <div class="d-flex">
                    <i class="fas fa-download"></i>
                    <div class="text-nowrap ml-2">Download xls (up to 2000)</div>
                </div>
            </button>
        </p>
    </div>
    <div class="col-md-12 record-links">
        {{ $records->links() }}
    </div>
</div>
