@extends('layouts.app')

@section('title', 'Cost Control')

@section('header')
    <h2>Cost Control</h2>
@stop

@section('body')
    @if ($projectGroups->count())
        <table class="table table-hover projects-table table-bordered">
            <thead>
            <tr>
                <th class="col-sm-6">Project</th>

                <th class="col-sm-3">Budget Cost</th>
                <th class="col-sm-3">To Date Cost</th>
            </tr>
            </thead>
            <tbody>
            @foreach($projectGroups as $client => $group)
                <tr class="bg-blue-lightest">
                    <td class="col-sm-6">
                        <a href="#" data-target=".{{slug($client ?: 'Not Assigned')}}" class="group-label">
                            <i class="fa fa-plus-square-o"></i>
                            <strong>{{$client ?: 'Not Assigned'}}</strong>
                        </a>
                    </td>

                    <td>{{number_format($group->sum('latest_budget_cost'), 2)}}</td>
                    <td>{{number_format($group->sum('to_date_cost'), 2)}}</td>
                </tr>
                @foreach($group as $project)
                    <tr class="{{slug($client ?: 'Not Assigned')}} collapse">
                        <td>
                            <a class="project-label" href="{{route('project.cost-control', $project)}}">
                                {{$project->name}}
                            </a>

                            @if($project->rollup_level)
                                <span class="label label-info">{{$project->rollup_level}}</span>
                            @endif
                        </td>

                        <td>{{number_format($project->latest_budget_cost, 2)}}</td>
                        <td>{{number_format($project->to_date_cost, 2)}}</td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">No projects found</div>
    @endif
@endsection


@section('javascript')
    <script>
        $(function () {
            $('.group-label').click(function (e) {
                e.preventDefault();
                $(this).find('.fa').toggleClass('fa-plus-square-o fa-minus-square-o');
                $($(this).data('target')).toggleClass('in');
            });
        })
    </script>
@endsection

