@extends('layouts.app')

@section('header')
    <h2>BOQ Division</h2>
    <a href="{{ route('boq-division.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add BOQ Division</a>
@stop

@section('body')

    @if ($boqDivisions->total())
        <ul class="list-unstyled tree">
            @foreach($boqDivisions as $division)
                @include('boq-division._recursive', compact('division'))
            @endforeach
        </ul>

    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No boq divisions found</strong></div>
    @endif

@stop
