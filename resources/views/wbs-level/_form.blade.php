{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('code', 'has-error')}}">
            {{ Form::label('code', 'Code', ['class' => 'control-label']) }}
            {{ Form::text('code', null, ['class' => 'form-control code-generator']) }}

        </div>


        <div class="form-group {{$errors->first('project_id', 'has-error')}}">
            {{ Form::label('project_id', 'Project', ['class' => 'control-label']) }}
            @if (request('project'))
                <p><em>{{App\Project::find(request('project'))->name}}</em></p>
                {{Form::hidden('project_id', request('project'))}}
            @else
                {{ Form::select('project_id', App\Project::options(), request('project'), ['class' => 'form-control']) }}
            @endif
            {!! $errors->first('project_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('wbs_id', 'has-error')}}">
            {{ Form::label('parent_id', 'Parent', ['class' => 'control-label']) }}
            <div class="hidden">
                {{ Form::select('parent_id', App\WbsLevel::options(), null, ['class' => 'form-control']) }}
            </div>
            <p>
                <a href="#WBSModal" data-toggle="modal" class="tree-open">
                    {{Form::getValueAttribute('parent_id')? App\WbsLevel::with('parent')->find(Form::getValueAttribute('parent_id'))->path : 'Select Wbs Level' }}
                </a>
                <a href="#" class="remove-tree-input" data-label="Select Wbs Level" data-target="#WBSModal"><span class="fa fa-times-cricle"></span></a>
            </p>
        </div>

        <div class="form-group {{$errors->first('description', 'has-error')}}">
            {{ Form::label('description', 'Description', ['class' => 'control-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control']) }}
            {!! $errors->first('description', '<div class="help-block">:message</div>') !!}
        </div>

        <!-- Continue working on your fields here -->

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </div>
</div>


@include('wbs-level._modal', ['input' => 'parent_id', 'value' => Form::getValueAttribute('parent_id'), 'project_id' => request('project', Form::getValueAttribute('project_id'))])

@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop