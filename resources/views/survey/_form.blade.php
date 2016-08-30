<div class="row">
    <div class="col-md-6">

        <div class="form-group {{$errors->first('project_id', 'has-error')}}">
            {{ Form::label('project_id', 'Project Name', ['class' => 'control-label']) }}
            <div >
                {{ Form::select('project_id', App\Project::options(), null, ['class' => 'form-control']) }}
            </div>
            {!! $errors->first('project_id', '<div class="help-block">:message</div>') !!}
        </div>


        <div class="form-group {{$errors->first('wbs_level_id', 'has-error')}}">
            {{ Form::label('wbs_level_id', 'Wbs Level', ['class' => 'control-label']) }}
            <div class="hidden">
                {{ Form::select('wbs_level_id', App\WbsLevel::options(), null, ['class' => 'form-control']) }}
            </div>
            <p>
                <a href="#LevelsModal"  data-toggle="modal" id="select-parent">
                    {{Form::getValueAttribute('wbs_level_id')? App\WbsLevel::with('parent')->find(Form::getValueAttribute('wbs_level_id'))->path : 'Select Wbs Level' }}
                </a>
            </p>
            {!! $errors->first('wbs_level_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('category_id', 'has-error')}}">
            {{Form::label('category_id','Category')}}
            {{Form::select('category_id', App\Category::options(), null, ['class'=>'form-control'])}}
        </div>

        <div class="form-group {{$errors->first('description', 'has-error')}}">
            {{Form::label('item','Item Description')}}
            {{Form::textarea('description',null,['class'=>'form-control'])}}
        </div>

        <div class="form-group {{$errors->first('unit_id', 'has-error')}}">
            {{Form::label('units','Unit of measure')}}
            {{Form::select('unit_id',App\Unit::options(),['class'=>'form-control'],['class'=>'form-control'])}}
        </div>

        <div class="form-group {{$errors->first('budget_qty', 'has-error')}}">
            {{Form::label('budget_qty','Budget Quantity')}}
            {{Form::text('budget_qty',null,['class'=>'form-control'])}}
        </div>

        <div class="form-group {{$errors->first('eng_qty', 'has-error')}}">
            {{Form::label('eng_qty','Eng Quantity')}}
            {{Form::text('eng_qty',null,['class'=>'form-control'])}}
        </div>

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Save</button>
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
                    @foreach(App\WbsLevel::tree()->get() as $level)
                        @include('survey._recursive_input', compact('level'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>


@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop

