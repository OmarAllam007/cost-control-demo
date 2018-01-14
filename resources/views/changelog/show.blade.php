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
                        <a href="#log_{{$log->id}}" data-toggle="collapse">
                            <strong class="text-primary">{{$log->user->name}}</strong>
                            {{translate_method($log->method)}}

                            <strong class="text-primary">{{$log->base_model_name}}:</strong>
                            @if ($log->first_change->subject)
                                <small class="text-danger">{{$log->first_change->subject->descriptor ?? ''}}</small>
                            @else

                            @endif

                            at <strong class="text-primary">{{$log->created_at->format('d M Y H:i')}}</strong>
                        </a>
                    </div>

                    <section class="collapse" id="log_{{$log->id}}">
                        @foreach($log->changes as $change)
                            @if ($change->hasChangedFields())
                                <header>
                                    <strong>{{$change->simple_model_name}}</strong>:
                                    <small class="text-danger">{{$change->subject->descriptor ?? ''}}</small>
                                </header>
                                <ul>
                                    @if ($change->original)
                                        @foreach($change->original as $field => $value)
                                            @if (!empty($value) && !empty($change->updated[$field]))
                                                <li>{{$field}}: changed from <strong>{{$value ?? 'None'}}</strong> to
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
                    </section>
                </article>
            @endforeach
        </section>

        <div class="text-right">
            {{$logs->appends('date', $date->format('Y-m-d'))->links()}}
        </div>
    @else
        <div class="alert alert-info"><i class="fa fa-info-circle"></i> No logs found</div>
    @endif
@endsection