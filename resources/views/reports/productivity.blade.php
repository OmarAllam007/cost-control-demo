@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <h2 align="center">Productivity</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@stop
@section('image')
    <img src="{{asset('images/reports/productivity.jpg')}}">
@endsection
@section('body')

    <ul class="list-unstyled tree">
        @foreach($data as $category=>$attributes)

            <li class="list-unstyled">
                <div class="tree--item"><strong>{{$category}}</strong></div>
                <ul>
                    <table class="table table-condensed table-striped">
                        <thead>
                        <tr>
                            {{--<th class="col-xs-2">Category</th>--}}
                            <th class="col-xs-3">Item Description</th>
                            <th class="col-xs-3">Crew Structure</th>
                            <th class="col-xs-2 text-center">Daily output</th>
                            <th class="col-xs-2 text-center">Unit of measure</th>
                            <th class="col-xs-2 text-center">After reduction</th>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($attributes['items'] as $item)
                            <tr>
                                {{--<td class="col-xs-2" ></td>--}}
                                <td>{{$item['name']}}</td>
                                <td>{!! nl2br(e($item['crew_structure'])) !!}</td>
                                <td class="text-center">{{$item['daily_output']}}</td>
                                <td class="text-center">{{$item['unit']}}</td>
                                <td class="text-center">{{$item['productivity']}}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </ul>
            </li>
        @endforeach

    </ul>
@endsection