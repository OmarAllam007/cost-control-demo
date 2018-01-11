@extends('layouts.app')

@section('title', 'Changelog')

@section('header')
    <div class="display-flex">
        <h3 class="flex">{{$project->name}} &mdash; Changelog</h3>

        <a href="{{route('project.budget', $project)}}" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back to project
        </a>
    </div>
@endsection

@section('body')
    @include('changelog.filters')

    @if ($logs->count())
        <section class="list-group">

            @foreach($logs as $log)
                <article class="log-container list-group-item">
                    <div class="list-group-item-heading">
                        <strong class="text-primary">{{$log->user->name}}</strong>
                        {{$log->method == 'PATCH'? 'updated' : 'created'}}
                        <strong class="text-primary">{{$log->base_model_name}}</strong>
                        at <strong class="text-primary">{{$log->created_at->format('d M Y H:i')}}</strong>
                    </div>

                    @foreach($log->changes as $change)
                        @if ($change->hasChangedFields())
                        <p><strong>{{$change->id}} &mdash; {{$change->simple_model_name}}</strong></p>
                        <ul>
                            @if ($change->original)
                                @foreach($change->original as $field => $value)
                                    @if ($value || $change->updated[$field])
                                        <li>{{$field}}: changed from <strong>{{$value}}</strong> to
                                            <strong>{{$change->updated[$field] ?? 'None'}}</strong></li>
                                    @endif
                                @endforeach
                            @else
                                @foreach($change->original as $field => $value)
                                    <li>{{$field}}: {{$value}}</li>
                                @endforeach
                            @endif
                        </ul>
                        @endif
                    @endforeach
                </article>
            @endforeach
        </section>
    @endif
@endsection