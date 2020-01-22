@extends('layouts.app')

@section('style')
<style>
    #daterange {
        min-width: calc(80% - 36px) !important;
    }

    #daterange {
        background: #fff;
        cursor: pointer;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    @include('components.messageAlert')
    <div class="row py-3">
        <div class="col">
            <ul class="nav nav-pills size-md">
                <li class="nav-item">
                    <a class="nav-link{{ Request::query('filter_tab') !== 'campaigns' && Request::query('filter_tab') !== 'completed' ? ' active' : '' }}"
                        href="{{ url('/campaigns?filter_tab=draft') }}">
                        Proposals
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ Request::query('filter_tab') === 'campaigns' ? ' active' : '' }}"
                        href="{{ url('/campaigns?filter_tab=campaigns') }}">
                        Campaigns
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ Request::query('filter_tab') === 'completed' ? ' active' : '' }}"
                        href="{{ url('/campaigns?filter_tab=completed') }}">
                        Completed
                    </a>
                </li>
            </ul>
        </div>
    </div>
    @include('campaign.index.form')
    <div class="listing mt-3">
        @include('campaign.index.pagination')
        @if (Request::query('filter_tab') !== 'campaigns')
        @include('campaign.index.proposalOrCompletedTab')
        @else
        @include('campaign.index.tab')
        @endif
    </div>
</div>
@include('campaign.deleteModal', ['campaignId' => ''])
@endsection

@section('script')
<script>
    $(document).on('keydown', '#campaignForm input[type=text]', function(e) { return preventSubmit(e); });
    function preventSubmit(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    }
    $(document).on('keyup change', '#campaignForm input[type=text]', function(e) { refresh(this, e); });
    $(document).on('keyup change', '#daterange span', function(e) { refresh(this, e); });
    $(document).on('change', '#campaignForm select', function(e) { refresh(this, e); });
    function refresh(el, e) {
        // Reset page number el is a text number or checkbox input
        if ($(el).is('input')) $('#campaignForm input[name=page]').val(1);
        axios.get('{{ url('campaigns') }}?'+$('#campaignForm').serialize()).then(function(response) {
            $('.listing').html($(response.data).find('.listing').html());
        });
    }
    $(function() {
        $('.interests').selectize({
            options: {!! json_encode($interests) !!},
            delimiter: '|',
            create: false,
            persist: true
        });
    });
    $('#daterange').daterangepicker({
        opens: 'center',
        locale: {
            "format": "D MMMM, YYYY",
            cancelLabel: 'Clear'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        autoUpdateInput: false,
        alwaysShowCalendars: true,
        }, function(start, end, label) {
            startDate = start.format('D MMMM, YYYY');
            endDate = end.format('D MMMM, YYYY');
            $('#start_at').val(start.format('YYYY-MM-DD'));
            $('#end_at').val(end.format('YYYY-MM-DD'));
            $('#daterange span').html(startDate + ' - ' + endDate).trigger('change');
    });
    $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
        $('#daterange').data('daterangepicker').setStartDate(moment());
        $('#daterange').data('daterangepicker').setEndDate(moment());
        // clear the text
        $('#start_at').val('');
        $('#end_at').val('');
        $('#daterange span').html('').trigger('change');
    });
    $('#campaignDeleteModal').on('show.bs.modal', function(e) {
        updateDeleteFormRedirectUrl();
        updateDeleteFormDeleteUrlWithId(e);
    });
    function updateDeleteFormDeleteUrlWithId(e) {
        var campaignId = $(e.relatedTarget).data('id');
        $('#campaignDeleteModal form').attr('action', '{{ url('campaigns') }}/'+campaignId+'/status');
    }
    function updateDeleteFormRedirectUrl() {
        $('#campaignDeleteModal form #redirect_url').val('campaigns?' + $('#campaignForm').serialize());
    }
    function updateStatus(campaignId, status) {
        axios.post('{{ url('campaigns') }}/'+campaignId+'/status', {status: status}).then(function(response) {
            if (response.status === 200) {
                refresh(this, null);
            }
        });
    }
</script>
@endsection
