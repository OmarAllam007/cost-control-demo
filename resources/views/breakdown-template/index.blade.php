@extends('layouts.app')

@section('header')
    <h2>Breakdown templates</h2>
    <a href="{{ route('breakdown-template.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add template</a>
@stop

@section('body')
    @include('breakdown-template._filter')

    @if ($breakdownTemplates->total())
        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-8">Name</th>
                <th class="col-xs-4">Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($breakdownTemplates as $breakdown_template)
                    <tr>
                        <td class="col-xs-8"><a href="{{ route('breakdown-template.show', $breakdown_template) }}">{{ $breakdown_template->name }}</a></td>
                        <td class="col-xs-4">
                            <form action="{{ route('breakdown-template.destroy', $breakdown_template) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a href="{{route('breakdown-template.show', $breakdown_template)}}" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Show</a>
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
