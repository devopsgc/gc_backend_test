@extends('layouts.app')

@section('style')
<style>
.action button { background:none; border:none; }
.table-striped th { background:#000; color:#fff; text-align:center; border:1px solid #fff; }
.table-deliverable { margin:0; }
.table-deliverable select { width:100%; height:26px; }
.table-deliverable input { width:100%; height:26px; border:1px solid #999; border-radius:5px; padding:5px; }
.tooltip-inner { text-align: left; }
</style>
@endsection

@section('content')
{{ Form::open(['url' => 'campaigns/'.$record->id.'/deliverables', 'method' => 'put', 'id' => 'deliverableForm']) }}
<div class="ajaxModalHeader">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <h4>Add/Edit Deliverables</h4>
            </div>
            <div class="col-md-6 text-right">
                {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
            </div>
        </div>
    </div>
</div>
<div class="ajaxModalBody">
    <div class="container-fluid">
        <div id="addEditDeliverables">
            <div class="d-flex justify-content-center align-items-center">
                <table class="table table-striped table-condensed table-deliverable">
                    <thead>
                        <tr>
                            <th width="50" class="align-middle border-0">Quantity</th>
                            <th class="align-middle border-0">Deliverable / Internal Rate</th>
                            <th width="120" class="align-middle border-0">External Rate</th>
                            <th width="50" class="align-middle border-0"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($deliverables) == 0)
                        <tr>
                            <td>
                                {{ Form::select('quantity[]', [
                                    1 => 1,
                                    2 => 2,
                                    3 => 3,
                                    4 => 4,
                                    5 => 5,
                                    6 => 6,
                                    7 => 7,
                                    8 => 8,
                                    9 => 9,
                                    10 => 10,
                                ], old('quantity[0]'), ['class' => 'form-control']) }}
                            </td>
                            <td>
                                {{ Form::select('deliverable[]', [
                                    'facebook_post' => 'Facebook Post'.($record->facebook_external_rate_post ? ' ($'.$record->facebook_external_rate_post.')' : ''),
                                    'facebook_video' => 'Facebook Video'.($record->facebook_external_rate_video ? ' ($'.$record->facebook_external_rate_video.')' : ''),
                                    'instagram_post' => 'Instagram Post'.($record->instagram_external_rate_post ? ' ($'.$record->instagram_external_rate_post.')' : ''),
                                    'instagram_video' => 'Instagram Video'.($record->instagram_external_rate_video ? ' ($'.$record->instagram_external_rate_video.')' : ''),
                                    'instagram_story' => 'Instagram Story'.($record->instagram_external_rate_story ? ' ($'.$record->instagram_external_rate_story.')' : ''),
                                    'youtube_video' => 'YouTube Video'.($record->youtube_external_rate_video ? ' ($'.$record->youtube_external_rate_video.')' : ''),
                                    'twitter_post' => 'Twitter Post',
                                    'twitter_video' => 'Twitter Video',
                                ], old('deliverable[0]'), ['class' => 'form-control']) }}
                            </td>
                            <td>
                                {{ Form::number('price[]', old('price[0]'), ['autocomplete' => 'off', 'min' => 0, 'class' => 'form-control']) }}
                                @if ($errors->has('price'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('price') }}</strong>
                                </span>
                                @endif
                            </td>
                            <td class="action">
                                <button type="submit" class="btn btn-danger btn-xs">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @else
                        @foreach ($deliverables as $deliverable)
                        <tr>
                            <td>
                                {{ Form::select('quantity[]', [
                                    1 => 1,
                                    2 => 2,
                                    3 => 3,
                                    4 => 4,
                                    5 => 5,
                                    6 => 6,
                                    7 => 7,
                                    8 => 8,
                                    9 => 9,
                                    10 => 10,
                                ], old('quantity[0]', $deliverable['quantity']), ['class' => 'form-control']) }}
                            </td>
                            <td>
                                {{ Form::select('deliverable[]', [
                                    'facebook_post' => 'Facebook Post'.($record->facebook_external_rate_post ? ' ($'.$record->facebook_external_rate_post.')' : ''),
                                    'facebook_video' => 'Facebook Video'.($record->facebook_external_rate_video ? ' ($'.$record->facebook_external_rate_video.')' : ''),
                                    'instagram_post' => 'Instagram Post'.($record->instagram_external_rate_post ? ' ($'.$record->instagram_external_rate_post.')' : ''),
                                    'instagram_video' => 'Instagram Video'.($record->instagram_external_rate_video ? ' ($'.$record->instagram_external_rate_video.')' : ''),
                                    'instagram_story' => 'Instagram Story'.($record->instagram_external_rate_story ? ' ($'.$record->instagram_external_rate_story.')' : ''),
                                    'youtube_video' => 'YouTube Video'.($record->youtube_external_rate_video ? ' ($'.$record->youtube_external_rate_video.')' : ''),
                                    'twitter_post' => 'Twitter Post',
                                    'twitter_video' => 'Twitter Video',
                                ],
                                old('deliverable[0]', strtolower($deliverable['platform']).'_'.strtolower($deliverable['type'])),
                                ['class' => 'form-control']) }}
                            </td>
                            <td>
                                {{ Form::number('price[]', old('price[0]', $deliverable['price']), ['autocomplete' => 'off', 'min' => 0, 'class' => 'form-control']) }}
                            </td>
                            <td class="action">
                                <button type="submit" class="btn btn-danger btn-xs">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                        <tr>
                            <td colspan="5">
                                <button type="button" class="btn btn-default btn-xs">+ Add New Deliverable</button>
                            </td>
                        </tr>
                        <input id="record_id" hidden name="record_id" value="">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}
@endsection

@section('script')
<div>
    <script>
    $('#addEditDeliverables .btn-default').on('click', function(e) {
        var el = $('.table-deliverable tbody tr:eq(0)').clone();
        $(el).find('option:selected').removeAttr('selected');
        $(el).find(':input').each(function(){
            $(this).removeAttr('value');
        });
        $(this).closest('tr').before('<tr>'+$(el).html()+'</tr>');
    });
    $(document).on('click', '#addEditDeliverables .btn-danger', function(e) {
        if ($('.table-deliverable tbody tr').length == 2) {
            var el = $('.table-deliverable tbody tr:eq(0)');
            $(el).find('select:eq(0)').val($(el).find('select:eq(0) option:first').val());
            $(el).find('select:eq(1)').val($(el).find('select:eq(1) option:first').val());
            $(el).find('input:eq(0)').val('');
            e.preventDefault();
        }
        else {
            $(this).closest('tr').remove();
        }
    });
    </script>
</div>
@endsection
