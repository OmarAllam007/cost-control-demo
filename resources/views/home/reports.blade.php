@extends('layouts.app')

@section('title', 'Reports')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Reports</h2>

        @can('dashboard')
        <a href="{{url('/dashboard')}}" class="btn btn-info btn-sm">
            <i class="fa fa-dashboard"></i> Dashboard
        </a>
        @endcan
    </div>
@endsection

@section('body')
    @if ($projectGroups->count())
        @foreach($projectGroups as $groupName => $projects)
            @if ($projects->count())
                <article class="card">


                    <h3 class="card-title">
                        <a href="#{{slug($groupName ?: 'not-assigned')}}"
                           data-toggle="collapse">{{$groupName?: 'Not Assigned'}}</a>
                    </h3>

                    <div class="card-body collapse" id="{{slug($groupName ?: 'not-assigned')}}">
                        @foreach($projects as $project)
                            <div class="card-row display-flex">
                                <h4 class="flex"><a href="{{route('project.reports', $project)}}">{{$project->name}}</a></h4>
                            </div>
                        @endforeach
                    </div>
                </article>

            @endif
        @endforeach
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