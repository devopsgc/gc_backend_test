@extends('layouts.app')

@section('style')
<style>
    /*
    #campaignDeliverables th { vertical-align:top; }
    .influencer img { width: 70px; margin-right: 10px; border-radius: 50px; }
    .social table { width: 100%; }
    .social img { width: 35px; margin-bottom: 5px; }
    .social .inner { padding: 10px; line-height: 1.2em; }
    .social .total { background: #eee; }
    .table-striped th { background: #000; color: #fff; text-align: center; border: 1px solid #fff; }
    .table-striped th select { color: #000; margin-left: 5px; }
    .table-striped tfoot tr th { vertical-align: middle }
    .glyphicon-exclamation-sign { color: red; font-size: 20px; }
    .helptip { border-bottom: 1px dotted #999; }
    .table-deliverable { margin: 0; }
    .table-deliverable select { width: 100%; height: 26px; }
    .table-deliverable input { width: 100%; height: 26px; border: 1px solid #999; border-radius: 5px; padding: 5px; }
    .tooltip-inner { text-align: left; }
    .table-record-deliverable { margin-bottom: 5px; background-color: transparent !important; }
    .table-record-deliverable .total { border-top: 1px solid #999; }
    .table-record-deliverable td { border-top-width: 0px !important; line-height: 1rem !important; vertical-align: baseline !important }
    .table-record-deliverable td:first-child { text-align: right; width: 60%; }
    .table-record-deliverable td:last-child { text-align: left; width: 40%; }
    .deliverables span img { width: 20px; }
    .package_price { text-align: center; }
    .total_price { text-align: center; }
    .exit-campaign h2 { display: inline-block; }
    .exit-campaign div { display: inline-block; vertical-align: top; margin-left: 10px; }
    .no-border { border: 0 !important; }
    ::placeholder { opacity: 0.3; }
    */
    .table td {
        padding: 1.5rem;
    }
</style>
@endsection

@if (session()->has('campaign.campaign_id'))
@php $campaign = App\Models\Campaign::find(session('campaign.campaign_id')); @endphp
@endif

@section('content')


@include('components.recordModal')
@include('components.deliverableModal')
@include('components.recordDeleteModal')
@include('campaign.exitModal')
@include('components.recordClearModal')
@include('campaign.saveAndCreateProposalNameModal')
@isset ($campaign)
@include('campaign.updateProposalModal')
@endisset

<div class="container-fluid pt-3">
    @include('components.messageAlert')
    @include('components.warningAlert')
    @isset($campaign)
    <div class="row mb-3">
        <div class="col-md-6">
            @if ($campaign->isDraft())
            <div class="d-flex align-items-center">
                <h2>{{ $campaign->name }}</h2>
                <a class="ml-3" href="#" data-toggle="modal" data-target="#updateProposalModal">
                    <i class="far fa-edit"></i>
                </a>
            </div>
            @else
            <h2>{{ Html::link($campaign->getPath(), $campaign->name) }}</h2>
            @endif
        </div>
        <div class="col-md-6 exit-campaign text-md-right">
            @if ($campaign->isDraft())
            <a class="btn btn-default" href="{{ url('/records') }}">+ Add Influencers</a>
            @endif
            <span class="btn btn-sm btn-danger" data-toggle="modal" data-target="#campaignExitModal">
                Exit {{ $campaign->isDraft() ? 'Proposal' : 'Campaign' }}
            </span>
        </div>
    </div>
    @endisset

    @isset($campaign)
    {{ Form::open(['url' => 'campaigns/'.$campaign->id.'/shortlist', 'method' => 'PUT', 'class' => 'form-inline']) }}
    @else
    {{ Form::open(['id' => 'createCampaignProposalForm', 'url' => 'campaigns', 'method' => 'POST', 'class' => 'form-inline']) }}
    @endisset
    @include('campaign.recordShortlist')
    {{ Form::close() }}
</div>
@endsection

@section('script')
@include('components.recordModalScript')
@include('components.deliverableModalScript')
<script>
    $(document).ready(function() {
    @error('name')$('#updateProposalModal').modal('show');@enderror
    setupPage();
    registerDisableEnterPressedToSubmitForm();
    $('.package_price').on('keyup change', function(el) {
        updateTotal(true);
    });
    $('.total_price').on('keyup change', function(el) {
        updateTotalDiscount();
    });
    $('.total_package_price').on('change', function(el) {
        updateGrandTotalPackagePrice();
    });
    function setupPage() {
        $('.glyphicon-exclamation-sign').tooltip({html:true});
        $('[data-toggle="tooltip"]').tooltip();
        updateTotal(false);
        registerDeleteRecord();
        setupLinkForCreateProposalModal();
    }
    function setupLinkForCreateProposalModal() {
        $('#saveAndCreateProposalNameModal .saveAndCreateProposalNameModalSubmit').click( function(e) {
            e.preventDefault();
            $('#saveAndCreateProposalName').val($('#saveAndCreateProposalNameModal input[name=name]').val());
            $('#createCampaignProposalForm').submit();
        });
    }
    function updateTotal(shouldUpdateGrandTotalPackagePrice) {
        updateDiscount();
        var total_package_price = $('.package_price').map(function() {
            return $(this).val();
        }).get().reduce(function(a, b) { return parseFloat(a) + parseFloat(b); }, 0);
        $('.total_package_price').text(total_package_price.toLocaleString());
        if (shouldUpdateGrandTotalPackagePrice) updateGrandTotalPackagePrice();;
        updateTotalDiscount();
    }
    function updateDiscount() {
        $('.package_price').map(function() {
            var package_price = this.value;
            var total_price = $(this).closest('td').prev().find('.deliverable_total_price').text().replace('$', '').replace(',', '');
            var discount = (total_price - package_price)/total_price * 100;
            if (!parseFloat(total_price)) $(this).next().find('.discount').text('N/A');
            else $(this).next().find('.discount').text(discount.toFixed(2) + '%');
        })
    }
    function updateTotalDiscount() {
        var total_package_price = $('.package_price').map(function() {
            return $(this).val();
        }).get().reduce(function(a, b) { return parseFloat(a) + parseFloat(b); }, 0);
        var discounted_price = $('.total_price').val();
        var total_discount = (total_package_price - discounted_price)/total_package_price * 100;
        if (total_package_price == 0 ) $('.total_discount').text('0.00%');
        else $('.total_discount').text(total_discount.toFixed(2) + '%');
    }
    function updateGrandTotalPackagePrice() {
        $('.total_price').val($('.total_package_price').text().replace('$', '').replace(',', ''));
    }
    $('#recordModal').on('reloadRecord', function(e) {
        axios.get('{{ url('campaigns/shortlist') }}').then(function(response) {
            $('#campaignDeliverables').html($(response.data).find('#campaignDeliverables').html());
            setupPage();
        });
    });
    function registerDisableEnterPressedToSubmitForm() {
        $(document).on("keydown", "form", function(event) {
            return event.key != "Enter";
        });
    }
    function registerDeleteRecord() {
        $('.remove-selection').on('click', function(e) {
            e.preventDefault();
            axios.post('{{ url('campaigns/shortlist/remove-selection') }}', {record_id: $(this).data('record-id')})
            .then(function(response) {
                location.reload();
            });
        });
    }
});
</script>
@endsection