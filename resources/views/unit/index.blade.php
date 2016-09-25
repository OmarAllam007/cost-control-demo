@extends('layouts.app')

@section('header')
    <h2>Unit</h2>
    <a href="{{ route('unit.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add unit</a>
@stop

@section('body')
    @include('unit._filters')
    @if ($units->total())
        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-8">Name</th>
                <th class="col-xs-4">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($units as $unit)
                <tr>
                    <td class="col-xs-8"><a href="{{ route('unit.edit', $unit) }}">{{ $unit->type }}</a></td>
                    <td class="col-xs-4">
                        <form action="{{ route('unit.destroy', $unit) }}" method="post">
                            {{csrf_field()}} {{method_field('delete')}}
                            <a class="btn btn-sm btn-primary" href="{{ route('unit.edit', $unit) }} "><i
                                        class="fa fa-edit"></i> Edit</a>
                            <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $units->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No unit found</strong></div>
    @endif
@stop
