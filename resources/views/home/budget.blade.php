@extends('layouts.app')

@section('title', 'Budget')

@section('header')
    <h2>Budget - Projects</h2>
@stop

@section('body')
    @php $counter = 0; @endphp
    @if ($projectGroups->count())

            @foreach($projectGroups as $groupName => $projects)
                @if ($projects->count())
                    @if ($counter % 2 == 0)<section class="row">@endif
                        <article class="col-sm-6">
                            <div class="card">

                                    <a class="card-title" href="#{{slug($groupName ?: 'not-assigned')}}"
                                       data-toggle="collapse">{{$groupName?: 'Not Assigned'}}</a>
                                

                                <div class="card-body" id="{{slug($groupName ?: 'not-assigned')}}">
                                    @foreach($projects as $project)
                                        <div class="card-row display-flex">
                                            <h4 class="flex"><a href="{{ route('project.budget', $project) }}">{{$project->name}}</a>
                                            </h4>
                                            {{--<a class="btn btn-sm btn-info" href="{{ route('project.budget', $project) }}">Budget</a>--}}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @if ($counter % 2 != 0)</section>@endif
                    @php ++$counter @endphp
                @endif
            @endforeach

    @endif
@endsection

@section('css')
    <style>
        a.card-title {
            display: block;
        }

        .card-body {
            overflow-y: auto;
        }
    </style>
    
    @endsection

@section('javascript')
    <script>
        $(function() {
            const cards = $('.card-body');
            let maxh = cards.toArray().reduce((max, item) => Math.max(max, $(item).height()), 0);
            cards.height(maxh);
        })
    </script>
@endsection
