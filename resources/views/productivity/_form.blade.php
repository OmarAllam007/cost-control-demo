{{ csrf_field() }}

<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('csi_code', 'has-error')}}">
            {{ Form::label('csi_code', 'Code', ['class' => 'control-label']) }}
            @if($edit)
                {{ Form::text('csi_code', null, ['class' => 'form-control','readonly' => 'readonly']) }}
                {!! $errors->first('csi_code', '<div class="help-block">:message</div>') !!}
            @elseif($override)
                {{ Form::text('csi_code', $baseProductivity->csi_code, ['class' => 'form-control','readonly' => 'readonly']) }}
            @else
                {{ Form::text('csi_code', null, ['class' => 'form-control']) }}
            @endif
        </div>

        <div class="form-group {{$errors->first('csi_category_id', 'has-error')}}">
            {{ Form::label('csi_category_id', 'CSI Category', ['class' => 'control-label']) }}
            @if ($override)
                <p>
                    {{Form::hidden('csi_category_id', $baseProductivity->csi_category_id)}}
                    <em>{{ $baseProductivity->category->path }}</em>
                </p>
            @else
                <p>
                    <a href="#LevelsModal" data-toggle="modal" class="tree-open" id="select-parent">
                        {{Form::getValueAttribute('csi_category_id')? App\CsiCategory::with('parent')->find(Form::getValueAttribute('csi_category_id'))->path : 'Select Category' }}
                    </a>
                    <a class="remove-tree-input" data-target="#LevelsModal" data-label="Select Category"><span
                                class="fa fa-times"></span></a>
                </p>
                {!! $errors->first('csi_category_id', '<div class="help-block">:message</div>') !!}
            @endif
        </div>

        <div class="form-group {{$errors->first('daily_output', 'has-error')}}">
            {{ Form::label('daily_output', 'Daily Output', ['class' => 'control-label']) }}
            @if($override)
                {{ Form::text('daily_output', $baseProductivity->daily_output, ['class' => 'form-control', 'readonly']) }}
            @else
                {{ Form::text('daily_output', null, ['class' => 'form-control']) }}
                {!! $errors->first('daily_output', '<div class="help-block">:message</div>') !!}
            @endif
        </div>

        <div class="form-group {{$errors->first('reduction_factor', 'has-error')}}">
            {{ Form::label('reduction_factor', 'Reduction Factor', ['class' => 'control-label']) }}
            {{ Form::text('reduction_factor', null, ['class' => 'form-control']) }}
            {!! $errors->first('reduction_factor', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('description', 'has-error')}}">
            {{ Form::label('description', 'Description', ['class' => 'control-label', 'readonly' => 'readonly']) }}
            @if ($override)
                {{ Form::textarea('description', $baseProductivity->description, ['class' => 'form-control', 'readonly']) }}
                {!! $errors->first('description', '<div class="help-block">:message</div>') !!}
            @else
                {{ Form::textarea('description', null, ['class' => 'form-control', ]) }}
            @endif
        </div>

        <div class="form-group {{$errors->first('crew_structure', 'has-error')}}">
            {{ Form::label('crew_structure', 'Crew Structure', ['class' => 'control-label']) }}
            @if ($override)
                {{ Form::textarea('crew_structure', $baseProductivity->crew_structure, ['class' => 'form-control','id'=> 'crew_structure', 'readonly' => 'readonly']) }}
            @else
                {{ Form::textarea('crew_structure',null, ['class' => 'form-control','id'=> 'crew_structure']) }}
                {!! $errors->first('crew_structure', '<div class="help-block">:message</div>') !!}
            @endif
        </div>




        <div class="form-group {{$errors->first('unit', 'has-error')}}">
            {{ Form::label('unit', 'Unit', ['class' => 'control-label']) }}
            @if ($override)
                {{ Form::select('unit', App\Unit::options(), $baseProductivity->unit, ['class' => 'form-control', 'readonly']) }}
            @else
                {{ Form::select('unit', App\Unit::options(), null, ['class' => 'form-control']) }}
                {!! $errors->first('unit', '<div class="help-block">:message</div>') !!}
            @endif
        </div>

        <div class="form-group {{$errors->first('source', 'has-error')}}">
            {{ Form::label('source', '', ['class' => 'control-label']) }}
            @if ($override)
                {{ Form::text('source', null, ['class' => 'form-control', 'readonly']) }}
            @else
                {{ Form::text('source', null, ['class' => 'form-control']) }}
                {!! $errors->first('source', '<div class="help-block">:message</div>') !!}
            @endif
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
                    @foreach(App\CsiCategory::tree()->get()->sortBy('name') as $level)
                        @include('productivity._recursive_input', ['csi_category_id'=>$level])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop