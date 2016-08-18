{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('resource_type', 'Resource type', ['class' => 'control-label']) }}
            {{  Form::select('resource_type_id',$resource_types,null, ['class' => 'form-control']) }}
            {!! $errors->first('resource_type_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">

                {{ Form::label('resource_code', 'Resource Code', ['class' => 'control-label']) }}
                {{ Form::text('resource_code',null, ['class' => 'form-control']) }}
                {!! $errors->first('resource_code', '<div class="help-block">:message</div>') !!}

        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('rate', 'Rate', ['class' => 'control-label']) }}
            {{ Form::text('rate', null, ['class' => 'form-control']) }}
            {!! $errors->first('rate', '<div class="help-block">:message</div>') !!}
        </div>
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('unit', 'Unit Of Measure', ['class' => 'control-label']) }}
            {{ Form::select('unit', $units_drop,['class' => 'form-control'], ['class' => 'form-control']) }}
            {!! $errors->first('unit', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('waste', 'waste', ['class' => 'control-label']) }}
            {{ Form::text('waste', null, ['class' => 'form-control']) }}
            {!! $errors->first('waste', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('business_partner', 'business partner', ['class' => 'control-label']) }}

            {{  Form::select('business_partner_id',$partners,1, ['class' => 'form-control']) }}
            {!! $errors->first('business_partner_id', '<div class="help-block">:message</div>') !!}
        </div>




        <!-- Continue working on your fields here -->

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>

    </div>
</div>
