{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">

        <div class="form-group {{$errors->first('parent_id', 'has-error')}}">
            {{ Form::label('parent_id', 'Resource Type', ['class' => 'control-label']) }}
            <p>
                <a href="#ResourceTypeModal" data-toggle="modal" class="tree-open">
                    {{Form::getValueAttribute('parent_id')? App\ResourceType::with('parent')->find(Form::getValueAttribute('parent_id'))->path : 'Select Resource Type' }}
                </a>
                <a class="remove-tree-input" data-target="#ResourceTypeModal" data-label="Select Resource Type"><span class="fa fa-times"></span></a>
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

@include('resource-type._modal', ['value' => Form::getValueAttribute('parent_id'), 'input' => 'parent_id'])


@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop