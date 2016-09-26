{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">

        <div class="form-group {{$errors->first('parent_id', 'has-error')}}">
            {{ Form::label('parent_id', 'Resource Type', ['class' => 'control-label']) }}
            <p>
                <a href="#ParentsModal" data-toggle="modal" class="tree-open">
                    {{Form::getValueAttribute('parent_id')? App\ResourceType::with('parent')->find(Form::getValueAttribute('parent_id'))->path : 'Select Resource Type' }}
                </a>
                <a class="remove-tree-input" data-target="#ParentsModal" data-label="Select Resource Type"><span class="fa fa-times"></span></a>
            </p>
            {!! $errors->first('division_id', '<div class="help-block">:message</div>') !!}
        </div>
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>


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
                <h4 class="modal-title">Select Division Type</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\ResourceType::tree()->get() as $division)
                        @include('resource-type._recursive_input', compact('division'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>


@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop