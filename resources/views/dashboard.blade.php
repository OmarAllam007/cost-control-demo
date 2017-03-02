@extends('layouts.app')

@section('header')
    <h2><i class="fa fa-dashboard"></i> Dashboard</h2>
@endsection

@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.17/d3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.11/c3.min.js"></script>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.11/c3.min.css">
@endsection

@section('body')

    @include('dashboard.project')



    <div class="row">
        <div class="col-sm-6">
            @include('dashboard.resource_types');
        </div>

        <div class="col-sm-6">
            @include('dashboard.activities')
        </div>

        <div class="col-sm-6">
            @include('dashboard.resources')
        </div>
    </div>
@endsection