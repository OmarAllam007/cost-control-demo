<div class="form-group {{$errors->first('std_activity_id', 'has-errors')}}">
    {{Form::label('std_activity_id', 'Standard Activity', ['class' => 'control-label'])}}
    {{Form::select('std_activity_id', App\Activity::options(), null, ['class' => 'form-control', 'id' => 'Activity-ID'])}}
</div>

<div class="form-group {{$errors->first('template_id', 'has-errors')}}">
    {{Form::label('template_id', 'Breakdown Template', ['class' => 'control-label'])}}
    {{Form::select('template_id', App\Activity::options(), null, ['class' => 'form-control', 'id' => 'Template-ID'])}}
</div>




@section('javascript')
    <script src="/js/breakdown.js"></script>
@stop