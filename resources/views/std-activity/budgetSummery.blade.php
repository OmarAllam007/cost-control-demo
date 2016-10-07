@extends('layouts.app')
@section('header')
    <a href="{{ URL::previous()}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left "></i> Back</a>
@endsection
@section('body')

    <ul class="list-unstyled tree">
        @foreach($parents as $division)
            @include('std-activity._recursive_budget_summery', compact('division'))
        @endforeach

    </ul>
@endsection