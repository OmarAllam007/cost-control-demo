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
            {{ Form::label('division_id', 'Division', ['class' => 'control-label']) }}
            <p>
                <a href="#ParentsModal" data-toggle="modal" id="select-parent">
                    {{Form::getValueAttribute('division_id')? App\ActivityDivision::with('parent')->find(Form::getValueAttribute('division_id'))->path : 'Select Division' }}
                </a>
            </p>
            {!! $errors->first('division_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('work_package_name', 'has-error')}}">
            {{ Form::label('work_package_name', 'Work Package Name', ['class' => 'control-label']) }}
            {{ Form::text('work_package_name', null, ['class' => 'form-control']) }}
            {!! $errors->first('work_package_name', '<div class="help-block">:message</div>') !!}
        </div>
        <!-- Continue working on your fields here -->

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </div>
</div>

@include('std-activity._division_modal', ['value' => Form::getValueAttribute('division_id')])

@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop