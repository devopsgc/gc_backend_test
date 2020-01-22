<div class="modal" id="recordClearModal" tabindex="-1">
    <div class="modal-dialog">
        {{ Form::open(['url' => 'campaigns/shortlist/remove-selection', 'method' => 'post']) }}
        <input type="hidden" name="all" value="1">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clear All Shortlisted Influencers</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to clear all shortlisted influencers?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, clear</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>