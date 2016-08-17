{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
            {{ Form::select('name', $resource_types, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('parent_id', 'Sub Division', ['class' => 'control-label']) }}
            {{ Form::text('parent_id', '', ['class' => 'form-control']) }}
            {!! $errors->first('parent_id', '<div class="help-block">:message</div>') !!}
        </div>
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('resource_id', 'Resource', ['class' => 'control-label']) }}
            {{ Form::select('resource_id', $resources,1, ['class' => 'form-control']) }}
            {!! $errors->first('resource_id', '<div class="help-block">:message</div>') !!}
        </div>
        <!-- Continue working on your fields here -->
        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </div>
</div>
