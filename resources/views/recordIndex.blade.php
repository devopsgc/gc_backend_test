@extends('layouts.app')

@section('style')
<style>
    .record-links .pagination {
        flex-wrap: wrap;
    }
</style>
@show

@section('content')
<div class="container-fluid">
    {{ Form::open(['url' => 'records', 'method' => 'get', 'id' => 'indexForm', 'class' => 'm-0']) }}
    <div class="row">
        <div class="col-md-4 filters-wrap">
            {{ Form::text('q', Request::get('q'), ['class' => 'form-control my-3', 'placeholder' => 'Search by name, facebook or instagram ID']) }}
            <div class="d-md-none my-2 text-right">
                <button type="button" class="btn btn-outline-primary btn-sm record-index-filters-btn">Show Filters</button>
            </div>
            <div class="d-none d-md-block record-index-filters">
                @include('recordIndexFilters')
            </div>
        </div>
        <div class="col-md-8 listing-wrap">
            <div class="filters-tags d-flex">
            </div>
            <div class="listing">
                <div class="row mt-4">
                    <div class="col-md-9">
                        <div class="mb-3">
                            @include('recordIndexPagination')
                        </div>
                    </div>
                    <div class="col-md-3 text-right">
                        <div class="d-flex flex-column">
                            @can('create', App\Models\Record::class)
                            <p><a class="btn btn-link btn-sm text-nowrap p-0" href="{{ url('records/create') }}">+ Add New Influencer</a></p>
                            @endcan
                            <div class="form-group">
                                {{ Form::select('order', [
                                    'created_at' => 'Most Recently Created',
                                    'updated_at' => 'Most Recently Updated',
                                    'instagram_followers' => 'Most Instagram Followers',
                                    'youtube_subscribers' => 'Most YouTube Subscribers',
                                    'facebook_followers' => 'Most Facebook Followers',
                                    'twitter_followers' => 'Most Twitter Followers',
                                    'tiktok_followers' => 'Most TikTok Followers',
                                    'weibo_followers' => 'Most Weibo Followers',
                                    'xiaohongshu_followers' => 'Most XiaoHongShu Followers',
                                ], Request::get('order', 'created_at'), ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>
                </div>
                @include('recordIndexTable')
                <div class="row">
                    <div class="col-md-9">
                        <div class="mb-3">
                            @include('recordIndexPagination')
                        </div>
                    </div>
                    <div class="col-md-3">
                    </div>
                </div>
                {{ Form::hidden('page', Request::get('page')) }}
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>
@include('components.recordModal')
@include('components.recordDeleteModal')
@endsection

@section('script')
@include('components.recordModalScript')
<script>
    $(document).ready(function() {
    /* ----- start
        control of the scrolling of filter view and listing view.
    */
    function removeViewFlow() {
        $('.filters-wrap').css('height', '');
        $('.filters-wrap').css('overflow-y', '');
        $('.listing').css('height', '');
        $('.listing').css('overflow-y', '');
    }

    function addViewFlow() {
        let height = $(window).outerHeight()-$('.navbar').outerHeight()-$('footer').outerHeight();
        $('.filters-wrap').css({'height':height+'px', 'overflow-y':'scroll'});
        $('.listing-wrap').css({'height':height+'px', 'overflow-y':'scroll'});
    }

    function resize() {
        if ($(window).outerWidth() < 760) {
            // remove view flow because it is hard to scroll on mobile with 2 view overflow
            removeViewFlow();
        } else {
            addViewFlow();
        }
    }

    resize();
    $(window).on('resize', _.debounce(function() {
        resize();
    }, 300));
    /* ----- end */

    /* ----- start
        control of showing and hiding filters in mobile or web view.
    */
    $(document).on('click', '.record-index-filters-btn', function(e) {
        if ($('.record-index-filters').hasClass('d-none')) {
            $('.record-index-filters').removeClass('d-none');
            $('.record-index-filters').removeClass('d-md-block');
            $('.record-index-filters-btn').html('Hide Filters');
        } else {
            $('.record-index-filters').addClass('d-none');
            $('.record-index-filters').addClass('d-md-block');
            $('.record-index-filters-btn').html('Show Filters');
        }
    });
    /* ----- end */

    /* ----- start
        form submit of filters using ajax
    */
    // Disabled form submit by enter key for text and number input
    $(document).on('keydown', '#indexForm input[type=text]', function(e) { return preventSubmit(e); });
    $(document).on('keydown', '#indexForm input[type=number]', function(e) { return preventSubmit(e); });
    function preventSubmit(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    }
    $(document).on('keyup', '#indexForm input[type=text]', _.debounce(function(e) { refresh(this, e); }, 300));
    $(document).on('keyup', '#indexForm input[type=number]', _.debounce(function(e) { refresh(this, e); }, 300));
    $(document).on('change', '#indexForm input[type=checkbox]', _.debounce(function(e) { refresh(this, e); }, 300));
    $(document).on('change', '#indexForm select', _.debounce(function(e) { refresh(this, e); }, 300));
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        $('#indexForm input[name=page]').val(getURLParameter($(this).attr('href'), 'page'));
        refresh(this, e);
    });

    function getURLParameter(url, name) {
        return (RegExp(name+'='+'(.+?)(&|$)').exec(url)||[,null])[1];
    }

    function refresh(el, e) {
        // Reset page number el is a text number or checkbox input
        if ($(el).is('input')) $('#indexForm input[name=page]').val(1);
        axios.get('{{ url('records') }}?'+$('#indexForm').serialize()).then(function(response) {
            $('.listing').html($(response.data).find('.listing').html());
        });

        updateFiltersDisplay();
    }

    function updateFiltersDisplay() {
        $('.filters-tags').html('');
        $('.filters .form-check-input:checked').each(function() {
            $('.filters-tags').append('<span class="badge badge-primary m-1">'+$(this).parent().find('label').html()+'</span>');
        });
        $('.filters .min-max').each(function() {
            if ($(this).find('.min-max-min').val() || $(this).find('.min-max-max').val()) {
                let min = $(this).find('.min-max-min').val() ? addCommasToNumber($(this).find('.min-max-min').val()) : '-';
                let max = $(this).find('.min-max-max').val() ? addCommasToNumber($(this).find('.min-max-max').val()) : '-';
                $('.filters-tags').append('<span class="badge badge-primary m-1">' + $(this).find('.min-max-label').html() + ':' + min + ' to ' + max +'</span>');
            }
        });
    }

    function addCommasToNumber(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    /* ----- end */

    /* ----- start
        adding to shortlist from records listing
    */
    $(document).on('click', '.shortlist .btn-primary', function(e) {
        e.preventDefault();
        $(this).removeClass('btn-primary');
        $(this).addClass('btn-danger');
        $(this).text('Remove');
        axios.post('{{ url('campaigns/shortlist') }}', { record_id: $(this).data('record-id') })
            .then(function(response) {
                if (successAndHasDataCount(response)) {
                    refreshShortlist(response);
                } else {
                    console.log('nodata');
                    console.log(response);
                }
            }).catch(function(e) {
                console.log('error');
                console.log(e);
            });
    });
    $(document).on('mouseover', '.shortlist .btn-success', function(e) {
        $(this).removeClass('btn-success');
        $(this).addClass('btn-danger');
        $(this).text('Remove');
    });
    $(document).on('mouseout', '.shortlist .btn-danger', function(e) {
        $(this).removeClass('btn-danger');
        $(this).addClass('btn-success');
        $(this).text('Shortlisted');
    });
    $(document).on('click', '.shortlist .btn-danger', function(e) {
        e.preventDefault();
        $(this).removeClass('btn-danger');
        $(this).addClass('btn-primary');
        $(this).text('Add to Shortlist');
        axios.post('{{ url('campaigns/shortlist/remove-selection') }}', {record_id: $(this).data('record-id')})
            .then(function(response) {
                if (successAndHasDataCount(response)) {
                    refreshShortlist(response);
                } else {
                    console.log('nodata');
                    console.log(response);
                }
            }).catch(function(e) {
                console.log('error');
                console.log(e);
            });
    });
    function refreshShortlist(response) {
        if (response.data.badge_count === 0) {
            $('.shortlist-badge').html('');
        } else {
            $('.shortlist-badge').html(response.data.badge_count);
        }
        $('[data-toggle="tooltip"]').tooltip();
    }
    function successAndHasDataCount(response) {
        return response.status === 200 && response.data && response.data.badge_count >= 0;
    }
    /* ----- end */
});
</script>
@endsection
