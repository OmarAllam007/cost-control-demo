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
<template id="resourceTemplate">
    <div class="container-row">
        <div class="panel panel-primary panel-sm">
            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for="resourceType##">Resource Type</label>
                        <input class="form-control input-sm" type="text" name="resources[##][resource_type]" id="resourceType##" j-model="resource_type" readonly>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="resourceName##">Resource Name</label>
                        <input class="form-control input-sm" type="text" name="resources[##][resource_name]" id="resourceType##" j-model="resource_name" readonly>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="budgetQuantity##">Budget Qty</label>
                        <input class="form-control input-sm" type="text" name="resources[##][budget_quantity]" id="budgetQuantity##" j-model="budget_quantity">
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="engQuantity##">Eng Qty</label>
                        <input class="form-control input-sm" type="text" name="resources[##][eng_quantity]" id="engQuantity##" j-model="eng_quantity">
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="resourceWaste##">Resource Waste</label>
                        <input class="form-control input-sm" type="text" name="resources[##][resource_waste]" id="resourceWastete##" j-model="resource_waste">
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="laborsCount##">Labors Count</label>
                        <input class="form-control input-sm" type="text" name="resources[##][labors_count]" id="laborsCount##" j-model="labors_count">
                    </div>
                </div>
            </div>
        </div>
    </div>
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