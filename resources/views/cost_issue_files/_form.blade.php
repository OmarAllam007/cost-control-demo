<div class="row">
    <div class="col-sm-8">
        <div class="form-group {{$errors->first('subject', 'has-error')}}">
            {{ Form::label('subject', 'Subject', ['class' => 'control-label']) }}
            {{ Form::text('subject', null, ['class' => 'form-control']) }}
            {!! $errors->first('subject', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('period_id', 'has-error')}}">
            {{ Form::label('period_id','Period', ['class' => 'control-label']) }}
            {{ Form::select('period_id', $periods, null, ['class' => 'form-control', 'placeholder' => 'Select Period']) }}
            {!! $errors->first('period_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('file', 'has-error')}}">
            {{ Form::label('file', 'Issues File', ['class' => 'control-label']) }}
            {{ Form::file('file', ['class' => 'form-control']) }}
            {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('note', 'has-error')}}">
            {{ Form::label('note', 'Note', ['class' => 'control-label']) }}
            {{ Form::textarea('note', null, ['class' => 'form-control', 'rows' => 5]) }}
            {!! $errors->first('note', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</div>
