@extends('layouts.app')
@section('header')
    <a href="{{URL::previous()}}" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@endsection
@section('body')
    <h1 align="center" style="background: yellow;color: #000000;">BUDGET BY NUMBERS</h1>

    <div class="tree--item">
        <a href="#children-1" class="tree--item--label" data-toggle="collapse"><i
                    class="fa fa-chevron-circle-right"></i> {{$root}}
        </a>
    </div>

    <article id="children-1" class="tree--child collapse">
        <table class="table table-condensed table-striped " style="margin: 3px; padding: 5px;">
            <thead>
            <tr>
                <th class="col-md-3 bg-success">Description</th>
                <th class="col-md-3 bg-success">Budget Cost</th>
                <th class="col-md-2 bg-success">Budget Unit</th>
                <th class="col-md-1 bg-success">Unit of measure</th>
            </tr>
            </thead>
            @foreach($resources as $resource)

                @if($resource['id'])

                    <tbody>
                    <tr>
                        {{--<td class="col-md-3">{{$resource['type'] or ''}}</td>--}}
                        <td class="col-md-3 ">{{$resource['name'] or ''}}</td>
                        <td class="col-md-3">{{number_format($resource['budget_cost'], 2)}}</td>
                        <td class="col-md-2">{{number_format($resource['budget_unit'], 2)}}</td>
                        <td class="col-md-1">{{$resource['unit']  or ''}}</td>
                    </tr>

        @endif
    </article>

    @endforeach
    <tr style="border:solid #000000">
        <td>Total</td>
        <td>{{$total_budget_cost}}</td>
        <td>{{$total_budget_unit}}</td>
        <td></td>
    </tr>
    </tbody>
    </table>
@stop