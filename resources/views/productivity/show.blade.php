@extends('layouts.app')

@section('header')
<h2>Productivity</h2>

<form action="{{ route('productivity.destroy', $productivity)}}" class="pull-right" method="post">
    {{csrf_field()}} {{method_field('delete')}}

    <a href="{{ route('productivity.edit', $productivity)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
    <a href="{{ route('productivity.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
</form>
@stop

@section('body')
    <table class="table table-condensed">
        <tbody>
        <tr>
            <th>Project</th>
            <td>{{$project->name or ''}}</td>
        </tr>
        <tr>
            <th>Productivity Code</th>
            <td>{{$productivity->code}}</td>
        </tr>

        </tbody>
    </table>

    <h4 class="page-header">Productivity Details</h4>
    <div class="form-group clearfix">
        <table class="table table-condensed">
            <tbody>
            <tr>
                <th>Description</th>
                <td>{{$productivity->description ?:''}}</td>
            </tr>
            <tr>
                <th>Unit</th>
                <td>{{$productivity->units->type ?:''}}</td>
            </tr>
            <tr>
                <th>Crew Structure</th>
                <td>{{$productivity->crew_structure ?:''}}</td>
            </tr>


            <tr>
                <th>Reduction Factor</th>
                <td>{{$productivity->reduction_factor}}</td>
            </tr>
            <tr>
                <th>Daily Output</th>
                <td>{{$productivity->daily_output}}</td>
            </tr>
            <tr>
                <th>Daily Output (After Reduction)</th>
                <td>{{$productivity->getAfterReductionAttribute()}}</td>
            </tr>

            <tr>
                <th>Crew Hours</th>
                <td>{{$productivity->getCrewManAttribute($productivity->crew_structure)}}</td>
            </tr>

            <tr>
                <th>Crew Equipments</th>
                <td>{{$productivity->getCrewEquipAttribute($productivity->crew_structure)}}</td>
            </tr>



            <tr>
                <th>Man Hours</th>
                <td>{{$productivity->getManHoursAttribute()}}</td>
            </tr>

            <tr>
                <th>Equipment Hours</th>
                <td>{{$productivity->getEquipHoursAttribute()}}</td>
            </tr>

            <tr>
                <th>Source</th>
                <td>{{$productivity->source}}</td>
            </tr>


            </tbody>
        </table>
    </div>

@stop

