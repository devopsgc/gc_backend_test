<div class="modal" id="deliverableModal" tabindex="-1" role="dialog">
    {{ Form::open(['url' => '', 'method' => 'put']) }}
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="handleDelete" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>