@extends('layouts.app')

@section('title', 'Cost Control')

@section('header')
    <h2>Cost Control</h2>
@stop

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
                                <h4 class="flex"><a href="{{route('project.cost-control', $project)}}">{{$project->name}}</a></h4>
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

