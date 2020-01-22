@php $lastCampaign = \App\Models\Campaign::withTrashed()->orderBy('id', 'desc')->first() @endphp
<div class="modal" id="saveAndCreateProposalNameModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proposal Create</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="name" class="control-label">Name</label>
                {{ Form::text('name', old('name'), [
                    'class' => 'form-control'.( $errors->has('name') ? ' is-invalid' : ''),
                    'required' => '',
                    'placeholder' =>  'default: Proposal '.( $lastCampaign ? $lastCampaign->id + 1 : 1)]) }}
                @if ($errors->has('name'))
                <span class="invalid-feedback d-block">{{ $errors->first('name') }}</span>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="handleDelete" class="btn btn-primary saveAndCreateProposalNameModalSubmit">Save</button>
            </div>
        </div>
    </div>
</div>
