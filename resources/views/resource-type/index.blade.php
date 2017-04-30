@extends('layouts.app')

@section('title', 'Resource Types')

@section('header')
    <h2>Resource Type</h2>
    <div class="btn-toolbar pull-right">
        <a href="{{ route('resource-type.create') }} " class="btn btn-sm btn-primary pull-right"><i
                    class="fa fa-plus"></i> Add Resource Type</a>
        @can('wipe')
            <a href="#WipeAlert" data-toggle="modal" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete
                All</a>
        @endcan
    </div>
@stop

@section('body')
    @if ($resource_levels->total())

        <ul class="list-unstyled tree">
            @foreach($resource_levels as $resource_level)

                @include('resource-type._recursive')
            @endforeach
        </ul>
        {{ $resource_levels->links() }}

        <div class="modal fade" tabindex="-1" role="dialog" id="WipeAlert">
            <form class="modal-dialog" action="{{route('type.wipe')}}" method="post">
                {{csrf_field()}}
                {{method_field('delete')}}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Delete All Resource Types</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Are you sure you want to delete all Resource Types ?
                            <input type="hidden" name="wipe" value="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete All</button>
                        <button type="button" class="btn btn-default"><i class="fa fa-close"></i> Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No resource type found</strong>
        </div>
    @endif
@stop
