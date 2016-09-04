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


        <div class="form-group {{$errors->first('std_activity_id', 'has-errors')}}">
            {{Form::label('std_activity_id', 'Standard Activity', ['class' => 'control-label'])}}
            <p>
                <a href="#ActivitiesModal" data-toggle="modal" id="select-activity">
                    {{Form::getValueAttribute('std_activity_id')? App\StdActivity::find(Form::getValueAttribute('std_activity_id'))->path : 'Select Activity' }}
                </a>
            </p>
        </div>


        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
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
@section('javascript')
    <script src="/js/breakdown.js"></script>
    @endsection