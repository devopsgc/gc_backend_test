<div class="modal" id="recordRemoveModal" tabindex="-1">
    <div class="modal-dialog">
        {!! Form::open(['url' => '', 'method' => 'delete']) !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Remove Influencer</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove this influencer?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, Remove</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>