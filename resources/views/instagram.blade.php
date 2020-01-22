@extends('layouts.app')

@section('content')
<div class="container">
    {{ Form::open(['url' => 'instagram', 'method' => 'post']) }}
    <div class="row">
        <div class="col-md-6">
            <h3 class="py-3">Instructions</h3>
            <p>Login on instagram.com &gt; go to your profile page &gt; open inspector &gt; look under XHR requests tab &gt; find the query_hash and sessionid.</p>
            <p>{{ Html::image('img/example-query-hash.png', 'Example 1', ['style' => 'width:100%;']) }}</p>
            <p>Example above shows the query_hash</p>
            <p>{{ Html::image('img/example-session-id.png', 'Example 2', ['style' => 'width:100%;']) }}</p>
            <p>Example above shows the sessionid within the request cookie</p>
        </div>
        <div class="col-md-6">
            <h3 class="py-3">Session</h3>
            @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif
            <div class="form-group">
                {{ Form::label('session_id', 'Session ID', ['class' => 'col-form-label']) }}
                {{ Form::textarea('session_id', old('session_id', $instagram->session_id), ['class' => 'form-control'.($errors->has('session_id') ? ' is-invalid' : '')]) }}
                @if ($errors->has('session_id'))
                <span class="invalid-feedback d-block">{{ $errors->first('session_id') }}</span>
                @endif
            </div>
            <div class="form-group">
                {{ Form::label('query_hash', 'Query Hash', ['class' => 'col-form-label']) }}
                {{ Form::text('query_hash', old('query_hash', $instagram->query_hash), ['class' => 'form-control'.($errors->has('query_hash') ? ' is-invalid' : '')]) }}
                @if ($errors->has('query_hash'))
                <span class="invalid-feedback d-block">{{ $errors->first('query_hash') }}</span>
                @endif
            </div>
            <div class="form-group">
                {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>
@endsection

@section('script')
<script>
$(".alert").alert();
</script>
@endsection