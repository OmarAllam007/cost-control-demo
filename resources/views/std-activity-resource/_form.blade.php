
<div class="row" id="BreakdownResourceForm">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('template_id', 'has-error')}}">
            {{ Form::label('template_id', 'Template', ['class' => 'control-label']) }}

            @if (request('template'))
                <p><em>{{\App\BreakdownTemplate::find(request('template'))->name}}</em></p>
                {{Form::hidden('template_id', request('template'))}}
            @else
                <p><em>{{$std_activity_resource->template->name}}</em></p>
                {{Form::hidden('template_id', $std_activity_resource->template->id)}}
            @endif

            {{--{{ Form::select('template_id', \App\BreakdownTemplate::options(), request()->template, ['class' => 'form-control']) }}--}}
            {!! $errors->first('template_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('resource_id', 'has-error')}}">
            {{ Form::label('resource_id', 'Resource', ['class' => 'control-label']) }}

            <div class="btn-group btn-group-block">
                <a class="tree-open btn btn-default" href="#ResourcesModal" data-toggle="modal" id="select-resource" v-text="resource.name || 'Select Resource'"></a>
                <a class="remove-tree-input btn btn-warning btn-sm" @click="$broadcast('resetResource')"><span class="fa fa-times"></span></a>
            </div>
            {{Form::hidden('resource_id')}}
            {!! $errors->first('resource_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('equation', 'has-error')}}">
            {{ Form::label('equation', 'Equation', ['class' => 'control-label']) }}
            <p class="text-info">
                <i class="fa fa-info-circle"></i> Please use <code>$v</code> for value in the equation
            </p>
            {{ Form::text('equation', null, ['class' => 'form-control']) }}
            {!! $errors->first('equation', '<div class="help-block">:message</div>') !!}
        </div>

        <section id="productivity-fields" v-show="show_productivity">
            <div class="form-group {{$errors->first('productivity_id', 'has-error')}}">
                {{ Form::label('productivity_id', 'Productivity', ['class' => 'control-label']) }}
                <p>
                    <a href="#ProductivityModal" data-toggle="modal" v-text="productivity.code || 'Select Productivity Reference'"></a>
                </p>
                {{--                {{ Form::select('productivity_id', \App\Productivity::options(), null, ['class' => 'form-control']) }}--}}
                {!! $errors->first('productivity_id', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group {{$errors->first('labor_count', 'has-error')}}">
                {{ Form::label('labor_count', 'Labor Count', ['class' => 'control-label']) }}
                {{ Form::text('labor_count', null, ['class' => 'form-control', 'v-model' => 'labor_count']) }}
                {!! $errors->first('labor_count', '<div class="help-block">:message</div>') !!}
            </div>
        </section>

        <div class="form-group {{$errors->first('remarks', 'has-error')}}">
            {{ Form::label('remarks', 'Remarks', ['class' => 'control-label']) }}
            {{ Form::text('remarks', null, ['class' => 'form-control']) }}
            {!! $errors->first('remarks', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </div>

    @include('std-activity-resource._templates')
</div>

@section('javascript')
    <script type="text/javascript">
        var productivity = {};
        @if (Form::getValueAttribute('productivity_id'))
        {{Form::getValueAttribute('productivity_id')}}
                productivity = {!! json_encode(\App\Productivity::find(Form::getValueAttribute('productivity_id'))->morphToJSON()) !!}
        @endif
    </script>
    <script src="{{asset('/js/breakdown-resource.js')}}"></script>
@endsection