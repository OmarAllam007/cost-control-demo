@extends('layouts.app')

@section('header')
    <h2>Productivity</h2>
    <a href="{{ route('productivity.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i>
        Add productivity</a>
    {{--<button href="" class="btn btn-sm btn-primary pull-right" id="prod_upload_file"><i class="fa fa-plus"></i>--}}
        {{--Upload Productivity--}}
    {{--</button>--}}

@stop

@section('body')
    @if ($categories->total())
        <ul class="list-unstyled tree">
            @foreach($categories as $category)

                @include('productivity._recursive')
            @endforeach
        </ul>

        {{ $categories->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No Categories found</strong></div>
    @endif
@stop


{{--@section('body')--}}
    {{--<div class="form-group upload_productivity pull-right" style="display: none;">--}}
        {{--{!! Form::open(array('action' => 'ProductivityController@upload', 'files' => true,'class'=>'form-inline')) !!}--}}
        {{--{!! Form::file('file',['class'=>'form-control']) !!}--}}
        {{--{!! Form::token() !!}--}}
        {{--{!! Form::submit('Upload',['class'=>'btn btn-primary']) !!}--}}
        {{--{!! Form::close() !!}--}}
    {{--</div>--}}
    {{--<script>--}}
        {{--$(document).ready(function () {--}}
            {{--$("#prod_upload_file").click(function () {--}}
                {{--$(".upload_productivity").toggle("fast", function () {--}}
                {{--});--}}
            {{--});--}}
        {{--});--}}

    {{--</script>--}}

    {{--@if ($productivities->total())--}}
        {{--<table class="table table-condensed table-striped">--}}
            {{--<thead>--}}
            {{--<tr>--}}
                {{--<th>Name</th>--}}
                {{--<th></th>--}}
                {{--<th>Crew Structure</th>--}}
                {{--<th>Unit</th>--}}
                {{--<th>crew hours</th>--}}
                {{--<th>crew equip</th>--}}
                {{--<th>daily output</th>--}}
                {{--<th>man hours</th>--}}
                {{--<th>equip hours</th>--}}
                {{--<th>reduction factor</th>--}}
                {{--<th>after reduction</th>--}}
                {{--<th>source</th>--}}
                {{--<th>Actions</th>--}}
            {{--</tr>--}}
            {{--</thead>--}}
            {{--<tbody>--}}
            {{--@foreach($productivities as $productivity)--}}
                {{--<tr>--}}


                    {{--<td class="col-md-1">{{ isset($productivity->category->name)?$productivity->category->name:'' }}</td>--}}


                    {{--<td class="col-md-1">{{ $productivity->description }}--}}
                    {{--</td>--}}
                    {{--<td class="col-md-1">{{ $productivity->crew_structure }}--}}
                    {{--</td>--}}
                    {{--<td class="col-md-1">{{ isset($productivity->units->type)?$productivity->units->type:'' }}--}}
                    {{--</td>--}}

                    {{--<td class="col-md-2">--}}
                        {{--<form action="{{ route('productivity.destroy', $productivity) }}" method="post">--}}
                            {{--{{csrf_field()}} {{method_field('delete')}}--}}
                            {{--<a class="btn btn-sm btn-primary" href="{{ route('productivity.edit', $productivity) }} "><i--}}
                                        {{--class="fa fa-edit"></i> Edit</a>--}}
                            {{--<button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>--}}
                        {{--</form>--}}
                    {{--</td>--}}
                {{--</tr>--}}
            {{--@endforeach--}}
            {{--</tbody>--}}
        {{--</table>--}}

        {{--{{ $productivities->links() }}--}}
    {{--@else--}}
        {{--<div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No productivity found</strong>--}}
        {{--</div>--}}
    {{--@endif--}}
{{--@stop--}}
