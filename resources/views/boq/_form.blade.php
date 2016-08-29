{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">

        <div class="form-group {{$errors->first('wbs_id', 'has-error')}}">
            {{ Form::label('wbs_id', 'Parent', ['class' => 'control-label']) }}
            <div class="hidden">
                {{ Form::select('wbs_id', App\WbsLevel::options(), null, ['class' => 'form-control']) }}
            </div>
            <p>
                <a href="#LevelsModal" data-toggle="modal" id="select-parent">
                    {{Form::getValueAttribute('wbs_id')? App\WbsLevel::with('parent')->find(Form::getValueAttribute('wbs_id'))->path : 'Select Parent' }}
                </a>
            </p>
            {!! $errors->first('wbs_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('item', 'has-error')}}">
            {{ Form::label('item', 'BOQ Item', ['class' => 'control-label']) }}
            {{ Form::text('item', null, ['class' => 'form-control']) }}
            {!! $errors->first('item', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('description', 'has-error')}}">
            {{ Form::label('description', 'Description', ['class' => 'control-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control']) }}
            {!! $errors->first('description', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('type', 'has-error')}}">
            {{ Form::label('type', 'Type', ['class' => 'control-label']) }}
            {{ Form::text('type', null, ['class' => 'form-control']) }}
            {!! $errors->first('type', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('unit_id', 'has-error')}}">
            {{Form::label('units','Unit of measure')}}
            {{Form::select('unit_id',App\Unit::options(),['class'=>'form-control'],['class'=>'form-control'])}}
        </div>

        <div class="form-group {{$errors->first('quantity', 'has-error')}}">
            {{ Form::label('quantity', 'Quantity', ['class' => 'control-label']) }}
            {{ Form::text('quantity', null,['class' => 'form-control'] ,['class' => 'form-control']) }}
            {!! $errors->first('quantity', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('dry_ur', 'has-error')}}">
            {{ Form::label('dry_ur', 'DRY U.R.', ['class' => 'control-label']) }}
            {{ Form::text('dry_ur', null,['class' => 'form-control'] ,['class' => 'form-control']) }}
            {!! $errors->first('dry_ur', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('price_ur', 'has-error')}}">
            {{ Form::label('price_ur', 'PRICE U.R.', ['class' => 'control-label']) }}
            {{ Form::text('price_ur', null,['class' => 'form-control'] ,['class' => 'form-control']) }}
            {!! $errors->first('price_ur', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('arabic_description', 'has-error')}}">
            {{ Form::label('arabic_description', 'Arabic Description', ['class' => 'control-label']) }}
            {{ Form::textarea('arabic_description', null, ['class' => 'form-control']) }}
            {!! $errors->first('arabic_description', '<div class="help-block">:message</div>') !!}
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
                    @foreach(App\WbsLevel::tree()->get() as $level)
                        @include('boq._recursive_input', compact('level'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop
