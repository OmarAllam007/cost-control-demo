<div class="row">
    <div class="col-md-6 col-sm-9">
        <div class="form-group {{$errors->first('project_id', 'has-errors')}}">
            {{Form::label('project_id', 'Project', ['class' => 'control-label'])}}
            {{Form::select('project_id', App\Project::options(), request('project'), ['class' => 'form-control', 'id' => 'Activity-ID'])}}
        </div>

        <div class="form-group {{$errors->first('wbs_level_id', 'has-error')}}">
            {{ Form::label('wbs_level_id', 'WBS Level', ['class' => 'control-label']) }}
            <p>
                <a href="#LevelsModal" data-toggle="modal" id="select-parent">
                    {{Form::getValueAttribute('wbs_level_id')? App\WbsLevel::with('parent')->find(Form::getValueAttribute('wbs_level_id'))->path : 'Select WBS Level' }}
                </a>
            </p>
            {!! $errors->first('wbs_level_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group {{$errors->first('cost_account', 'has-errors')}}">
                    {{Form::label('cost_account', 'Cost Account', ['class' => 'control-label'])}}
                    {{Form::text('cost_account', null, ['class' => 'form-control'])}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group {{$errors->first('std_activity_id', 'has-errors')}}">
                    {{Form::label('std_activity_id', 'Standard Activity', ['class' => 'control-label'])}}
                    {{Form::select('std_activity_id', App\StdActivity::options(), null, ['class' => 'form-control', 'id' => 'ActivityID'])}}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{$errors->first('template_id', 'has-errors')}}">
                    {{Form::label('template_id', 'Breakdown Template', ['class' => 'control-label'])}}
                    {{Form::select('template_id', App\BreakdownTemplate::options(), null, ['class' => 'form-control', 'id' => 'TemplateID'])}}
                </div>
            </div>
        </div>

        
        
    </div>
</div>

<section id="resource">
    <h4 class="page-header">Resources</h4>
    <div id="resourcesContainer">
        <div class="alert alert-info"><i class="fa fa-info-circle"></i> Please select breakdown template</div>
    </div>
</section>

<template id="resourcesEmptyAlert"><div class="alert alert-info"><i class="fa fa-info-circle"></i> Please select breakdown template</div></template>
<template id="resourcesLoading"><div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Please select breakdown template</div></template>
<template id="resourcesError"><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Error loading breakdown resources</div></template>
<template id="containerTemplate">
    <div class="container-row">
        <table class="table" id="resourcesTable">
            <thead>
                <tr>
                    <th>Resource Type</th>
                    <th>Resource Name</th>
                    <th>Budget Qty</th>
                    <th>Eng Qty</th>
                    <th>Resource Waste</th>
                    <th>Labors Count</th>
                    <th>Productivity Ref</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</template>

<template id="resourceRowTemplate">
    <tr>
        <td>
            <input class="form-control input-sm" type="text" name="resources[##][resource_type]" id="resourceType##" j-model="resource_type" readonly>
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][resource_name]" id="resourceType##" j-model="resource_name" readonly>
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][budget_quantity]" id="budgetQuantity##" j-model="budget_quantity">
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][eng_quantity]" id="engQuantity##" j-model="eng_quantity">
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][resource_waste]" id="resourceWastete##" j-model="resource_waste">
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][labors_count]" id="laborsCount##" j-model="labors_count">
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][productivity_id]" id="laborsCount##" j-model="productivity_id">
        </td>
    </tr>
</template>


<div id="LevelsModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Select Parent</h4>
                </div>
                <div class="modal-body">
                    <ul class="list-unstyled tree">
                        @foreach(App\WbsLevel::tree()->get() as $level)
                        @include('wbs-level._recursive_input', ['level' => $level, 'input' => 'wbs_level_id'])
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
    </div>

    @section('javascript')
    <script src="/js/tree-select.js"></script>
    <script src="/js/breakdown.js"></script>
    @stop