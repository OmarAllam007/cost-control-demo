<div class="clearfix">
    <div class="form-group tab-actions pull-right">
        <a style="margin-left: 2px;" href="{{route('break_down.export', ['project' => $project->id])}}"
           class="btn btn-info btn-sm">
            <i class="fa fa-cloud-download"></i> Export
        </a>
        <a href="{{route('breakdown.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Add Breakdown
        </a>

        @can('wipe')
            <a href="#WipeBreakdownModal" data-toggle="modal" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>
                Delete all</a>
        @endcan
    </div>
</div>

@include('project.filters._breakdown')

@if ($project->breakdown_resources->count())
    <div class="scrollpane">
        <table class="table table-condensed table-striped table-hover table-breakdown">
            <thead>
            <tr>
                <th style="min-width: 32px; max-width: 32px">&nbsp;</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-black">WBS</th>
                <th style="min-width: 300px; max-width: 300px;" class="bg-primary">Activity</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-black">Breakdown Template</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-primary">Cost Account</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Eng. Qty.</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Budget Qty.</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-primary">Resource Qty.</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-info">Resource Waste</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Resource Type</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Resource Code</th>
                <th style="min-width: 200px; max-width: 200px;" class="bg-success">Resource Name</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-info">Price/Unit</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-info">Unit of measure</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Budget Unit</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Budget Cost</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-black">BOQ Equivalent Unit Rate</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-primary">No. Of Labors</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-info">Productivity (Unit/Day)</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-primary">Productivity Ref</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Remarks</th>
            </tr>
            </thead>
        </table>
        <table class="table table-condensed table-striped table-hover table-breakdown">
            <tbody>
            @foreach($project->breakdown_resources as $resource)
                <tr>
                    <td style="min-width: 32px; max-width: 32px;">
                        {{Form::open(['route' => ['breakdown-resource.destroy', $resource], 'class' => 'dropdown', 'method' => 'delete'])}}
                        <button data-toggle="dropdown" type="button" class="btn btn-default btn-xs dropdown-toggle">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left">
                            <li><a href="{{route('breakdown-resource.edit', $resource)}}" class="btn btn-link in-iframe"><i class="fa fa-fw fa-edit"></i> Edit</a></li>
                            <li><a href="{{route('breakdown.duplicate', $resource->breakdown)}}" class="btn btn-link in-iframe"><i class="fa fa-fw fa-copy"></i> Duplicate</a></li>
                            <li><button class="btn btn-link"><i class="fa fa-fw fa-trash"></i> Delete</button></li>
                        </ul>
                        {{Form::close()}}
                    </td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-black">
                        <abbr title="{{$resource->breakdown->wbs_level->path}}">{{$resource->breakdown->wbs_level->code}}</abbr>
                    </td>
                    <td style="min-width: 300px; max-width: 300px;"
                        class="bg-primary">{{$resource->breakdown->std_activity->name}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-black">{{$resource->breakdown->template->name}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-primary">{{$resource->breakdown->cost_account}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="">{{number_format($resource->eng_qty, 2)}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="">{{number_format($resource->budget_qty, 2)}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-primary">{{number_format($resource->resource_qty, 2)}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-info">{{number_format($resource->project_resource->waste, 2)}}%
                    </td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="">{{$resource->resource->types->root->name or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="">{{$resource->resource->resource_code or ''}}</td>
                    <td style="min-width: 200px; max-width: 200px;"
                        class="">{{$resource->resource->name or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-info">{{$resource->resource->rate or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-info">{{$resource->resource->units->type or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="">{{number_format($resource->budget_unit, 2)}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="">{{number_format($resource->budget_cost, 2)}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-black">{{number_format($resource->boq_unit_rate, 2)}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-primary">{{$resource->labor_count or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-info">{{$resource->project_productivity->after_reduction or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-primary">{{$resource->productivity->csi_code or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="">{{$resource->remarks}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No breakdowns added</div>
@endif

<div class="modal fade" tabindex="-1" id="EditResourceModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span>
                </button>
                <h4 class="modal-title">Edit resource</h4>
            </div>
            <div class="modal-body iframe">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="WipeBreakdownModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <form method="post" action="{{route('breakdown.wipe', $project)}}" class="modal-content">
            {{csrf_field()}} {{method_field('delete')}}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Delete all breakdown</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete all breakdowns?</div>
                <input type="hidden" name="wipe" value="1">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete all</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </form>
    </div>
</div>