@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Global Financial Periods</h2>
        <a href="{{route('global-periods.create')}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Period</a>
    </div>
@endsection

@section('body')

    @if ($globalPeriods->count())
    <table class="table table-condensed table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            @foreach ($periods as $period)
            <td>{{$period->name}}</td>
            <td>{{$period->start_date->format('d M Y')}}</td>
            <td>{{$period->end_date->format('d M Y')}}</td>
            <td>
                <form action="{{route('global-periods.destroy', $period)}}" method="post">
                    {{csrf_field()}}
                    {{method_field('delete')}}

                    <a href="{{route('global-periods.delete', $period)}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                    @if (!$period->hasProjectPeriods())
                        <button class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
                    @endif
                </form>
            </td>
            @endforeach
        </tr>
        </tbody>
    </table>
    @else
        <div class="alert alert-info"><i class="fa fa-info-circle"></i> No periods found</div>
    @endif


@endsection