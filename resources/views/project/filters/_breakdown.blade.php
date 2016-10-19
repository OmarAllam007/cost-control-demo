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