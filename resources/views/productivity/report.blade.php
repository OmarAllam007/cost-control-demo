@extends('layouts.app')
@section('body')

    @if ($project->productivities->count())
        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-2">Category</th>
                <th class="col-xs-2">Item Description</th>
                <th class="col-xs-2">Crew Structure</th>
                <th class="col-xs-2">Daily output</th>
                <th class="col-xs-2">Unit of measure</th>
                <th class="col-xs-2">After reduction</th>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($project->productivities as $productivity)
                <tr>

                    <td class="col-xs-2" >{{$productivity->category->path}}</td>
                    <td class="col-xs-2">{{$productivity->description}}</td>
                    <td class="col-xs-2">{!!nl2br($productivity->crew_structure)!!}</td>
                    <td class="col-xs-2">{{$productivity->versionFor($project->id)->daily_output}}</td>
                    <td class="col-xs-2">{{$productivity->units->type or ''}}</td>
                    <td class="col-xs-2">{{$productivity->versionFor($project->id)->after_reduction}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @endif

@endsection