@extends('layouts.app')

@section('header')
    <h2>Fix import</h2>

    <a href="{{ route('project.show', $project) }}" class="btn btn-sm btn-warning pull-right"><i class="fa fa-remove"></i>
        Cancel</a>
@endsection

@section('body')
    {{Form::open(['route' => ['survey.post-fix-import', $key]])}}
    <div class="form-group clearfix">
        <button class="btn btn-primary pull-right"><i class="fa fa-check"></i> Update</button>
    </div>

    <table class="table table-striped table-condensed">
        <thead>
        <tr>
            <th>Cost Account</th>
            <th>Original WBS</th>
            <th>Selected WBS</th>
            <th>Description</th>
            <th>Original Unit</th>
            <th>Unit</th>
            <th>Budget Qty</th>
            <th>Eng Qty</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $idx => $item)
            <tr class="{{$errors->first($idx, 'danger')}}">
                <td>
                    {{$item['cost_account']}}
                    {{Form::hidden("data[$idx][cost_account]", $item['cost_account'])}}
                </td>
                <td>
                    {{$item['wbs_code']}}
                </td>
                <td>
                    @if ($item['wbs_level_id'])
                        {{$item['wbs_code']}}
                        {{Form::hidden("data[$idx][wbs_level_id]", $item['wbs_level_id'])}}
                    @else
                        <a href="#" class="select-wbs">

                            {{old("data.$idx.wbs_level_id")? App\WbsLevel::find(old("data.$idx.wbs_level_id"))->code : 'Select WBS'}}
                        </a>
                        {{Form::hidden("data[$idx][wbs_level_id]")}}
                    @endif
                </td>
                <td class="col-md-4">
                    {{substr($item['description'], 0, 55)}}{{strlen($item['description']) > 55? '...' : '' }}
                    {{Form::hidden("data[$idx][description]", $item['description'])}}
                </td>
                <td>{{$item['unit']}}</td>
                <td>
                    @if ($item['unit_id'])
                        {{$item['unit']}}
                        {{Form::hidden("data[$idx][unit_id]", $item['unit_id'])}}
                    @else
                        {{Form::select("data[$idx][unit_id]", App\Unit::options())}}
                    @endif
                </td>
                <td>
                    {{number_format(floatval($item['budget_qty']), 2)}}
                    {{Form::hidden("data[$idx][budget_qty]", $item['budget_qty'])}}
                </td>
                <td>
                    {{number_format(floatval($item['eng_qty']), 2)}}
                    {{Form::hidden("data[$idx][eng_qty]", $item['eng_qty'])}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="form-group clearfix">
        <button class="btn btn-primary pull-right"><i class="fa fa-check"></i> Update</button>
    </div>
    {{Form::close()}}

    <div id="WbsModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Select WBS</h4>
                </div>
                <div class="modal-body">
                    <ul class="list-unstyled tree">
                        @foreach(App\WbsLevel::forProject($project->id)->tree()->get() as $level)
                            @include('wbs-level._recursive_input', compact('level'))
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        (function (w, d, $) {
            $(function () {
                var target = null;
                var wbsModal = $('#WbsModal');

                $('.select-wbs').click(function (e) {
                    e.preventDefault();
                    target = $(this);
                    wbsModal.modal().find('.tree-radio').attr('checked', false);
                    wbsModal.find('.in').removeClass('in');
                });

                wbsModal.on('change', '.tree-radio', function(){
                    target.text($(this).data('code'));
                    target.parent().find('input').val($(this).val());
                });
            });
        }(window, document, jQuery));
    </script>
@endsection