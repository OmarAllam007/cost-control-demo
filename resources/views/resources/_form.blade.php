{{ csrf_field() }}
<div class="row">
    <div class="col-sm-8">
        <article class="form-group {{$errors->first('resource_type_id', 'has-error')}}">
            {{ Form::label('resource_type', 'Resource type', ['class' => 'control-label']) }}
            {{ Form::hidden('resource_type_id') }}
            @php
                $type_id = Form::getValueAttribute('resource_type_id');
                $type = App\ResourceType::with('parent')->find($type_id);
            @endphp

            @if ($resources->project_id)
                <input class="form-control" disabled value="{{$type->path ?? 'Type not found'}}">
            @else
                <div class="btn-group btn-group-sm btn-group-block">
                    <a href="#LevelsModal" data-toggle="modal" class="tree-open btn btn-default btn-sm flex text-right">{{$type->path ?? 'Select Type'}}</a>
                    <a class="remove-tree-input btn btn-warning btn-sm" data-label="Select Type" data-target="#LevelsModal"><span class="fa fa-times-circle"></span></a>
                </div>
                <div class="clearfix"></div>
            @endif
            {!! $errors->first('resource_type_id', '<div class="help-block">:message</div>') !!}
        </article>

        <article class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
            @if(!empty($override))
                {{ Form::text('name', $resources->name, ['class' => 'form-control', 'readonly' => $resources->project_id]) }}
            @else
                {{ Form::text('name', null, ['class' => 'form-control']) }}
                {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
            @endif
        </article>

        <article class="form-group {{$errors->first('unit', 'has-error')}}">
            {{ Form::label('unit', 'Unit Of Measure', ['class' => 'control-label']) }}
            {{ Form::select('unit', App\Unit::options(),null, ['class' => 'form-control', 'disabled' => $resources->project_id]) }}
            {!! $errors->first('unit', '<div class="help-block">:message</div>') !!}
        </article>

        <article class="form-group {{$errors->first('rate', 'has-error')}}">
            {{ Form::label('rate', 'Rate', ['class' => 'control-label']) }}
            {{ Form::number('rate', null, ['class' => 'form-control','step'=>'any']) }}
            {!! $errors->first('rate', '<div class="help-block">:message</div>') !!}
        </article>

        <article class="form-group {{$errors->first('waste', 'has-error')}}">
            {{ Form::label('waste', 'Waste', ['class' => 'control-label']) }}
            <div class="input-group">
                {{ Form::text('waste', null, ['class' => 'form-control']) }}
                <span class="input-group-addon">%</span>
            </div>
            {!! $errors->first('waste', '<div class="help-block">:message</div>') !!}
        </article>

        <article class="form-group {{$errors->first('reference', 'has-error')}}">
            {{ Form::label('reference', 'Reference', ['class' => 'control-label']) }}
            {{ Form::text('reference', null, ['class' => 'form-control']) }}
            {!! $errors->first('reference', '<div class="help-block">:message</div>') !!}
        </article>

        <article class="form-group {{$errors->first('business_partner_id', 'has-error')}}">
            {{ Form::label('business_partner_id', 'Business Partner', ['class' => 'control-label']) }}
            {{ Form::select('business_partner_id', App\BusinessPartner::options(), null, ['class' => 'form-control']) }}
            {!! $errors->first('business_partner_id', '<div class="help-block">:message</div>') !!}
        </article>

        @if ($resources->project_id)
        <article class="form-group">
            <label for="top-material" class="control-label">Top material</label>
            <input name="top_material" value="{{old('top_material', $resources->top_material) }}" class="form-control" id="top-material">
        </article>
        @endif
    </div>

    @include('resources._codes')

</div>

<article class="form-group">
    <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
</article>

<div id="LevelsModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Parent</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\ResourceType::tree()->get() as $level)
                        @include('resources._recursive_input', ['level' => $level, 'value' => Form::getValueAttribute('resource_type_id')])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@section('javascript')
    @if (isset($resources))
        <script src="{{asset('/js/resource-codes.js')}}"></script>
    @endif
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop