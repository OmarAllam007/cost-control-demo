@extends('layouts.app')

@section('title', 'Budget')

@section('header')
    <h2>Budget</h2>
@stop

@section('body')
    @if ($projectGroups->count())
        <table class="table table-hover projects-table table-bordered">
            <thead>
            <tr class="bg-primary">
                <th class="col-sm-6">Project</th>
                <th class="col-sm-2">Project Type</th>
                <th class="col-sm-2">Original Budget Cost</th>
                <th class="col-sm-2">Latest Budget Cost</th>
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
                    <td>&nbsp;</td>
                    <td>{{number_format($group->sum('original_budget_cost'), 2)}}</td>
                    <td>{{number_format($group->sum('latest_budget_cost'), 2)}}</td>
                </tr>
                @foreach($group as $project)
                <tr class="{{slug($client ?: 'Not Assigned')}} collapse">
                    <td>
                        <a class="project-label" href="{{route('project.budget', $project)}}">
                            {{$project->name}}
                        </a>
                    </td>
                    <td>{{$project->project_type}}</td>
                    <td>{{number_format($project->original_budget_cost, 2)}}</td>
                    <td>{{number_format($project->latest_budget_cost, 2)}}</td>
                </tr>
            @endforeach
            @endforeach
            </tbody>
        </table>
        {{--@foreach($projectGroups as $groupName => $projects)
            @if ($projects->count())
                @if ($counter % 2 == 0)
                    <section class="row">@endif
                        <article class="col-sm-6">
                            <div class="card">

                                <a class="card-title" href="#{{slug($groupName ?: 'not-assigned')}}"
                                   data-toggle="collapse">{{$groupName?: 'Not Assigned'}}</a>


                                <div class="card-body" id="{{slug($groupName ?: 'not-assigned')}}">
                                    @foreach($projects as $project)
                                        <div class="card-row display-flex">
                                            <h4 class="flex"><a
                                                        href="{{ route('project.budget', $project) }}">{{$project->name}}</a>
                                            </h4>
                                            --}}{{--<a class="btn btn-sm btn-info" href="{{ route('project.budget', $project) }}">Budget</a>--}}{{--
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                        @if ($counter % 2 != 0)</section>@endif
                @php ++$counter @endphp
            @endif
        @endforeach--}}
    @else
        <div class="alert alert-info">No projects found</div>
    @endif
@endsection

@section('javascript')
    <script>
        $(function () {
            $('.group-label').click(function(e) {
                e.preventDefault();
                $(this).find('.fa').toggleClass('fa-plus-square-o fa-minus-square-o');
                $($(this).data('target')).toggleClass('in');
            });
        })
    </script>
@endsection
