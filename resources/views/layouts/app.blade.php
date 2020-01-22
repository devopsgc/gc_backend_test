<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ (isset($title) ? $title.' | ' : '').config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ url('favicon.ico') }}" type="image/png"/>
    <link href="{{ asset('css/app.css?v=123') }}" rel="stylesheet">
    <link href="{{ asset('css/daterangepicker.css') }}" rel="stylesheet">
    @yield('style')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ Html::image('img/logo-gushcloud.png', config('app.name')) }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        @auth
                        <li class="px-2 nav-item{{ Request::is('/') ? ' active' : '' }}"><a class="nav-link" href="{{ url('') }}">{{ __('Dashboard') }}</a></li>
                        <li class="px-2 nav-item{{ Request::is('/records') ? ' active' : '' }}"><a class="nav-link" href="{{ url('records') }}">{{ __('Influencers') }}</a></li>
                        <li class="px-2 nav-item{{ Request::is('/campaigns') ? ' active' : '' }}"><a class="nav-link" href="{{ url('campaigns') }}">{{ __('Campaigns') }}</a></li>
                        <li class="px-2 nav-item{{ Request::is('/metrics') ? ' active' : '' }}"><a class="nav-link" href="{{ url('metrics') }}">{{ __('Metrics') }}</a></li>
                        @endauth
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        @if (Route::has('register'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                        @endif
                        @else
                        <li class="nav-item{{ Request::segment(1) == 'campaigns' && (Request::segment(2) == 'shortlist' || Request::segment(3) == 'shortlist') ? ' active' : '' }}">
                            <a class="nav-link shortlist" href="{{ url('campaigns/shortlist') }}">
                                <i class="fas fa-list-alt"></i> Shortlist
                                <?php $selected = session('selected'); ?>
                                <span class="badge badge-success shortlist-badge">{{ is_array($selected) && sizeof($selected) ? sizeof($selected) : '' }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-toggle="modal" data-target="#download">
                                <i class="fas fa-inbox"></i> Inbox
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                Hi, {{ Auth::user()->first_name }} <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                @can('index', App\Models\User::class)
                                {{ Html::link('users', 'Manage Users', ['class' => 'dropdown-item']) }}
                                {{ Html::link('social-data/dictionaries', 'Social Data Dictionaries', ['class' => 'dropdown-item']) }}
                                <div class="dropdown-divider"></div>
                                @endcan
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main>
            @yield('content')
        </main>
    </div>
    <footer class="py-3">
        <div class="container-fluid">
            <p class="m-0">&copy; {{ date('Y') }} {{ __('All Rights Reserved. Gushcloud Pte Ltd') }} &sdot;
                {{ Html::link('privacy-policy', __('Privacy Policy')) }} &sdot;
                {{ Html::link('terms-and-conditions', __('Terms and Conditions')) }}
            </p>
        </div>
    </footer>
    @auth
    <div class="modal" id="download" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Report Inbox</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <tbody>
                            <?php $reports = App\Models\Report::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->limit(5)->get(); ?>
                            @if ($reports->count())
                            @foreach ($reports as $report)
                            <tr>
                                <td>
                                    <strong>{{ $report->file }}</strong><br />
                                    Created on {{ $report->created_at->format('d M Y, H:ia') }}<br />
                                    {{ implode(', ', App\Models\Record::find(explode("\n", $report->records))->pluck('name')->toArray()) }}
                                </td>
                                <td>
                                    @if ($report->generated_at)
                                    <a class="btn<?php if ( ! $report->downloaded_at) echo ' btn-success'; ?>" href="{{ url('reports/'.$report->id) }}"><i class="fa fa-download"></i></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td>
                                    <div class="text-center">
                                        No reports found.
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endauth
    <script src="{{ asset('js/app.js?v=123') }}"></script>
    <script src="{{ asset('js/daterangepicker.min.js') }}"></script>
    @auth
    <script>
    (function() {
        $('#download .btn').on('click', function() {
            $(this).removeClass('btn-success');
        });
        @if (Request::get('download'))
        $('#download').modal('show');
        @endif
        $('[data-toggle="tooltip"]').tooltip();
    })();
    </script>
    @endauth
    @yield('script')
</body>
</html>
