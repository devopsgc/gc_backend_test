<div class="modal" id="recordDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        {{ Form::open(['url' => '', 'method' => 'delete']) }}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Influencer</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this influencer?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>