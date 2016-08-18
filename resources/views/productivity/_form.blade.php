{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">


        <div class="form-group {{$errors->first('category', 'has-error')}}">
            {{ Form::label('csi_category_id', 'csi-category', ['class' => 'control-label']) }}
            {{ Form::select('csi_category_id', $csi_category,['class' => 'form-control'], ['class' => 'form-control']) }}
            {!! $errors->first('category', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('csi_code', 'has-error')}}">
            {{ Form::label('csi_code', 'code', ['class' => 'control-label']) }}
            {{ Form::text('csi_code', null, ['class' => 'form-control']) }}
            {!! $errors->first('csi_code', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('description', 'has-error')}}">
            {{ Form::label('description', 'Description', ['class' => 'control-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control']) }}
            {!! $errors->first('description', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('unit', 'has-error')}}">
            {{ Form::label('unit', 'unit', ['class' => 'control-label']) }}
            {{ Form::select('unit', $units_drop, ['class' => 'form-control'],['class' => 'form-control']) }}
            {!! $errors->first('unit', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('crew_structure', 'has-error')}}">
            {{ Form::label('crew_structure', 'crew structure', ['class' => 'control-label']) }}
            {{ Form::textarea('crew_structure',null, ['class' => 'form-control']) }}
            {!! $errors->first('crew_structure', '<div class="help-block">:message</div>') !!}
        </div>

        {{----}}
        <div class="form-group">

            <div class="form-group {{$errors->first('crew_hours', 'has-error')}}">
                {{ Form::label('crew_hours', 'crew hours', ['class' => 'control-label']) }}
                {{ Form::text('crew_hours', null, ['class' => 'form-control']) }}
            </div>

            <div class="form-group {{$errors->first('crew_equip', 'has-error')}}">

                {{ Form::label('crew_equip', 'crew equip', ['class' => 'control-label']) }}
                {{ Form::text('crew_equip', null, ['class' => 'form-control']) }}
            </div>

            <div class="form-group {{$errors->first('daily_output', 'has-error')}}">

                {{ Form::label('daily_output', 'daily output', ['class' => 'control-label']) }}
                {{ Form::text('daily_output', null, ['class' => 'form-control']) }}
            </div>
            <div class="form-group {{$errors->first('daily_output', 'has-error')}}">

                {{ Form::label('man_hours', 'man hours', ['class' => 'control-label']) }}
                {{ Form::text('man_hours', null, ['class' => 'form-control']) }}
            </div>
            <div class="form-group {{$errors->first('equip_hours', 'has-error')}}">

                {{ Form::label('equip_hours', 'equip hours', ['class' => 'control-label']) }}
                {{ Form::text('equip_hours', null, ['class' => 'form-control']) }}
            </div>

            <div class="form-group {{$errors->first('reduction_factor', 'has-error')}}">

                {{ Form::label('reduction_factor', 'reduction factor', ['class' => 'control-label']) }}
                {{ Form::text('reduction_factor', null, ['class' => 'form-control']) }}
            </div>
            <div class="form-group {{$errors->first('after_reduction', 'has-error')}}">

                {{ Form::label('after_reduction', 'after reduction', ['class' => 'control-label']) }}
                {{ Form::text('after_reduction', null, ['class' => 'form-control']) }}
            </div>
            <div class="form-group {{$errors->first('source', 'has-error')}}">

                {{ Form::label('source', 'source', ['class' => 'control-label']) }}
                {{ Form::text('source', null, ['class' => 'form-control']) }}

                {!! $errors->first('name', '<div class="help-block">:message</div>') !!}

            </div>

            <div class="form-group">
                <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
            </div>
        </div>
    </div>
