<div class="modal" id="campaignExitModal" tabindex="-1" role="dialog">
    {{ Form::open(['url' => 'campaigns/shortlist/exit', 'method' => 'POST']) }}
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exit {{ isset($campaign) && $campaign->isDraft() ? 'Proposal' : 'Campaign'}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to exit this {{ isset($campaign) && $campaign->isDraft() ? 'proposal' : 'campaign'}}? Any unsaved changes will be
                    lost.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="handleDelete" class="btn btn-primary">Yes, Exit</button>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>
