@extends('layouts.app')

@section('style')
<style>
    .fb img {
        width: 100%;
        max-width: 240px;
    }
</style>
@endsection

@section('content')
<div class="container">
    @include('components.messageAlert')
    @include('components.warningAlert')
    @if(! session()->has('message'))
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Connect to Facebook</div>
                <div class="panel-body text-center">
                    <a class="fb" href="{{ url('connect/facebook') }}"><img src="{{ url('img/facebook-button.png') }}" alt="Continue with Facebook" /></a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {

});
</script>
@endsection