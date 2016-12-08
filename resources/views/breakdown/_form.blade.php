<div class="row">
    <div class="col-md-6 col-sm-9">
        <div class="form-group {{$errors->first('project_id', 'has-errors')}}">
            {{Form::label('project_id', 'Project', ['class' => 'control-label'])}}
            @if (request('project'))
                <p><em>{{App\Project::find(request('project'))->name}}</em></p>
                {{Form::hidden('project_id', request('project'), ['id' => 'ProjectId'])}}

            @else
                {{--{{Form::select('project_id', App\Project::options(), request('project'), ['class' => 'form-control', 'id' => 'Activity-ID'])}}--}}
                <p><em>{{$breakdown->project->name}}</em></p>
                {{Form::hidden('project_id', $breakdown->project->name, ['id' => 'ProjectId'])}}

                <input hidden value="{{$breakdown->project->id}}" id="project_id">
            @endif
        </div>
        <div class="form-group {{$errors->first('wbs_level_id', 'has-error')}}">
            {{ Form::label('wbs_level_id', 'WBS Level', ['class' => 'control-label']) }}
            <p>
                <a href="#WBSModal" data-toggle="modal" id="select-parent">
                    @if(request('wbs_id'))
                        {{request('wbs_id')? \App\WbsLevel::find(request('wbs_id'))->path: 'Select WBS Level' }}
                        {{Form::hidden('wbs_level_id',request('wbs_id'),['id'=>'WbsID'])}}
                    @else
                        {{Form::getValueAttribute('wbs_level_id')? App\WbsLevel::with('parent')->find(Form::getValueAttribute('wbs_level_id'))->path
                        : 'Select WBS Level' }}
                    @endif
                </a>
            </p>
            {!! $errors->first('wbs_level_id', '<div class="help-block">:message</div>') !!}
        </div>


        <div class="form-group {{$errors->first('cost_account', 'has-errors')}}">
            {{Form::label('cost_account', 'Cost Account', ['class' => 'control-label'])}}
            {{Form::text('cost_account', null, ['class' => 'form-control', 'id' => 'CostAccount'])}}
        </div>


        <div class="form-group {{$errors->first('std_activity_id', 'has-errors')}}">
            {{Form::label('std_activity_id', 'Standard Activity', ['class' => 'control-label'])}}
            {{--{{Form::select('std_activity_id', App\StdActivity::options(), null, ['class' => 'form-control', 'id' => 'ActivityID'])}}--}}
            <p>
                <a href="#ActivitiesModal" data-toggle="modal" id="select-activity">
                    {{Form::getValueAttribute('std_activity_id')?
                    App\StdActivity::find(Form::getValueAttribute('std_activity_id'))->path : 'Select Activity' }}
                </a>
            </p>
        </div>

        <div class="form-group {{$errors->first('template_id', 'has-errors')}}">
            {{Form::label('template_id', 'Breakdown Template', ['class' => 'control-label'])}}
            {{Form::select('template_id', ['' => 'Select Template'], null,
            ['class' => 'form-control', 'id' => 'TemplateID'])}}
        </div>
        <input type="hidden" name="code">
        <div class="form-group" id="SetVariablesPane">
            <a href="#VariablesModal" class="btn btn-info" data-toggle="modal"><i class="fa fa-dollar"></i> Set
                Variables</a>
        </div>
    </div>
</div>

<section id="resource">
    <h4 class="page-header">Resources</h4>
    @if (old('resources'))
        @include('breakdown._resource_container', ['include' => true])
    @else
        <div id="resourcesContainer">
            <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Please select cost account and
                breakdown template
            </div>
        </div>
    @endif
</section>

@include('breakdown._template')
@include('wbs-level._modal', ['value' => Form::getValueAttribute('wbs_level_id')?Form::getValueAttribute('wbs_level_id'):request('wbs_level_id'), 'input' => 'wbs_level_id', 'project_id' => request('project', Form::getValueAttribute('project_id'))])
@include('std-activity._modal', ['input' => 'std_activity_id', 'value' => Form::getValueAttribute('std_activity_id')])

<div class="form-group">
    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
</div>

@section('javascript')
    <script src="/js/breakdown.js"></script>

    <script>

        jQuery(function ($) {
            $('#CostAccount').completeList({
                url: '/api/cost-accounts?project={{request('project')}}'
            });
        });
    </script>
@stop