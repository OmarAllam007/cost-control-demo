@extends('layouts.app')

@section('header')
    <h2>Survey</h2>
    <a href="{{ route('survey.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add survey</a>
@stop

@section('body')
    @if ($surveys->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($surveys as $survey)
                    <tr>
                        <td class="col-md-5"><a href="{{ route('survey.edit', $survey) }}">{{ $survey->cost_name
                        }}</a></td>
                        <td class="col-md-3">
                            <form action="{{ route('survey.destroy', $survey) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-primary" href="{{ route('survey.edit', $survey) }} "><i class="fa fa-edit"></i> Edit</a>
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
