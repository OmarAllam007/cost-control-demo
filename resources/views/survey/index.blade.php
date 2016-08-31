@extends('layouts.app')

@section('header')
    <h2>Quantity Survey</h2>
    <a href="{{ route('survey.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add
        survey</a>
@stop

@section('body')
    @if ($surveys->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Budget Quantitiy</th>
                <th>Eng Quantitiy</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($surveys as $survey)
                    <tr>
                        <td class="col-md-2">{{ isset($survey->wbsLevel->name)?$survey->wbsLevel->name:'' }}</td>
                        <td class="col-md-2">{{ $survey->description }}</td>
                        <td class="col-md-1">{{isset($survey->unit->type)?$survey->unit->type:0}}</td>
                        <td class="col-md-1">{{$survey->budget_qty }}</td>
                        <td class="col-md-1">{{ $survey->eng_qty }}</td>
                        <td class="col-md-2">{{$survey->categories->name}}</td>
                        <td class="col-md-3">
                            <form action="{{ route('survey.destroy', $survey) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-primary" href="{{ route('survey.edit', $survey) }} "><i
                                            class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>


            @endforeach
            </tbody>
        </table>

        {{ $surveys->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No survey found</strong></div>
    @endif
@stop
