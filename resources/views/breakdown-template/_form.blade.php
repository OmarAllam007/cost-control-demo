<div class="row">
    <div class="col-md-6">

        @if (request('project') && request('import'))
            {{Form::label('project_id', 'Project', ['class' => 'control-label'])}}
            <p><em>{{\App\Project::find(request('project'))->name}}</em></p>
            {{Form::hidden('project_id', request('project'))}}
            <div class="form-group {{$errors->first('parent_template_id', 'has-error')}}">
                {{Form::label('parent_template_id', 'Template Name', ['class' => 'control-label']) }}
                {{Form::select('parent_template_id[]', \App\BreakdownTemplate::orderBy('name')->whereNull('project_id')->pluck('name', 'id'),null,['class'=>'form-control dropdown-templates','multiple'=>true])}}
                {!! $errors->first('parent_template_id', '<div class="help-block">:message</div>') !!}
            </div>

        @else
            @if(request('project'))
                {{Form::hidden('project_id', request('project'))}}
                {{Form::hidden('iframe', 'iframe')}}
                {{Form::hidden('import', 0)}}
            @endif
            <div class="form-group {{$errors->first('name', 'has-error')}}">
                {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
                {{ Form::text('name', null, ['class' => 'form-control']) }}
                {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
            </div>


            <div class="form-group {{$errors->first('std_activity_id', 'has-errors')}}">
                {{Form::label('std_activity_id', 'Standard Activity', ['class' => 'control-label'])}}
                <p>
                    <a href="#ActivitiesModal" data-toggle="modal" id="select-activity">
                        {{($activity_id = request('activity', Form::getValueAttribute('std_activity_id')))? App\StdActivity::find($activity_id)->name : 'Select Activity' }}
                    </a>
                    <a href="#" id="remove-parent"><span class="fa fa-times"></span></a>
                </p>
            </div>
    </div>
</div>

@include('std-activity._modal', ['value' => $activity_id])


@endif
<div class="form-group">
    <button class="btn btn-success" id="createTemplate"><i class="fa fa-check"></i> Submit</button>
</div>
@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>

@stop
<breakdown project="{{request('project')}}"></breakdown>