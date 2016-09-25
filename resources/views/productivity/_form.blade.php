{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('code', 'has-error')}}">
            {{ Form::label('code', 'Code', ['class' => 'control-label']) }}
            @if($edit)
                {{ Form::text('code', null, ['class' => 'form-control','disabled' => 'disabled']) }}
                {!! $errors->first('code', '<div class="help-block">:message</div>') !!}
            @else
                {{ Form::text('code', null, ['class' => 'form-control']) }}

            @endif
        </div>

        <div class="form-group {{$errors->first('csi_category_id', 'has-error')}}">
            {{ Form::label('csi_category_id', 'CSI Category', ['class' => 'control-label']) }}
            <div class="hidden">
                {{ Form::select('csi_category_id', App\CsiCategory::options(), null, ['class' => 'form-control']) }}
            </div>
            <p>
                <a href="#LevelsModal" data-toggle="modal" id="select-parent">
                    {{Form::getValueAttribute('csi_category_id')? App\CsiCategory::with('parent')->find(Form::getValueAttribute('csi_category_id'))->path : 'Select' }}
                </a>
                <a id="remove-parent"><span class="fa fa-times"></span></a>
            </p>
            {!! $errors->first('csi_category_id', '<div class="help-block">:message</div>') !!}
        </div>


        <div class="form-group {{$errors->first('description', 'has-error')}}">
            {{ Form::label('description', 'Description', ['class' => 'control-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control']) }}
            {!! $errors->first('description', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('crew_structure', 'has-error')}}">
            {{ Form::label('crew_structure', 'Crew Structure', ['class' => 'control-label']) }}
            {{ Form::textarea('crew_structure',null, ['class' => 'form-control']) }}
            {!! $errors->first('crew_structure', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('unit', 'has-error')}}">
            {{ Form::label('unit', 'Unit', ['class' => 'control-label']) }}
            {{ Form::select('unit', App\Unit::options(), null, ['class' => 'form-control']) }}
            {!! $errors->first('unit', '<div class="help-block">:message</div>') !!}
        </div>


        <div class="form-group {{$errors->first('crew_hours', 'has-error')}}">
            {{ Form::label('crew_hours', 'Crew Hours', ['class' => 'control-label']) }}
            {{ Form::text('crew_hours', null, ['class' => 'form-control']) }}
        </div>


        <div class="form-group {{$errors->first('crew_equip', 'has-error')}}">
            {{ Form::label('crew_equip', 'Crew equipment', ['class' => 'control-label']) }}
            {{ Form::text('crew_equip', null, ['class' => 'form-control']) }}
        </div>

        <div class="form-group {{$errors->first('daily_output', 'has-error')}}">

            {{ Form::label('daily_output', 'Daily Output', ['class' => 'control-label']) }}
            {{ Form::text('daily_output', null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group {{$errors->first('man_hours', 'has-error')}}">

            {{ Form::label('man_hours', 'Man Hours', ['class' => 'control-label']) }}
            {{ Form::text('man_hours', null, ['class' => 'form-control']) }}
        </div>

        <div class="form-group {{$errors->first('man_hours', 'has-error')}}">
            {{ Form::label('man_hours', 'Man Hours', ['class' => 'control-label']) }}
            {{ Form::text('man_hours', null, ['class' => 'form-control']) }}
        </div>

        <div class="form-group {{$errors->first('equip_hours', 'has-error')}}">
            {{ Form::label('equip_hours', 'Equipment Hours', ['class' => 'control-label']) }}
            {{ Form::text('equip_hours', null, ['class' => 'form-control']) }}
        </div>

        <div class="form-group {{$errors->first('reduction_factor', 'has-error')}}">
            {{ Form::label('reduction_factor', 'Reduction Factor', ['class' => 'control-label']) }}
            {{ Form::text('reduction_factor', null, ['class' => 'form-control']) }}
        </div>

        <div class="form-group {{$errors->first('source', 'has-error')}}">
            {{ Form::label('source', 'Source', ['class' => 'control-label']) }}
            {{ Form::text('source', null, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </div>
</div>

<div id="LevelsModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select CSI Category</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\CsiCategory::tree()->get() as $level)
                        @include('productivity._recursive_input', $level)
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop