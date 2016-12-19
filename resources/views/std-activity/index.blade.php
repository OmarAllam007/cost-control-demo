@extends('layouts.app')

@section('header')
    <h2>Standard Activity</h2>
    <div class="pull-right">
        @can('write', 'std-activities')
        <a href="{{ route('std-activity.create') }} " class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add
            Activity</a>

        <div class="btn dropdown" style="padding: 0px">
            <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="true">
                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                Importing
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <li>
                    <a href="{{ route('std-activity.import') }} " class="btn"><i class="fa fa-cloud-upload"></i>
                        Import</a>
                </li>
                <li>
                    <a href="{{route('all-stdActivites.modify')}}" class="btn">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                        Modify
                    </a>
                </li>
            </ul>
        </div>
        @endcan


        <a href="{{route('std-activity.exportAll')}}" class="btn btn-info btn-sm">
            <i class="fa fa-cloud-download"></i> Export
        </a>
        @can('wipe')
            <a href="#WipeAlert" data-toggle="modal" class="btn btn-sm btn-danger">
                <i class="fa fa-trash"></i> Delete All
            </a>
        @endcan
    </div>
@stop

@section('body')
    @if(count(request('dublicate')))
        <div class="container" id="notify" style="">
            @foreach(request('dublicate') as $item)
                <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>Item With Code
                        ( {{$item}} ) Exist.</strong>
                </div>
            @endforeach

        </div>
    @endif
    @include('std-activity._filters')

    @if ($stdActivities->total())
        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-4">Name</th>
                <th class="col-xs-4">Division</th>
                <th class="col-xs-4">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($stdActivities->sortBy('name') as $std_activity)
                <tr>
                    <td class="col-xs-4">
                        <a href="{{ route('std-activity.edit', $std_activity) }}">{{ $std_activity->name }}</a>
                    </td>

                    <td class="col-xs-4">{{ $std_activity->division->path }}</td>

                    <td class="col-xs-4">
                        <form action="{{ route('std-activity.destroy', $std_activity) }}" method="post">
                            @can('read', 'std-activities')
                                <a class="btn btn-sm btn-info" href="{{ route('std-activity.show', $std_activity) }}">
                                    <i class="fa fa-eye"></i> View
                                </a>
                            @endcan

                            @can('write', 'std-activities')
                                <a class="btn btn-sm btn-primary" href="{{ route('std-activity.edit', $std_activity) }}">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            @endcan

                            @can('delete', 'std-activities')
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
        {{ $stdActivities->links() }}
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="WipeAlert">
            <form class="modal-dialog" action="{{route('std-activity.wipe')}}" method="post">
                {{csrf_field()}}
                {{method_field('delete')}}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Delete All Standard Activities</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Are you sure you want to delete all activities?
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
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No std activity found</strong>
        </div>
    @endif
@stop

@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
    <script>
        (function (w, d, $) {
            $(function () {
                $(function () {
                    setTimeout(function () {
                        $("#notify").hide('slow')
                    }, 10000);
                });

                var divisionModal = $('#ParentsModal');
                var selectDivision = $('#selectDivision');

                var resetDivision = $('#resetDivision');

                var divisions = divisionModal.find('.tree-radio');

                if (divisions.is(':checked')) {
                    selectDivision.after(resetDivision);
                }

                divisions.on('change', function () {
                    if (divisions.is(':checked')) {
                        resetDivision.show();
                    } else {
                        resetDivision.hide();
                    }
                }).change();

                resetDivision.on('click', function (e) {
                    e.preventDefault();
                    divisions.attr('checked', false);
                    resetDivision.hide();
                    selectDivision.text('Select Division');
                });
            });
        }(window, document, jQuery));
    </script>
@endsection
