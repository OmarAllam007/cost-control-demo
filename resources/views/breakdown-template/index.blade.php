@extends('layouts.app')

@section('header')
    <h2>Breakdown templates</h2>
    @can('write', 'breakdown-template')
        <div class="pull-right">
            {{csrf_field()}}
            {{method_field('delete')}}
            <a href="{{ route('breakdown-template.create') }} " class="btn btn-sm btn-primary">
                <i class="fa fa-plus"></i> Add template
            </a>

            <a href="{{route('breakdown-template.import')}}" class="btn btn-sm btn-success">
                <i class="fa fa-cloud-upload"></i> Import
            </a>
            @can('wipe')
                <a href="#DeleteAlert" data-toggle="modal" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete
                    All</a>
            @endcan
        </div>
    @endcan
@stop

@section('body')
    <div id="BreakdownResourceForm">
        @include('breakdown-template._filter')
    </div>
    @if ($breakdownTemplates->total())
        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-8">Name</th>
                <th class="col-xs-4">Actions</th>
            </tr>
            </thead>
            <tbody>

            @foreach($breakdownTemplates->sortBy('name') as $breakdown_template)
                <tr>
                    <td class="col-xs-8"><a
                                href="{{ route('breakdown-template.show', $breakdown_template) }}">{{ $breakdown_template->name }}</a>
                    </td>
                    <td class="col-xs-4">
                        <form action="{{ route('breakdown-template.destroy', $breakdown_template) }}" method="post">
                            <a href="{{route('breakdown-template.show', $breakdown_template)}}" class="btn btn-info btn-sm">
                                <i class="fa fa-eye"></i> Show
                            </a>

                            @can('write', 'breakdown-template')
                                <a class="btn btn-sm btn-primary" href="{{route('breakdown-template.edit', $breakdown_template)}}">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            @endcan

                            @can('delete', 'breakdown-template')
                                {{csrf_field()}} {{method_field('delete')}}
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="text-center">
            {{ $breakdownTemplates->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <i class="fa fa-exclamation-circle"></i> <strong>No breakdown template found</strong>
        </div>
    @endif

    @can('wipe')
        <div class="modal fade" tabindex="-1" role="dialog" id="DeleteAlert">
            <form class="modal-dialog" action="{{route('breakdown-template.deleteAll')}}" method="post">
                {{csrf_field()}}
                {{method_field('delete')}}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Delete All Breakdown Templates</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Are you sure you want to delete all Breakdown Templates ?
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
    @endcan
@stop

@section('javascript')
    <script type="text/javascript">
        var productivity = {};
    </script>
    <script src="{{asset('/js/breakdown-resource.js')}}"></script>
@endsection