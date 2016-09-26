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
                {{ session('resource_type')? App\WbsLevel::find(session('resource_type'))->path : 'Select WBS Level' }}
            </a>
            <a href="#" class="remove-tree-input" data-target="#WBSModal" data-label="Select WBS Level"><span class="fa fa-times-circle"></span></a>
        </p>
    </div>
</div>

<div class="col-sm-2">
    <div class="form-group form-group-sm">
        {{Form::label('activity', 'Activity', ['class' => 'control-label'])}}
        {{Form::select('activity', App\StdActivity::options(), session('filters.breakdown.' . $project->id . '.activity'), ['class' => 'form-control'])}}
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
                {{session('resource_type')? App\ResourceType::with('parent')->find(session('resource_type'))->path : 'Select Resource Type' }}
            </a>
            <a href="#" class="remove-tree-input" data-target="#ResourceTypeModal" data-label="Select Resource Type"><span class="fa fa-times-circle"></span></a>
        </p>

    </div>
</div>

<div class="col-sm-2">
    <div class="form-group form-group-sm">
        {{Form::label('resource', 'Resource Name', ['class' => 'control-label'])}}
        {{Form::text('resource', session('filters.breakdown.' . $project->id . '.resource'), ['class' => 'form-control'])}}
    </div>
</div>

<div class="col-sm-2">
    <div class="form-group form-group-sm">
        <button class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Filter</button>
    </div>
</div>

@include('resource-type._modal', ['input' => 'resource_type', 'value' => session('resource_type')])
@include('wbs-level._modal', ['input' => 'wbs_id', 'value' => session('wbs_id'), 'project_id' => $project->id])
{{Form::close()}}

@if ($project->breakdown_resources->count())
    <div class="scrollpane">
        <table class="table table-condensed">
            <thead>
            <tr>
                <th class="bg-black">WBS</th>
                <th class="bg-primary">Activity</th>
                <th class="bg-black">Breakdown Template</th>
                <th class="bg-primary">Cost Account</th>
                <th class="bg-success">Eng. Qty.</th>
                <th class="bg-success">Budget Qty.</th>
                <th class="bg-primary">Resource Qty.</th>
                <th class="bg-success">Resource Waste</th>
                <th class="bg-success">Resource Type</th>
                <th class="bg-success">Resource Code</th>
                <th class="bg-success">Resource Name</th>
                <th class="bg-success">Price/Unit</th>
                <th class="bg-success">Unit of measure</th>
                <th class="bg-success">Budget Unit</th>
                <th class="bg-success">Budget Cost</th>
                <th class="bg-black">BOQ Equivalent Unit Rate</th>
                <th class="bg-primary">No. Of Labors</th>
                <th class="bg-success">Productivity (Unit/Day)</th>
                <th class="bg-primary">Productivity Ref</th>
                <th class="bg-success">Remarks</th>
            </tr>
            </thead>
            <tbody>
            @foreach($project->breakdown_resources as $resource)
                <tr>
                    <td class="bg-black">
                        <abbr title="{{$resource->breakdown->wbs_level->path}}">{{$resource->breakdown->wbs_level->code}}</abbr>
                    </td>
                    <td class="bg-primary">{{$resource->breakdown->std_activity->name}}</td>
                    <td class="bg-black">{{$resource->breakdown->template->name}}</td>
                    <td class="bg-primary">{{$resource->breakdown->cost_account}}</td>
                    <td class="bg-success">{{number_format($resource->eng_qty, 2)}}</td>
                    <td class="bg-success">{{number_format($resource->budget_qty, 2)}}</td>
                    <td class="bg-primary">{{number_format($resource->resource_qty, 2)}}</td>
                    <td class="bg-success">{{$resource->resource_waste}}%</td>
                    <td class="bg-success">{{$resource->project_resource->types->root->name or ''}}</td>
                    <td class="bg-success">{{$resource->project_resource->resource_code or ''}}</td>
                    <td class="bg-success">{{$resource->project_resource->name or ''}}</td>
                    <td class="bg-success">{{$resource->project_resource->rate or ''}}</td>
                    <td class="bg-success">{{$resource->project_resource->units->type or ''}}</td>
                    <td class="bg-success">{{number_format($resource->budget_unit, 2)}}</td>
                    <td class="bg-success">{{number_format($resource->budget_cost, 2)}}</td>
                    <td class="bg-black">{{number_format($resource->boq_unit_rate, 2)}}</td>
                    <td class="bg-primary">{{$resource->labor_count or ''}}</td>
                    <td class="bg-success">{{$resource->project_productivity->after_reduction or ''}}</td>
                    <td class="bg-primary">{{$resource->project_productivity->csi_code or ''}}</td>
                    <td class="bg-success">{{$resource->remarks}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No breakdowns added</div>
@endif