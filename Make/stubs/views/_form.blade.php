@{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group @{{$errors->first('name', 'has-error')}}">
            @{{ Form::label('name', 'Name', ['class' => 'control-label']) }}
            @{{ Form::text('name', null, ['class' => 'form-control']) }}
            {{'{' . '!!'}} $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <!-- Continue working on your fields here -->

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </div>
</div>
