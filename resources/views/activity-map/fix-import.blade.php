@extends('layouts.app')

@section('header')
    <h2>{{$project->name}} &mdash; Fix Import Activity Map</h2>

    <div class="pull-right">
        <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-sm"><i
                    class="fa fa-chevron-left"></i> Back to project</a>
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-sm-9">

            {{Form::open()}}

            <table class="table table-condensed table-striped table-hover">
                <thead>
                <tr>
                    <th>Activity Code</th>
                    <th>Store Activity</th>
                    <th>Selected Activity</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td>{{$row[0]}}</td>
                        <td>{{$row[1]}}</td>
                        <td>
                            <a href="#ActivityModal"
                               class="select-activity">{{old("mapping[{$row[1]}]") ?: "Select Activity"}}</a>
                            {{Form::hidden("mapping[{$row[1]}]", null)}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="form-group">
                <button class="btn btn-primary">
                    <i class="fa fa-check"></i> Submit
                </button>
            </div>

            {{Form::close()}}

        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="ActivityModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">Select Activity</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="search" id="SearchActivity">
                    </div>

                    <table class="table table-striped table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>Code</th>
                            <th>WBS</th>
                            <th>Activity</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            @foreach($codes as $code => $activity)
                                <td><a heref="#" class="select-activity">{{$code}}</a></td>
                                <td>{{$activity->wbs->path}}</td>
                                <td>{{$activity->activity}}</td>
                            @endforeach
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection