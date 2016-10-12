@extends('layouts.app')
@section('header')
    <h2 align="center">Productivity</h2>
    <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop
@section('body')

    <ul class="list-unstyled tree">
        @foreach($data as $category=>$attributes)

            <li class="list-unstyled">
                <div class="tree--item">
                    <a href="#children" class="tree--item--label"><i
                                class="fa fa-chevron-circle-right"></i> {{$category}}
                    </a>
                </div>
                <ul>
                    <table class="table table-condensed table-striped">
                        <thead>
                        <tr>
                            {{--<th class="col-xs-2">Category</th>--}}
                            <th class="col-xs-2">Item Description</th>
                            <th class="col-xs-2">Crew Structure</th>
                            <th class="col-xs-2">Daily output</th>
                            <th class="col-xs-2">Unit of measure</th>
                            <th class="col-xs-2">After reduction</th>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($attributes['items'] as $item)
                            <tr>
                                {{--<td class="col-xs-2" ></td>--}}
                                <td class="col-xs-2">{{$item['name']}}</td>
                                <td class="col-xs-2">{{$item['crew_structure']}}</td>
                                <td class="col-xs-2">{{$item['daily_output']}}</td>
                                <td class="col-xs-2">{{$item['unit']}}</td>
                                <td class="col-xs-2">{{$item['productivity']}}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </ul>
            </li>
        @endforeach

    </ul>
@endsection