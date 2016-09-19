{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('resource_type_id', 'has-error')}}">
            {{ Form::label('resource_type', 'Resource type', ['class' => 'control-label']) }}
            {{--{{  Form::select('resource_type_id',$resource_types,null, ['class' => 'form-control']) }}--}}
            <div class="form-group {{$errors->first('wbs_id', 'has-error')}}">
                <div class="hidden">
                    {{ Form::select('resource_type_id', App\ResourceType::options(), null, ['class' => 'form-control']) }}
                </div>
                <p>
                    <a href="#LevelsModal" data-toggle="modal" id="select-parent">
                        {{Form::getValueAttribute('resource_type_id')? App\ResourceType::with('parent')->find(Form::getValueAttribute('resource_type_id'))->path : 'Select Resource Type' }}
                    </a>
                </p>
                {!! $errors->first('resource_type_id', '<div class="help-block">:message</div>') !!}
            </div>

        </div>

        <div class="form-group {{$errors->first('resource_code', 'has-error')}}">

            {{ Form::label('resource_code', 'Resource Code', ['class' => 'control-label']) }}
            @if($edit)
                {{ Form::text('resource_code',null, ['class' => 'form-control','disabled'=>'disabled']) }}
            @else
                {{ Form::text('resource_code',null, ['class' => 'form-control']) }}
            @endif
            {!! $errors->first('resource_code', '<div class="help-block">:message</div>') !!}

        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('rate', 'has-error')}}">
            {{ Form::label('rate', 'Rate', ['class' => 'control-label']) }}
            {{ Form::number('rate', null, ['class' => 'form-control','step'=>'any']) }}
            {!! $errors->first('rate', '<div class="help-block">:message</div>') !!}
        </div>
        <div class="form-group {{$errors->first('unit', 'has-error')}}">
            {{ Form::label('unit', 'Unit Of Measure', ['class' => 'control-label']) }}
            {{ Form::select('unit', App\Unit::options(),null, ['class' => 'form-control']) }}
            {!! $errors->first('unit', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('waste', 'has-error')}}">
            {{ Form::label('waste', 'Waste', ['class' => 'control-label']) }}
            <div class="input-group">
                {{ Form::text('waste', null, ['class' => 'form-control']) }}
                <span class="input-group-addon">%</span>
            </div>
            {!! $errors->first('waste', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('reference', 'has-error')}}">
            {{ Form::label('reference', 'Reference', ['class' => 'control-label']) }}
            {{ Form::text('reference', null, ['class' => 'form-control']) }}
            {!! $errors->first('reference', '<div class="help-block">:message</div>') !!}
        </div>


        <div class="form-group {{$errors->first('business_partner_id', 'has-error')}}">
            {{ Form::label('business_partner_id', 'Business Partner', ['class' => 'control-label']) }}
            <p>

                <a href="#ParentsModal2" data-toggle="modal" id="select-parent">
                    {{Form::getValueAttribute('business_partner_id')? App\BusinessPartner::find(Form::getValueAttribute('business_partner_id'))->path : 'Select Business Partner' }}
                </a>
            </p>
            {!! $errors->first('business_partner_id', '<div class="help-block">:message</div>') !!}
        </div>

        <!-- Continue working on your fields here -->

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
                <h4 class="modal-title">Select Parent</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\ResourceType::tree()->get() as $level)
                        @include('resources._recursive_input', compact('level'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>


<div id="ParentsModal2" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Business Partner</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\BusinessPartner::select('id','name')->groupBy('name')
            ->get() as $partner)
                        @include('business-partner._recursive_input', compact('partner'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>


@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop