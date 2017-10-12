@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Update breakdown template - Add Resource</h2>

        <div class="btn-toolbar">
            <a href="{{route('project.budget', $project)}}" class="btn btn-sm btn-default">
                <i class="fa fa-chevron-left"></i> Back to project
            </a>

            <a href="{{route('breakdown-template.show', [$breakdown_template, 'project' => $project])}}" class="btn btn-sm btn-danger">
                <i class="fa fa-close"></i> Cancel
            </a>
        </div>
    </div>
@endsection

@section('body')
    <table class="table table-condensed table-striped">
        <tbody>
        <tr>
            <th class="col-sm-3">Project</th>
            <td>{{$project->name}}</td>
        </tr>
        <tr>
            <th>Activity</th>
            <td>
                {{$breakdown_template->activity->division->path}} &raquo;
                {{$breakdown_template->activity->name}}
            </td>
        </tr>
        <tr>
            <th>Breakdown Template</th>
            <td>{{$breakdown_template->name}}</td>
        </tr>
        </tbody>
    </table>


    <form action="" method="post">
        {{csrf_field()}}

        <table class="table table-striped table-bordered">
            <thead>
            <tr class="bg-primary">
                <th class="text-center"><input type="checkbox" name="" id="select-all"></th>
                <th>WBS</th>
                <th>Cost Account</th>
                <th>Budget Qty</th>
                <th>Eng Qty</th>
                <th>Resource Qty</th>
                <th>Budget Unit</th>
                <th>Unit Price</th>
                <th>Budget Cost</th>
            </tr>
            </thead>
            <tbody>
            @foreach($breakdowns as $breakdown)
                <tr>
                    <td class="text-center">
                        <input value="1" class="select-breakdown" type="checkbox" name="breakdown[{{$breakdown->id}}]">
                    </td>
                    <td>
                        <abbr title="{{$breakdown->wbs_level->path}}">{{$breakdown->wbs_level->code}}</abbr>
                    </td>
                    <td>{{$breakdown->cost_account}}</td>
                    <td>{{number_format($breakdown->new_resource->budget_qty, 2)}}</td>
                    <td>{{number_format($breakdown->new_resource->eng_qty, 2)}}</td>
                    <td>{{number_format($breakdown->new_resource->resource_qty, 2)}}</td>
                    <td>{{number_format($breakdown->new_resource->budget_unit, 2)}}</td>
                    <td>{{number_format($breakdown->new_resource->unit_price, 2)}}</td>
                    <td>{{number_format($breakdown->new_resource->budget_cost, 2)}}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr class="info">
                <th colspan="8" class="text-right">Total</th>
                <th>{{number_format($breakdowns->pluck('new_resource')->sum('budget_cost'), 2)}}</th>
            </tr>
            </tfoot>
        </table>

        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Update Breakdowns</button>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="confirm-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title text-danger"><i class="fa fa-exclamation-circle"></i> Update Breakdowns</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead text-danger">Are you sure you want to continue without adding resources to breakdowns?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="confirm-btn" class="btn btn-danger">Confirm <i class="fa fa-chevron-right"></i></button>
                        <button type="button" class="btn btn-default"><i class="fa fa-remove"></i> Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('javascript')
    <script>
        $(function () {
            $('abbr').tooltip();

            const checkboxes = $('.select-breakdown');
            $('#select-all').on('change', e => {
                checkboxes.prop('checked', e.target.checked);
            });

            let confirm = false;
            const form = $('form').on('submit', e => {
                if (!confirm && checkboxes.filter(':checked').length === 0) {
                    e.preventDefault();
                    $('#confirm-modal').modal();
                }
            }).on('click', '#confirm-btn', e => {
                confirm = true;
                form.submit();
            });
        });
    </script>
@endsection