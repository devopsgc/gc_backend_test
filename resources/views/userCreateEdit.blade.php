@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @if (Request::segment(2) == 'create')
    {{ Form::open(['url' => 'users', 'method' => 'post']) }}
    @else
    {{ Form::open(['url' => 'users/'.$user->id, 'method' => 'put']) }}
    @endif
    @if (session('status'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{ session('status') }}
    </div>
    @endif
    <p><a href="{{ url('users') }}"><i class="fa fa-angle-left" aria-hidden="true"></i> Return to Manage Users</a></p>
    <h2>{{ Request::segment(2) == 'create' ? 'Add' : 'Edit' }} User {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('email', 'Email') }}
                {{ Form::text('email', old('email', isset($user) ? $user->email : null), ['class' => 'form-control'.($errors->has('email') ? ' is-invalid' : ''), 'id' => 'email']) }}
                @error('email')<span class="invalid-feedback d-block">{{ $errors->first('email') }}</span>@enderror
            </div>
            <div class="form-group">
                {{ Form::label('password', 'Password') }}
                {{ Form::text('password', old('password'), ['class' => 'form-control'.($errors->has('password') ? ' is-invalid' : ''), 'id' => 'password']) }}
                @error('password')<span class="invalid-feedback d-block">{{ $errors->first('password') }}</span>@enderror
            </div>
            <div class="form-group">
                {{ Form::label('first_name', 'First Name') }}
                {{ Form::text('first_name', old('first_name', isset($user) ? $user->first_name : null), ['class' => 'form-control'.($errors->has('first_name') ? ' is-invalid' : ''), 'id' => 'first_name']) }}
                @error('first_name')<span class="invalid-feedback d-block">{{ $errors->first('first_name') }}</span>@enderror
            </div>
            <div class="form-group">
                {{ Form::label('last_name', 'Last Name') }}
                {{ Form::text('last_name', old('last_name', isset($user) ? $user->last_name : null), ['class' => 'form-control'.($errors->has('last_name') ? ' is-invalid' : ''), 'id' => 'last_name']) }}
                @error('last_name')<span class="invalid-feedback d-block">{{ $errors->first('last_name') }}</span>@enderror
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                @php $countriesArray = isset($user) && $user->countries ? $user->countries->pluck('id')->toArray() : [] @endphp
                {{ Form::label('country_ids', 'Country') }}
                @foreach ($countries as $i => $country)
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('country_ids[]', $country->id, in_array(old('country_ids.'.$i, $country->id), $countriesArray)) }}
                        {{ $country->name }}
                        {{ Html::image('flags/'.$country->iso_3166_2.'.png', null, ['style' => 'margin-left:5px; width:20px;']) }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('role_id', 'Role', ['class' => 'control-label']) !!}
                @foreach ($roles as $role)
                <div class="radio">
                    <label>{!! Form::radio('role_id', $role->id, $role->id === old('role_id', isset($user) && $user->role ? $user->role->id : '')) !!}
                        {{ $role->name }}</label>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    {{ Form::close() }}
    @if (isset($user) && ! $user->isSuperAdmin())
    @if ($user->suspended_at)
    <p>This user is <strong>suspended</strong>. Would you like to <a href="#" data-toggle="modal" data-target=".modal-restore">restore this user?</a>
    </p>
    {{ Form::open(['id' => 'restore-form', 'url' => 'users/'.$user->id.'/restore', 'method' => 'post', 'style' => 'display:none;']) }}
    {{ Form::close() }}
    <div class="modal modal-restore" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Restore User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to restore this user?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="document.getElementById('restore-form').submit();">Yes, Restore</button>
                </div>
            </div>
        </div>
    </div>
    @else
    <p>This user is <strong>active</strong>. Would you like to <a href="#" style="" data-toggle="modal" data-target=".modal-suspend">suspend this user?</a>
    </p>
    {{ Form::open(['id' => 'suspend-form', 'url' => 'users/'.$user->id.'/suspend', 'method' => 'post', 'style' => 'display:none;']) }}
    {{ Form::close() }}
    <div class="modal modal-suspend" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Suspend User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to suspend this user?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('suspend-form').submit();">Yes, Suspend</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>
@endsection

@section('script')
<script>
    $(".alert").alert();
</script>
@endsection