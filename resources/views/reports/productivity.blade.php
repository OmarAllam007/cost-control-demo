@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._productivity')
@endif
@section('header')
    <h2 align="center">Productivity</h2>
    <div class="pull-right">
        <a href="{{route('budget_productivity.export',
        ['project'=>$project])}}"
           target="_blank" class="btn
        btn-info
        btn-sm"><i class="fa fa-cloud-download"></i>
            Export</a>

        <a href="?print=1&paint=productivity" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@stop
@section('image')
    <img src="{{asset('images/reports/productivity.jpg')}}" height="80%">
@endsection
@section('body')

    <ul class="list-unstyled tree">
        @foreach($data as $category=>$attributes)
            <ul class="list-unstyled">
                @foreach($attributes['parents'] as $key=>$parent)
                    <li class="blue-second-level tree--item">
                        <a style="color: white;" href="#{{$key}}" data-toggle="collapse">
                            {{$parent['name']}}</a></li>
                @endforeach
                <li class="list-unstyled tree--child collapse" id="{{$key}}">
                    <div class="tbl-division">{{$category}}</div>
                    <ul class="list-unstyled">
                        <table class="table table-condensed">
                            <thead>
                            <tr class="tbl-header">
                                {{--<th class="col-xs-2">Category</th>--}}
                                <th class="col-xs-3">Item Description</th>
                                <th class="col-xs-3">Crew Structure</th>

                                <th class="col-xs-2 text-center">Unit of
                                    measure
                                </th>
                                <th class="col-xs-2 text-center">Output
                                </th>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($attributes['items'] as $item)
                                <tr class="tbl-content">
                                    {{--<td class="col-xs-2" ></td>--}}
                                    <td>{{$item['name']}}</td>
                                    <td>{!! nl2br(e($item['crew_structure'])) !!}</td>
                                    {{--<td class="text-center">{{$item['daily_output']}}</td>--}}
                                    <td class="text-center">{{$item['unit']}}</td>
                                    <td class="text-center">{{$item['productivity']}}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </ul>
                </li>
            </ul>
        @endforeach
    </ul>
@endsection