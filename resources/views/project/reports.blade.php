@extends('layouts.app')

@section('title', 'Reports')

@section('header')
    <h2>{{$project->name}} &mdash; Reports</h2>
@endsection

@section('body')
    <h3 class="page-header">Budget Reports</h3>
    @include('project.tabs._report', ['skipButtons' => 1])

    <h3 class="page-header">Cost Control Reports</h3>
    @include('project.cost-control._report', ['skipButtons' => 1])
@endsection
