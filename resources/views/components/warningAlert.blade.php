@if (session()->has('warning'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {!! session()->get('warning') !!}
</div>
@endif