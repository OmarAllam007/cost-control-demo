@extends('layouts.app')

@section('header')
    <h2>Breakdown template</h2>
    <a href="{{ route('breakdown-template.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add breakdown_template</a>
@stop

@section('body')
    @if ($breakdownTemplates->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($breakdownTemplates as $breakdown_template)
                    <tr>
                        <td class="col-md-5"><a href="{{ route('breakdown-template.edit', $breakdown_template) }}">{{ $breakdown_template->name }}</a></td>
                        <td class="col-md-3">
                            <form action="{{ route('breakdown-template.destroy', $breakdown_template) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-primary" href="{{ route('breakdown-template.edit', $breakdown_template) }} "><i class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $breakdownTemplates->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No breakdown template found</strong></div>
    @endif
@stop
