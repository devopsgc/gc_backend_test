@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-md-6">
            <h2 class="m-0">Manage Users
                <a href="{{ url('users/create') }}" class="btn btn-link">+ Add User</a>
            </h2>
        </div>
        <div class="col-md-6">
            {{ Form::open(['url' => 'users', 'method' => 'get', 'id' => 'indexForm']) }}
            {{ Form::label('search', 'Search', ['hidden']) }}
            {{ Form::text('q', Request::get('q'), ['class' => 'form-control', 'placeholder' => 'Search by name, email', 'id' => 'search']) }}
            {{ Form::close() }}
        </div>
    </div>
    <br />
    <div class="users-listing">
        <div class="row">
            <div class="col-md-12">
                <p class="text-right">
                    Showing {{ ($users->currentpage()-1)*$users->perpage()+1 }} to
                    {{ ($users->currentpage()*$users->perpage()) > $users->total() ? $users->total() : $users->currentpage()*$users->perpage() }} of
                    {{ $users->total() }} users
                </p>
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>ID</th>
                                <th>Status</th>
                                <th>Email</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Role</th>
                                <th width="200">Countries</th>
                                <th>Created At</th>
                            </tr>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{!! $user->suspended_at ? '<span class="badge badge-danger">Suspended</span>' : '<span
                                        class="badge badge-success">Active</span>' !!}</td>
                                <td>{{ Html::link('users/'.$user->id, $user->email) }}</td>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->role->name }}</td>
                                <td>{{ $user->countries->count() ? $user->countries->implode('iso_3166_2', ', ') : 'All Countries' }}</td>
                                <td>{{ $user->created_at->format('d M Y H:ia') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $users->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).on('keydown', '#indexForm input[type=text]', function(e) { return preventSubmitByPressingEnter(e); });
    function preventSubmitByPressingEnter(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    }

    $(document).on('keyup', '#indexForm input[type=text]', function(e) { refreshUserList(); });
    var refreshUserList = debounce(function() {
        axios.get('{{ url('users') }}?'+$('#indexForm').serialize()).then(function(response) {
            $('.users-listing').html($(response.data).find('.users-listing').html());
        });
    }, 500);

    // Returns a function, that, as long as it continues to be invoked, will not
    // be triggered. The function will be called after it stops being called for
    // N milliseconds. If `immediate` is passed, trigger the function on the
    // leading edge, instead of the trailing.
    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };
</script>
@endsection