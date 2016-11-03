{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('parent_id', 'has-error')}}">
            {{ Form::label('parent_id', 'Category', ['class' => 'control-label']) }}
            <div class="hidden">
                {{ Form::select('parent_id', App\CsiCategory::options(), null, ['class' => 'form-control']) }}
            </div>
            <p>
                <a href="#LevelsModal" data-toggle="modal" class="tree-open">
                    {{Form::getValueAttribute('parent_id')? App\CsiCategory::with('parent')->find(Form::getValueAttribute('parent_id'))->path : 'Select Category' }}
                </a>
                <a class="remove-tree-input" data-target="#LevelsModal" data-label="Select Category"><span class="fa fa-times"></span></a>

            </p>
            {!! $errors->first('parent_id', '<div class="help-block">:message</div>') !!}
        </div>


        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
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
                    @foreach(App\CsiCategory::tree()->get() as $level)
                        @include('csi-category._recursive_input', compact('level'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop
