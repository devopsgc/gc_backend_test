{{ Form::open(['url' => 'campaigns', 'method' => 'get', 'id' => 'campaignForm']) }}
<input type="hidden" name="filter_tab" value="{{ Request::query('filter_tab') }}" />
<input type="hidden" name="page" value="{{ Request::query('page') }}" />
<div class="row">
    <div class="col-lg-4">
        <div class="row my-2">
            <div class="col-sm-12">
                {{ Form::text('q', Request::get('q'), ['class' => 'form-control', 'placeholder' => 'Search by campaign name or brand', 'style' => 'width: 100%']) }}
            </div>
        </div>
    </div>
    @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin() || Auth::user()->isManager())
    <div class="col-lg-4">
        <div class="row my-2">
            <div class="col-sm-3 d-flex align-items-center">
                {{ Form::label('filter', 'Filter By', ['class' => 'my-1']) }}
            </div>
            <div class="col-sm-9">
                {{ Form::select('filter', ['' => 'All', 'me' => 'Created By Me'], old('filter'), ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    @endif
    @if (Request::query('filter_tab') === 'campaigns' || Request::query('filter_tab') === 'completed')
    <div class="col-lg-4">
        <div class="row my-2">
            <div class="col-sm-3 d-flex align-items-center">
                {{ Form::label('country_code', 'Country', ['class' => 'my-1']) }}
            </div>
            <div class="col-sm-9">
                {{ Form::select('country_code', array_merge(['' => 'All'], $campaigns_countries), old('country_code'), ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="row my-2">
            <div class="col-sm-3 d-flex align-items-center">
                {{ Form::label('category', 'Category', ['class' => 'my-1']) }}
            </div>
            <div class="col-sm-9">
                {{ Form::text('category', old('category'), ['class' => 'form-control interests', 'placeholder' => '']) }}
            </div>
        </div>
    </div>
    @endif
    @if (Request::query('filter_tab') === 'campaigns')
    <div class="col-lg-4">
        <div class="row my-2">
            <div class="col-sm-3 d-flex align-items-center">
                {{ Form::label('status', 'Campaign Status', ['class' => 'my-1']) }}
            </div>
            <div class="col-sm-9">
                {{ Form::select('status', [
                    '' => 'All',
                    App\Models\Campaign::STATUS_ACCEPTED => App\Models\Campaign::STATUS_ACCEPTED,
                    App\Models\Campaign::STATUS_REJECTED => App\Models\Campaign::STATUS_REJECTED,
                    App\Models\Campaign::STATUS_CANCELLED => App\Models\Campaign::STATUS_CANCELLED,
                ], old('status'), ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    @endif
    @if (Request::query('filter_tab') === 'campaigns' || Request::query('filter_tab') === 'completed')
    <div class="col-lg-4">
        <div class="row my-2">
            <div class="col-sm-3 d-flex align-items-center">
                {{ Form::label('daterange', 'Date', ['class' => 'my-1']) }}
            </div>
            <div class="col-sm-9">
                <span id="daterange">
                    <i class="fa fa-calendar-alt"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </span>
                <input type="hidden" id="start_at" name="start_at" value="" />
                <input type="hidden" id="end_at" name="end_at" value="" />
            </div>
        </div>
    </div>
    @endif
</div>
{{ Form::close() }}
