<div class="form-group tab-actions clearfix">
    <a href="{{route('breakdown.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm pull-right">
        <i class="fa fa-plus"></i> Add Breakdown
    </a>
</div>

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
                    <td class="bg-black"><abbr title="{{$resource->breakdown->wbs_level->path}}">{{$resource->breakdown->wbs_level->code}}</abbr></td>
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