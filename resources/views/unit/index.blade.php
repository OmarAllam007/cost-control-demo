@extends('layouts.app')

@section('header')
    <h2>Unit</h2>
    <div class="btn-toolbar pull-right">
        @can('write', 'resources')
            <a href="{{ route('unit.create') }} " class="btn btn-sm btn-primary pull-right">
                <i class="fa fa-plus"></i> Add unit
            </a>
        @endcan
        @can('wipe')
            <a href="#WipeAlert" data-toggle="modal" class="btn btn-sm btn-danger">
                <i class="fa fa-trash"></i> Delete All
            </a>
        @endcan
    </div>
@stop

@section('body')
    @include('unit._filters')
    @if ($units->total())
        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-8">Name</th>
                <th class="col-xs-4">@can('write', 'resources') Actions @endcan</th>
            </tr>
            </thead>
            <tbody>
            @foreach($units->sortBy('type') as $unit)
                <tr>
                    <td class="col-xs-8"><a href="{{ route('unit.edit', $unit) }}">{{ $unit->type }}</a></td>
                    <td class="col-xs-4">
                        @can('write', 'resources')
                            <form action="{{ route('unit.destroy', $unit) }}" method="post">
                                <a class="btn btn-sm btn-primary" href="{{ route('unit.edit', $unit) }} ">
                                    <i class="fa fa-edit"></i> Edit
                                </a>

                                @can('delete', 'resources')
                                    {{csrf_field()}} {{method_field('delete')}}
                                    <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                                @endcan
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $units->links() }}

        <div class="modal fade" tabindex="-1" role="dialog" id="WipeAlert">
            <form class="modal-dialog" action="{{route('unit.wipe')}}" method="post">
                {{csrf_field()}}
                {{method_field('delete')}}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Delete All Standard Units</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Are you sure you want to delete all Units ?
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
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No unit found</strong></div>
    @endif
@stop
