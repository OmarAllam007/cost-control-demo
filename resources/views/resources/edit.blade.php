@extends(request()->exists('iframe')? 'layouts.iframe' : 'home.master-data')

@section('header')
    <h2>Edit Resources</h2>

    <form action="{{ route('resources.destroy', $resource)}}" class="pull-right" method="post">

        @can('delete', 'resources')
        {{csrf_field()}} {{method_field('delete')}}
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        @endcan

        @if ($resource->project_id)
            <a href="{{ route('project.show', $resource->project_id)}}#resources" class="btn btn-sm btn-default">
                <i class="fa fa-chevron-left"></i> Back
            </a>
        @else
            <a href="{{ route('resources.index')}}" class="btn btn-sm btn-default">
                <i class="fa fa-chevron-left"></i> Back
            </a>
        @endif
    </form>
@stop

@section(request()->exists('iframe')? 'body' : 'content')
    {{ Form::model($resource, ['route' => ['resources.update', 'resources' => $resource,'project_id'=>request('project_id'), 'iframe' => request()->exists('iframe') ?: null], 'method' => 'PATCH']) }}

    @include('resources._form', ['override' => $resource->project_id])

    {{ Form::close() }}
@stop
