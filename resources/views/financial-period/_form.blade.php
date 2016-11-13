{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('start_date', 'has-error')}}">
            {{ Form::label('start_date', null, ['class' => 'control-label','id'=>'start_date']) }}
            {{ Form::date('start_date', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('start_date', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('end_date', 'has-error')}}">
            {{ Form::label('end_date', null, ['class' => 'control-label','id'=>'end_date']) }}
            {{ Form::date('end_date', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('end_date', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('open', 'has-error')}}">
            {{ Form::label('open', null, ['class' => 'control-label']) }}
            {{ Form::checkbox('open', 1,false) }}
            {!! $errors->first('open', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('opened_time', 'has-error')}}">
            {{ Form::label('opened_time', null, ['class' => 'control-label']) }}
            {{ Form::datetimeLocal('opened_time', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('opened_time', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('closed_time', 'has-error')}}">
            {{ Form::label('closed_time', null, ['class' => 'control-label']) }}
            {{ Form::datetimeLocal('closed_time', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('closed_time', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('description', 'has-error')}}">
            {{ Form::label('description', null, ['class' => 'control-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('description', '<div class="help-block">:message</div>') !!}
        </div>


        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Add</button>
        </div>
    </div>
</div>
