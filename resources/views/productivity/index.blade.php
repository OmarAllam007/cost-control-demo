@extends('layouts.app')

@section('header')
    <h2>Productivity</h2>
    <div class="btn-toolbar pull-right">
        <a href="{{ route('productivity.create') }} " class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Add Productivity
        </a>

        <a href="{{ route('productivity.import') }} " class="btn btn-sm btn-success">
            <i class="fa fa-cloud-upload"></i> Import
        </a>
    </div>

@stop




@section('body')
    <div class="form-group upload_productivity pull-right" style="display: none;">
        {!! Form::open(array('action' => 'ProductivityController@upload', 'files' => true,'class'=>'form-inline')) !!}
        {!! Form::file('file',['class'=>'form-control']) !!}
        {!! Form::token() !!}
        {!! Form::submit('Upload',['class'=>'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
    <script>
        $(document).ready(function () {
            $("#prod_upload_file").click(function () {
                $(".upload_productivity").toggle("fast", function () {
                });
            });
        });

    </script>

    @if ($productivities->total())

        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>CSI Code</th>
                <th>Category</th>
                <th>Daily Output</th>
                <th>After Reduction</th>
                <th>Unit of Measure</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($productivities as $productivity)

                <tr>
                    <td class="col-md-1">{{ $productivity->code }}
                    </td>

                    </td>
                    <td>
                        {{$productivity->category->path}}
                    </td>
                    {{--<td>--}}
                        {{--{{$productivity->category->name}}--}}
                    {{--</td>--}}
                    {{--<td class="col-md-1">{{ isset($productivity->category->name)?$productivity->category->name:'' }}</td>--}}
                    <td class="col-md-1">{{ isset($productivity->daily_output)?$productivity->daily_output:'' }}</td>
                    <td class="col-md-1">{{ isset($productivity->after_reduction)?$productivity->after_reduction:'' }}</td>
                    <td class="col-md-1">{{ isset($productivity->units->type)?$productivity->units->type:'' }}
                    </td>
                    <td class="col-md-2">
                        <form action="{{ route('productivity.destroy', $productivity) }}" method="post">
                            {{csrf_field()}} {{method_field('delete')}}
                            <a class="btn btn-sm btn-primary" href="{{ route('productivity.edit', $productivity) }} "><i
                                        class="fa fa-edit"></i> Edit</a>
                            <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $productivities->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No productivity found</strong>
        </div>
    @endif
@stop
