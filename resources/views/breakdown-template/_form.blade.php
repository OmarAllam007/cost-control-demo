<div class="row">
    <div class="col-md-6">

        @if (request('project'))
            {{Form::label('project_id', 'Project', ['class' => 'control-label'])}}
            <p><em>{{\App\Project::find(request('project'))->name}}</em></p>
            {{Form::hidden('project_id', request('project'))}}

            <div class="form-group {{$errors->first('name', 'has-error')}}">
                {{Form::label('name', 'Template Name', ['class' => 'control-label']) }}
                {{Form::select('name', \App\BreakdownTemplate::pluck('name', 'name')->prepend('Select Template',0),null,['class'=>'form-control'])}}
                {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group {{$errors->first('wbs_id', 'has-error')}}">
                {{ Form::label('wbs_id', 'WBS Level', ['class' => 'control-label']) }}
                <p>
                    <a href="#WBSModal" data-toggle="modal" id="select-parent">
                        {{Form::getValueAttribute('wbs_id')? App\WbsLevel::with('parent')->find(Form::getValueAttribute('wbs_id'))->path : 'Select WBS Level' }}
                    </a>
                </p>
                {!! $errors->first('wbs_id', '<div class="help-block">:message</div>') !!}
            </div>


        @else

            <div class="form-group {{$errors->first('name', 'has-error')}}">
                {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
                {{ Form::text('name', null, ['class' => 'form-control']) }}
                {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
            </div>


        @endif

        <div class="form-group {{$errors->first('std_activity_id', 'has-errors')}}">
            {{Form::label('std_activity_id', 'Standard Activity', ['class' => 'control-label'])}}
            <p>
                <a href="#ActivitiesModal" data-toggle="modal" id="select-activity">
                    {{($activity_id = request('activity', Form::getValueAttribute('std_activity_id')))? App\StdActivity::find($activity_id)->name : 'Select Activity' }}
                </a>
                <a href="#" id="remove-parent"><span class="fa fa-times"></span></a>
            </p>
        </div>

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </div>
</div>

@include('std-activity._modal', ['value' => $activity_id])
{{--@include('wbs-level._modal', ['value' => Form::getValueAttribute('wbs_id'), 'input' => 'wbs_id', 'project_id' => request('project', Form::getValueAttribute('project_id'))])--}}

@section('javascript')

@endsection