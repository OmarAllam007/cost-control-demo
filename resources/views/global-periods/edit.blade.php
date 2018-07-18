@extends('home.master-data')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Edit Global Financial Period</h2>
        <a href="{{route('global-periods.index')}}" class="btn btn-default btn-sm"><i class="fa fa-plus"></i> Back</a>
    </div>
@endsection

@section('content')

    {{ Form::model($global_period, ['route' => ['global-periods.update', $global_period], 'method' => 'patch']) }}

    @include('global-periods.form')

    {{Form::close()}}

@endsection

@section('css')
    <style>
        .section-header {
            font-size: 16px;
            font-weight: 700;
            margin-top: 10px;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e5e5;
        }

        .b-1 {
            border-right: 1px solid #e5e5e5;
        }
    </style>
@endsection