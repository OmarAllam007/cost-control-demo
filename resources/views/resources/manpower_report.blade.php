@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <h2>BUDGET BY NUMBERS</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@stop
@section('image')
    <img src="{{asset('images/reports/manpower.jpg')}}">
@endsection
@section('body')
    <div class="tree--item">
       <strong>{{$root}}</strong>
    </div>

    <article id="children-1" class="tree--child">
        <table class="table table-condensed table-striped " style="margin: 3px; padding: 5px;">
            <thead>
            <tr>
                <th class="col-md-3 bg-success">Description</th>
                <th class="col-md-3 bg-success">Budget Cost</th>
                <th class="col-md-2 bg-success">Budget Unit</th>
                <th class="col-md-1 bg-success">Unit of measure</th>
            </tr>
            </thead>
            <tbody>
            @foreach($resources as $resource)

                @if($resource['id'])

                    <tr>
                        {{--<td class="col-md-3">{{$resource['type'] or ''}}</td>--}}
                        <td class="col-md-3 ">{{$resource['name'] or ''}}</td>
                        <td class="col-md-3">{{number_format($resource['budget_cost'], 2)}}</td>
                        <td class="col-md-2">{{number_format($resource['budget_unit'], 2)}}</td>
                        <td class="col-md-1">{{$resource['unit']  or ''}}</td>
                    </tr>
                @endif
            @endforeach
            <tr style="border:solid #000000">
                <td>Total</td>
                <td>{{number_format($total_budget_cost, 2)}}</td>
                <td>{{number_format($total_budget_unit, 2)}}</td>
                <td></td>
            </tr>
            </tbody>
        </table>
    </article>
@stop