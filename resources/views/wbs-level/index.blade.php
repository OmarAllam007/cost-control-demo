@extends('layouts.app')

@section('header')
    <h2>WBS</h2>
    <a href="{{ route('wbs-level.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add Level</a>
@stop

@section('body')
    @if ($wbsLevels->total())
        {{--<table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($wbsLevels as $wbs_level)
                    <tr>
                        <td class="col-md-5"><a href="{{ route('wbs-level.edit', $wbs_level) }}">{{ $wbs_level->name }}</a></td>
                        <td class="col-md-3">
                            <form action="{{ route('wbs-level.destroy', $wbs_level) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-primary" href="{{ route('wbs-level.edit', $wbs_level) }} "><i class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table> --}}

        <ul class="list-unstyled tree">
            @foreach($wbsLevels as $wbs_level)
                @include('wbs-level._recursive')
            @endforeach
        </ul>

        {{ $wbsLevels->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No WSB found</strong></div>
    @endif
@stop
