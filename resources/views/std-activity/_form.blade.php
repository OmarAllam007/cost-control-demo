{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('code', 'has-error')}}">
            {{ Form::label('code', 'Code', ['class' => 'control-label']) }}
            {{ Form::text('code', null, ['class' => 'form-control']) }}
            {!! $errors->first('code', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('division_id', 'has-error')}}">
            {{ Form::label('parent_id', 'Parent', ['class' => 'control-label']) }}
            <p>
                <a href="#ParentsModal" data-toggle="modal" id="select-parent">
                    {{Form::getValueAttribute('parent_id')? App\ActivityDivision::with('parent')->find(Form::getValueAttribute('parent_id'))->path : 'Select Parent' }}
                </a>
            </p>
            {!! $errors->first('division_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('id_partial', 'has-error')}}">
            {{ Form::label('id_partial', 'ID Partial', ['class' => 'control-label']) }}
            {{ Form::text('id_partial', null, ['class' => 'form-control']) }}
            {!! $errors->first('id_partial', '<div class="help-block">:message</div>') !!}
        </div>
        <!-- Continue working on your fields here -->

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </div>
</div>

<div id="ParentsModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Division</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\ActivityDivision::tree()->get() as $division)
                        @include('std-activity._recursive_input', compact('division'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop