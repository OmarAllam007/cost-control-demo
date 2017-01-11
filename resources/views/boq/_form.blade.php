{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">

        <div class="form-group {{$errors->first('project_id', 'has-error')}}">
            {{ Form::label('project_id', 'Project', ['class' => 'control-label']) }}
            @if ($project_id = request('project'))
                <p><em>{{App\Project::find($project_id)->name}}</em></p>
                {{Form::hidden('project_id', $project_id)}}
            @elseif (isset($boq) && ($project_id = $boq->project_id))
                <p><em>{{$boq->project->name}}</em></p>
                {{Form::hidden('project_id', $project_id)}}
            @else
                {{ Form::select('project_id', App\Project::options(), $project_id = Form::getValueAttribute('project_id'), ['class' => 'form-control']) }}
                {{Form::hidden('project_id', $project_id)}}
            @endif
            {!! $errors->first('project_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('wbs_id', 'has-error')}}">
            {{ Form::label('wbs_id', 'Wbs Level', ['class' => 'control-label']) }}
            <div class="hidden">
                {{ Form::select('wbs_id', App\WbsLevel::options(), null, ['class' => 'form-control']) }}
            </div>
            <p>
                <a href="#WBSModal" data-toggle="modal" id="select-parent" class="tree-open">
                    @if($wbs_id = request('wbs_id'))
                        {{App\WbsLevel::with('parent')->find($wbs_id)->path}}
                        {{Form::hidden('wbs_id', $wbs_id)}}
                    @else
                        {{Form::getValueAttribute('wbs_id')? App\WbsLevel::with('parent')->find(Form::getValueAttribute('wbs_id'))->path : 'Select Wbs Level' }}
                    @endif
                </a>
                <a class="remove-tree-input text-danger" data-label="Select Wbs Level" data-target="#LevelsModal"><span
                            class="fa fa-times"></span></a>
            </p>
            {!! $errors->first('wbs_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('item_code', 'has-error')}}">
            {{ Form::label('item_code', 'Item Code', ['class' => 'control-label']) }}
            {{ Form::text('item_code', null, ['class' => 'form-control']) }}
            {!! $errors->first('item_code', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('cost_account', 'has-error')}}">
            {{ Form::label('cost_account', 'Cost Account', ['class' => 'control-label']) }}
            {{ Form::text('cost_account', null, ['class' => 'form-control']) }}
            {!! $errors->first('cost_account', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('type', 'has-error')}}">
            {{ Form::label('type', 'Discipline', ['class' => 'control-label']) }}
            {{ Form::select('type', App\Boq::where('project_id',$project_id)->lists('type','type')->unique(), null, ['class' => 'form-control']) }}
            {!! $errors->first('type', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('division_id', 'has-error')}}">
            {{ Form::label('division_id', 'Division Level', ['class' => 'control-label']) }}
            <div class="hidden">
                {{ Form::select('division_id', App\BoqDivision::options(), null, ['class' => 'form-control']) }}
            </div>
            <p>
                <a href="#LevelsModal2" data-toggle="modal" id="select-parent">
                    {{Form::getValueAttribute('division_id')? App\BoqDivision::with('parent')->find(Form::getValueAttribute('division_id'))->path : 'Select BOQ Division' }}
                </a>
            </p>
            {!! $errors->first('division_id', '<div class="help-block">:message</div>') !!}
        </div>
        {{--<div class="form-group {{$errors->first('item', 'has-error')}}">--}}
        {{--{{ Form::label('item', 'BOQ Item', ['class' => 'control-label']) }}--}}
        {{--{{ Form::text('item', null, ['class' => 'form-control']) }}--}}
        {{--{!! $errors->first('item', '<div class="help-block">:message</div>') !!}--}}
        {{--</div>--}}


        <div class="form-group {{$errors->first('description', 'has-error')}}">
            {{ Form::label('description', 'Item Description', ['class' => 'control-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control']) }}
            {!! $errors->first('description', '<div class="help-block">:message</div>') !!}
        </div>


        <div class="form-group {{$errors->first('unit_id', 'has-error')}}">
            {{Form::label('unit_id','Unit', ['class' => 'control-label'])}}
            {{Form::select('unit_id',App\Unit::options(),null,['class'=>'form-control'])}}
            {!! $errors->first('unit_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('quantity', 'has-error')}}">
            {{ Form::label('quantity', 'Estimated Quantity', ['class' => 'control-label']) }}
            {{ Form::text('quantity', null,['class' => 'form-control'] ,['class' => 'form-control']) }}
            {!! $errors->first('quantity', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('price_ur', 'has-error')}}">
            {{ Form::label('price_ur', 'Unit Pirce', ['class' => 'control-label']) }}
            {{ Form::text('price_ur', null,['class' => 'form-control'] ,['class' => 'form-control']) }}
            {!! $errors->first('price_ur', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('dry_ur', 'has-error')}}">
            {{ Form::label('dry_ur', 'Unit Dry', ['class' => 'control-label']) }}
            {{ Form::text('dry_ur', null,['class' => 'form-control'] ,['class' => 'form-control']) }}
            {!! $errors->first('dry_ur', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('kcc_qty', 'has-error')}}">
            {{ Form::label('kcc_qty', 'KCC-Quantity', ['class' => 'control-label']) }}
            {{ Form::text('kcc_qty', null,['class' => 'form-control'] ,['class' => 'form-control']) }}
            {!! $errors->first('kcc_qty', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('materials', 'has-error')}}">
            {{ Form::label('materials', 'Materials', ['class' => 'control-label']) }}
            {{ Form::text('materials', null,['class' => 'form-control'] ,['class' => 'form-control']) }}
            {!! $errors->first('materials', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('subcon', 'has-error')}}">
            {{ Form::label('subcon', 'Sub-Con.', ['class' => 'control-label']) }}
            {{ Form::text('subcon', null,['class' => 'form-control'] ,['class' => 'form-control']) }}
            {!! $errors->first('subcon', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('manpower', 'has-error')}}">
            {{ Form::label('manpower', 'ManPower', ['class' => 'control-label']) }}
            {{ Form::text('manpower', null,['class' => 'form-control'] ,['class' => 'form-control']) }}
            {!! $errors->first('manpower', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </div>
</div>

@include('wbs-level._modal', ['project_id' => $project_id, 'value' => Form::getValueAttribute('wbs_id'), 'input' => 'wbs_id'])

<div id="LevelsModal2" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Division</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\BoqDivision::tree()->get() as $division)
                        @include('boq-division._recursive_input', compact('division'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>


@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@stop
