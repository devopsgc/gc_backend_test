@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <h2 class="py-3">Influencer network overview</h2>
    <div class="row">
        @foreach ($countries as $country)
        <div class="col-md-3">
            <div class="card text-center mb-4">
                <div class="card-header bg-dark">
                    {{ Html::image('flags/'.$country->iso_3166_2.'.png', $country->name, ['class' => 'mr-2']) }}
                    <a href="{{ url('metrics?country_id=').$country->id }}">
                        <strong class="text-white">{{ $country->name }}</strong>
                    </a>
                </div>
                <div class="card-body">
                    <span>Influencers</span>
                    <h3 class="font-weight-bold">{{ number_format($country->influences) }}</h3>
                    <span>Followers</span>
                    <h3 class="font-weight-bold">{{ number_format($country->followers) }}</h3>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
