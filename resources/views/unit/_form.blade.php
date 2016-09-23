{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('type', 'has-error')}}">
            {{ Form::label('type', 'Name', ['class' => 'control-label']) }}
            {{ Form::text('type', null, ['class' => 'form-control']) }}
            {!! $errors->first('type', '<div class="help-block">:message</div>') !!}
        </div>

        <!-- Continue working on your fields here -->

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Add Unit</button>
        </div>
    </div>
</div>
