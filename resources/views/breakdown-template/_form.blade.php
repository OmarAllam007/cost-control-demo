<div class="row">
    <div class="col-md-6">
        {{--<div class="form-group {{$errors->first('code', 'has-error')}}">--}}
            {{----}}
            {{--{{ Form::label('code', 'Code', ['class' => 'control-label']) }}--}}
            {{--{{ Form::text('code', null, ['class' => 'form-control']) }}--}}
            {{--{!! $errors->first('code', '<div class="help-block">:message</div>') !!}--}}
        {{--</div>--}}

        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>


        <div class="form-group {{$errors->first('std_activity_id', 'has-errors')}}">
            {{Form::label('std_activity_id', 'Standard Activity', ['class' => 'control-label'])}}
            <p>
                <a href="#ActivitiesModal" data-toggle="modal" id="select-activity">
                    {{Form::getValueAttribute('std_activity_id')? App\StdActivity::find(Form::getValueAttribute('std_activity_id'))->name : 'Select Activity' }}
                </a>
                <a id="remove-parent"><span class="fa fa-times"></span></a>
            </p>
        </div>

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </div>
</div>

@include('std-activity._modal', ['value' => Form::getValueAttribute('std_activity_id')])
@section('javascript')
    <script src="/js/breakdown.js"></script>
    @endsection