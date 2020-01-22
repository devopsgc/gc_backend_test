<div class="modal" id="updateProposalModal" tabindex="-1" role="dialog">
    {{ Form::open(['url' => 'campaigns/'.$campaign->id.'/update-name', 'method' => 'post']) }}
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="name" class="control-label">Name <sup class="text-red"><small>&#10033;</small></sup></label>
                {{ Form::text('name',
                    old('name', $campaign->name),
                    ['class' => 'form-control'.($errors->has('name') ? ' is-invalid' : ''), 'required' => '']) }}
                @error('name')<span class="invalid-feedback d-block">{{ $errors->first('name') }}</span>@enderror
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="handleDelete" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>