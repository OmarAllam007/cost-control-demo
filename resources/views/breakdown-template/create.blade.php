@extends('layouts.' . (request('iframe')? 'iframe' : 'app'))

@section('header')
    <h2>Add template</h2>

    <div class="pull-right btn-toolbar">
        <a href="{{ route('breakdown-template.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Templates List</a>

        @if (request('activity'))
            <a href="{{ route('std-activity.show', request('activity')) }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back to activity</a>
        @endif
    </div>


@stop

@section('body')
    {{ Form::open(['route' => ['breakdown-template.store', 'activity' => request('activity'),'import'=>request('import')],'id'=>'create_template']) }}
    @include('breakdown-template._form')
    {{ Form::close() }}


@stop
<script>

</script>