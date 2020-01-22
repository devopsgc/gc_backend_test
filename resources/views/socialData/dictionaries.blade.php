@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="text-right">
        <small>*note that the count in this page is not an updated data. this dictionary data is saved as of nov 2019.</small>
    </div>
    <div class="accordion" id="accordian-social-data-dic">
        <div class="card">
            <div class="card-header" id="heading-interests">
                <h2 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-interests" aria-expanded="true"
                        aria-controls="collapse-interests">
                        Interests
                    </button>
                </h2>
            </div>
            <div id="collapse-interests" class="collapse" aria-labelledby="heading-interests" data-parent="#accordian-social-data-dic">
                <div class="card-body">
                    <div class="row py-2 mx-1 font-weight-bold">
                        <div class="col-1">ID</div>
                        <div class="col-7">Name</div>
                        <div class="col-4">Count</div>
                    </div>
                    @foreach ($interests as $interest)
                    <div class="row border py-1 mx-1">
                        <div class="col-1">
                            {{ $interest->id }}
                        </div>
                        <div class="col-7">
                            {{ $interest->name }}
                        </div>
                        <div class="col-4">
                            {{ $interest->count }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="heading-languages">
                <h2 class="mb-0">
                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse-languages" aria-expanded="false"
                        aria-controls="collapse-languages">
                        Languages
                    </button>
                </h2>
            </div>
            <div id="collapse-languages" class="collapse" aria-labelledby="heading-languages" data-parent="#accordian-social-data-dic">
                <div class="card-body">
                    <div class="row py-2 mx-1 font-weight-bold">
                        <div class="col-1">Code</div>
                        <div class="col-7">Name</div>
                        <div class="col-4">Count</div>
                    </div>
                    @foreach ($languages as $language)
                    <div class="row border py-1 mx-1">
                        <div class="col-1">
                            {{ $language->code }}
                        </div>
                        <div class="col-7">
                            {{ $language->name }}
                        </div>
                        <div class="col-4">
                            {{ $language->count }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="heading-brands">
                <h2 class="mb-0">
                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse-brands" aria-expanded="false"
                        aria-controls="collapse-brands">
                        Brands
                    </button>
                </h2>
            </div>
            <div id="collapse-brands" class="collapse" aria-labelledby="heading-brands" data-parent="#accordian-social-data-dic">
                <div class="card-body">
                    <div class="row py-2 mx-1 font-weight-bold">
                        <div class="col-1">ID</div>
                        <div class="col-7">Name</div>
                        <div class="col-4">Count</div>
                    </div>
                    @foreach ($brands as $brand)
                    <div class="row border py-1 mx-1">
                        <div class="col-1">
                            {{ $brand->id }}
                        </div>
                        <div class="col-7">
                            {{ $brand->name }}
                        </div>
                        <div class="col-4">
                            {{ $brand->count }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="heading-countries">
                <h2 class="mb-0">
                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse-countries" aria-expanded="false"
                        aria-controls="collapse-countries">
                        Countries
                    </button>
                </h2>
            </div>
            <div id="collapse-countries" class="collapse" aria-labelledby="heading-countries" data-parent="#accordian-social-data-dic">
                <div class="card-body">
                    <div class="row py-2 mx-1 font-weight-bold">
                        <div class="col-1">ID</div>
                        <div class="col-3">Name (code)</div>
                        <div class="col-8">Cities</div>
                    </div>
                    @foreach ($countries as $country)
                    <div class="row border py-1 mx-1">
                        <div class="col-1">
                            {{ $country->id }}
                        </div>
                        <div class="col-3">
                            {{ $country->name }} ({{ $country->country->code }})
                        </div>
                        <div class="col-8">
                            {{ $country->cities->pluck('name')->implode(', ') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection