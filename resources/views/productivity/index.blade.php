@extends('layouts.app')

@section('header')
    <h2>Productivity</h2>
    <a href="{{ route('productivity.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add productivity</a>
@stop

@section('body')
    @if ($productivities->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($productivities as $productivity)
                    <tr>
                        <td class="col-md-5"><a href="{{ route('productivity.edit', $productivity) }}">{{ $productivity->name }}</a></td>
                        <td class="col-md-3">
                            <form action="{{ route('productivity.destroy', $productivity) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-primary" href="{{ route('productivity.edit', $productivity) }} "><i class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $productivities->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No productivity found</strong></div>
    @endif
@stop
