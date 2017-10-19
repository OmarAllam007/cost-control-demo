@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Update breakdown template - Delete Resource</h2>

        <div class="btn-toolbar">
            <a href="{{route('project.budget', $project)}}" class="btn btn-sm btn-default">
                <i class="fa fa-chevron-left"></i> Back to project
            </a>

            <a href="{{route('breakdown-template.show', [$template_resource->breakdown_template, 'project' => $project])}}" class="btn btn-sm btn-danger">
                <i class="fa fa-close"></i> Cancel
            </a>
        </div>
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-sm-6">
            <table class="table table-condensed table-striped col-sm-6">

                <tbody>
                <tr>
                    <th class="col-sm-3">Project</th>
                    <td>{{$project->name}}</td>
                </tr>
                <tr>
                    <th>Activity</th>
                    <td>
                        {{$template_resource->template->activity->division->path}} &raquo;
                        {{$template_resource->template->activity->name}}
                    </td>
                </tr>
                <tr>
                    <th>Breakdown Template</th>
                    <td>{{$template_resource->template->name}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <table class="table table-condensed table-striped col-sm-6">
                <tbody>
                <tr>
                    <th class="col-sm-3">Resource</th>
                    <td>{{$template_resource->resource->name}}</td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td>{{$template_resource->resource->types->path}}</td>
                </tr>
                <tr>
                    <th>Rate</th>
                    <td>{{number_format($template_resource->resource->rate, 2)}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>


    <form action="{{route('template-resource.update', [$project, $template_resource])}}" method="post">
        {{csrf_field()}}
        {{method_field('delete')}}
        @foreach ($resources->groupBy('wbs_id') as $group)
            <article class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">{{$group->first()->wbs->path}} ({{$group->first()->wbs->code}})</h4>
                </div>

                <table class="table table-striped table-bordered">
                    <thead>
                    <tr class="bg-primary">
                        <th class="text-center"><input type="checkbox" name="" id="select-all"></th>
                        <th>WBS</th>
                        <th>Cost Account</th>
                        <th>Item Description</th>
                        <th>Budget Qty</th>
                        <th>Budget Unit</th>
                        <th>Budget Cost</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($group as $resource)
                        <tr>
                            <td class="text-center">
                                <input value="{{$resource->breakdown_resource->id}}" class="select-breakdown" type="checkbox" name="resources[{{$resource->breakdown_resource->id}}]">
                            </td>
                            <td>
                                <abbr title="{{$resource->wbs->path}}">{{$resource->wbs->code}}</abbr>
                            </td>
                            <td>{{$resource->cost_account}}</td>
                            <td>{{$resource->boq->description ?? ''}}</td>
                            <td>{{$resource->budget_qty}}</td>
                            <td>{{number_format($resource->budget_unit, 2)}}</td>
                            <td>{{number_format($resource->budget_cost, 2)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr class="info">
                        <th colspan="6" class="text-right">Total</th>
                        <th>{{number_format($group->sum('budget_cost'), 2)}}</th>
                    </tr>
                    </tfoot>
                </table>
            </article>
        @endforeach

        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Delete Template Resource</button>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="confirm-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title text-danger"><i class="fa fa-exclamation-circle"></i> Update Breakdowns
                        </h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead text-danger">Are you sure you want to continue without updating any breakdown?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="confirm-btn" class="btn btn-danger">Confirm
                            <i class="fa fa-chevron-right"></i></button>
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
                $(e.target).parents('article').find('.select-breakdown').prop('checked', e.target.checked);
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