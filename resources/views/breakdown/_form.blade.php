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
            @endif
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


        <div class="form-group {{$errors->first('cost_account', 'has-errors')}}">
            {{Form::label('cost_account', 'Cost Account', ['class' => 'control-label'])}}
            {{Form::text('cost_account', null, ['class' => 'form-control', 'id' => 'CostAccount'])}}
        </div>


        <div class="form-group {{$errors->first('std_activity_id', 'has-errors')}}">
            {{Form::label('std_activity_id', 'Standard Activity', ['class' => 'control-label'])}}
            {{--{{Form::select('std_activity_id', App\StdActivity::options(), null, ['class' => 'form-control', 'id' => 'ActivityID'])}}--}}
            <p>
                <a href="#ActivitiesModal" data-toggle="modal" id="select-activity">
                    {{Form::getValueAttribute('std_activity_id')? App\StdActivity::find(Form::getValueAttribute('std_activity_id'))->path : 'Select Activity' }}
                </a>
            </p>
        </div>
    <div class="form-group {{$errors->first('template_id', 'has-errors')}}">
        {{Form::label('template_id', 'Breakdown Template', ['class' => 'control-label'])}}
        {{Form::select('template_id', App\BreakdownTemplate::options(), null, ['class' => 'form-control', 'id' => 'TemplateID'])}}
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
                    @foreach(App\WbsLevel::forProject(request('project'))->tree()->get() as $level)
                        @include('wbs-level._recursive_input', ['level' => $level, 'input' => 'wbs_level_id'])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="ActivitiesModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Parent</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\ActivityDivision::with('activities')->tree()->get() as $division)
                        @include('std-activity._recursive_activity_input', ['division' => $division, 'input' => 'std_activity_id'])
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
    <script src="/js/breakdown.js"></script>
    <script>
        jQuery(function ($) {
            $('#CostAccount').completeList({
                url: '/api/cost-accounts'
            });
        });
    </script>
@stop