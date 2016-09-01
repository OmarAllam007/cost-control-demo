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
            {{ Form::text('code', null, ['class' => 'form-control']) }}
            {!! $errors->first('code', '<div class="help-block">:message</div>') !!}
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
            {{ Form::label('wbs_id', 'Parent', ['class' => 'control-label']) }}
            <div class="hidden">
                {{ Form::select('wbs_id', App\WbsLevel::options(), null, ['class' => 'form-control']) }}
            </div>
            <p>
                <a href="#LevelsModal" data-toggle="modal" id="select-parent">
                    {{Form::getValueAttribute('wbs_id')? App\WbsLevel::with('parent')->find(Form::getValueAttribute('wbs_id'))->path : 'Select Parent' }}
                </a>
            </p>
            {!! $errors->first('wbs_id', '<div class="help-block">:message</div>') !!}
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
                    @foreach(App\WbsLevel::forProject(request('project') ?: $wbs_level->project_id)->tree()->get() as $level)
                        @include('wbs-level._recursive_input', compact('level'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop