@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="py-3">Metrics</h2>
    <div class="row">
        <div class="col-md-4">
            {{ Form::open(['url' => 'metrics', 'method' => 'get', 'id' => 'filter']) }}
                {{ Form::select('country_id', $countriesFilter, old('country_id', request('country_id')), ['class' => 'form-control mb-4', 'id' => 'country_id']) }}
            {{ Form::close() }}
        </div>
    </div>
    <div class="row">
        @foreach ($metrics as $type => $chart)
        <div class="col-md-4">
            <canvas class="mb-4" id="{{ $type }}"></canvas>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('script')
<script>
    $('#country_id').change(function () {
        $('#filter').submit();
    });
    @foreach ($metrics as $type => $chart)
    @include('_chartjs', [
        'chart' => $chart,
        'type' => $type
    ])
    @endforeach
</script>
@endsection