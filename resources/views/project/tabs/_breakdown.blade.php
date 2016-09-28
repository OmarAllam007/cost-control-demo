<div class="form-group tab-actions clearfix">
    <a href="{{route('breakdown.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm pull-right">
        <i class="fa fa-plus"></i> Add Breakdown
    </a>
</div>

{{Form::open(['route' => ['breakdown.filters', $project], 'class' => 'row filter-form'])}}
<div class="col-sm-2">
    <div class="form-group form-group-sm">
        {{Form::label('wbs_id', 'WBS Level', ['class' => 'control-label'])}}
        <p>
            <a href="#WBSModal" data-toggle="modal" class="tree-open">
                {{ session('filters.breakdown.'.$project->id.'.wbs_id')? App\WbsLevel::find(session('filters.breakdown.'.$project->id.'.wbs_id'))->path : 'Select WBS Level' }}
            </a>
            <a href="#" class="remove-tree-input" data-target="#WBSModal" data-label="Select WBS Level"><span class="fa fa-times-circle"></span></a>
        </p>
    </div>
</div>

<div class="col-sm-2">
    <div class="form-group form-group-sm">
        {{Form::label('activity', 'Activity', ['class' => 'control-label'])}}
        <p>
            <a href="#ActivitiesModal" data-toggle="modal" class="tree-open">
                {{ session('filters.breakdown.'.$project->id.'.activity')? App\StdActivity::find(session('filters.breakdown.'.$project->id.'.activity'))->name : 'Select Activity' }}
            </a>
            <a href="#" class="remove-tree-input" data-target="#ActivitiesModal" data-label="Select Activity"><span class="fa fa-times-circle"></span></a>
        </p>
    </div>
</div>

<div class="col-sm-2">
    <div class="form-group form-group-sm">
        {{Form::label('cost_account', 'Cost Account', ['class' => 'control-label'])}}
        {{Form::text('cost_account', session('filters.breakdown.' . $project->id . '.cost_account'), ['class' => 'form-control'])}}
    </div>
</div>

<div class="col-sm-2">
    <div class="form-group form-group-sm">
        {{Form::label('resource_type', 'Resource Type', ['class' => 'control-label'])}}
        <p>
            <a href="#ResourceTypeModal" data-toggle="modal" class="tree-open">
                {{session('filters.breakdown.'.$project->id.'.resource_type')? App\ResourceType::with('parent')->find(session('filters.breakdown.'.$project->id.'.resource_type'))->path : 'Select Resource Type' }}
            </a>
            <a href="#" class="remove-tree-input" data-target="#ResourceTypeModal" data-label="Select Resource Type"><span class="fa fa-times-circle"></span></a>
        </p>

    </div>
</div>

<div class="col-sm-2">
    <div class="form-group form-group-sm">
        {{Form::label('resource', 'Resource Name', ['class' => 'control-label'])}}
        {{Form::text('resource', session('filters.breakdown.'.$project->id.'.resource'), ['class' => 'form-control'])}}
    </div>
</div>

<div class="col-sm-2">
    <div class="form-group form-group-sm">
        <button class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Filter</button>
    </div>
</div>

@include('resource-type._modal', ['input' => 'resource_type', 'value' => session('filters.breakdown.'.$project->id.'.resource_type')])
@include('std-activity._modal', ['input' => 'activity', 'value' => session('filters.breakdown.'.$project->id.'.activity')])
@include('wbs-level._modal', ['input' => 'wbs_id', 'value' => session('filters.breakdown.'.$project->id.'.wbs_id'), 'project_id' => $project->id])
{{Form::close()}}

@if ($project->breakdown_resources->count())
    <div class="scrollpane">
        <table class="table table-condensed table-striped table-hover table-breakdown">
            <thead>
            <tr>
                <th style="min-width: 150px; max-width: 150px;" class="bg-black">WBS</th>
                <th style="min-width: 200px; max-width: 200px;" class="bg-primary">Activity</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-black">Breakdown Template</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-primary">Cost Account</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Eng. Qty.</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Budget Qty.</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-primary">Resource Qty.</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Resource Waste</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Resource Type</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Resource Code</th>
                <th style="min-width: 200px; max-width: 200px;" class="bg-success">Resource Name</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Price/Unit</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Unit of measure</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Budget Unit</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Budget Cost</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-black">BOQ Equivalent Unit Rate</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-primary">No. Of Labors</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Productivity (Unit/Day)</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-primary">Productivity Ref</th>
                <th style="min-width: 150px; max-width: 150px;" class="bg-success">Remarks</th>
            </tr>
            </thead>
        </table>
        <table class="table table-condensed table-striped table-hover table-breakdown">
            <tbody>
            @foreach($project->breakdown_resources as $resource)
                <tr>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-black">
                        <abbr title="{{$resource->breakdown->wbs_level->path}}">{{$resource->breakdown->wbs_level->code}}</abbr>
                    </td>
                    <td style="min-width: 200px; max-width: 200px;" class="bg-primary">{{$resource->breakdown->std_activity->name}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-black">{{$resource->breakdown->template->name}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-primary">{{$resource->breakdown->cost_account}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="">{{number_format($resource->eng_qty, 2)}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="">{{number_format($resource->budget_qty, 2)}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-primary">{{number_format($resource->resource_qty, 2)}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="">{{number_format($resource->project_resource->resource_waste, 2)}}
                        %
                    </td>
                    <td style="min-width: 150px; max-width: 150px;" class="">{{$resource->resource->types->root->name or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="">{{$resource->resource->resource->resource_code or ''}}</td>
                    <td style="min-width: 200px; max-width: 200px;" class="">{{$resource->resource->resource->name or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="">{{$resource->project_resource->rate or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="">{{$resource->project_resource->units->type or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="">{{number_format($resource->budget_unit, 2)}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="">{{number_format($resource->budget_cost, 2)}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-black">{{number_format($resource->boq_unit_rate, 2)}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-primary">{{$resource->labor_count or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="">{{$resource->project_productivity->after_reduction or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-primary">{{$resource->productivity->csi_code or ''}}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="">{{$resource->remarks}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No breakdowns added</div>
@endif