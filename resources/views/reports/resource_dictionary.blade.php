@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._resource_dictionary')
@endif
@section('header')
    <h2 align="center">Resource Dictionary Report</h2>
    <div class="pull-right">
        <a href="?print=1&paint=resource-dictionary" target="_blank" class="btn btn-default btn-sm"><i
                    class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')
    @foreach ($data as $root=>$divisions)
        <?php $counter = 0;?>
        @foreach($divisions['divisions'] as $divIndex => $resourceType)
            <ul class="list-unstyled tree">
                <li>
                    @if ($counter == 0)
                        <p class="blue-first-level"><a href="#{{$divIndex}}" style="color: white" data-toggle="collapse">{{$root}}</a></p>
                    @endif
                    @if(count($resourceType['parents']))
                        @foreach($resourceType['parents'] as $pKey=>$parent)
                            <ul class="list-unstyled">
                                <li class="blue-third-level">{{$parent['name']}}</li>
                                @endforeach
                                @endif
                                <article class="tree--child" id="{{$divIndex}}">
                                    <ul class="list-unstyled tree">
                                        <p class="blue-second-level"><a data-toggle="collapse">{{$resourceType['name']}}</a></p>
                                        <article class="tree--child " >
                                            <li>
                                                <table class="table table-condensed">
                                                    <thead>
                                                    <tr class="blue-third-level">
                                                        <th class="col-xs-1">CODE</th>
                                                        <th class="col-xs-1">RESOURCE NAME</th>
                                                        <th class="col-xs-1">UNIT</th>
                                                        <th class="col-xs-1">RATE</th>
                                                        <th class="col-xs-1">SUPPLIER/SUBCON.</th>
                                                        <th class="col-xs-1">REFERENCE</th>
                                                        <th class="col-xs-1">Waste %</th>
                                                        <th class="col-xs-1">BUDGET UNIT</th>
                                                        <th class="col-xs-1">BUDGET COST</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($divisions['divisions'][$divIndex]['resources'] as $index => $resource)
                                                        <tr>

                                                            <td class="col-xs-1">
                                                                {{$resource['code']}}
                                                            </td>
                                                            <td class="col-xs-1 blue-fourth-level">
                                                                {{$resource['name']}}
                                                            </td>
                                                            <td class="col-xs-1">
                                                                {{$resource['unit']}}
                                                            </td>
                                                            <td class="col-xs-1 blue-fourth-level">
                                                                {{number_format($resource['rate'],2)}}
                                                            </td>
                                                            <td class="col-xs-1">
                                                                {{$resource['partner']}}
                                                            </td>

                                                            <td class="col-xs-1 blue-fourth-level">
                                                                {{$resource['reference']}}
                                                            </td>
                                                            <td class="col-xs-1">{{number_format($resource['waste'],2)}}
                                                                %
                                                            </td>
                                                            <td class="col-xs-1">{{number_format($resource['budget_unit'],2)}}</td>
                                                            <td class="col-xs-1">{{number_format($resource['budget_cost'],2)}}</td>
                                                            <?php $counter++;?>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </li>
                                        </article>

                                    </ul>
                                </article>
                            </ul>
                </li>
            </ul>

        @endforeach
    @endforeach
@endsection
@section('javascript')
    <script>

    </script>
@endsection