<div class="modal" id="campaignDeleteModal" tabindex="-1" role="dialog">
    {{ Form::open(['url' => 'campaigns/'.$campaignId.'/status', 'method' => 'DELETE']) }}
    <input type="hidden" name="redirect_url" id="redirect_url" value="{{ isset($redirectUrl) ? $redirectUrl : '' }}" />
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Campaign</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this campaign?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="handleDelete" class="btn btn-primary">Yes, Delete</button>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>