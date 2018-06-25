@extends(request('iframe')? 'layouts.iframe' : 'layouts.app')

@section('title', 'Send Cost Control Reports')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Send Cost Control Reports &mdash; {{$project->name}}</h2>
        <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-small">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')
    <form action="" method="post" class="row">
        {{csrf_field()}}

        <div class="form-group col-sm-6 col-md-4">
            <label for="periodSelect" class="control-label"></label>
            <select name="period_id" id="periodSelect" class="form-control">
                @foreach($periods as $period)
                    <option value="{{$period->id}}">{{$period->name}}</option>
                @endforeach
            </select>
        </div>

        @foreach($project_roles as $role_id => $group)
            <article class="col-md-9 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <label>
                            <input type="hidden" name="schedule[{{$role_id}}][enabled]" value="0">
                            <input type="checkbox" name="schedule[{{$role_id}}][enabled]" class="select-role"
                                   value="1" {{old("schedule.{$role_id}.enabled", 1)? 'checked' : ''}}>
                            {{$roles[$role_id]->name}}
                        </label>
                    </div>

                    <div class="panel-body collapse">
                        <div class="row">
                            <div class="col-sm-4">
                                <h4>Users</h4>
                                @foreach($group as $project_role)
                                    <article class="checkbox">
                                        <label>
                                            <input type="hidden"
                                                   name="schedule[{{$role_id}}][users][{{$project_role->id}}]"
                                                   value="0">
                                            <input type="checkbox" value="{{$project_role->id}}"
                                                   name="schedule[{{$role_id}}][users][{{$project_role->id}}]"
                                                    {{old("schedule.{$role_id}.users.{$project_role->id}", 1)? 'checked' : ''}}>
                                            {{$project_role->name}}
                                        </label>
                                    </article>
                                @endforeach
                            </div>
                            <div class="col-sm-4 report-group">
                                <h4>Cost Reports</h4>
                                <p>
                                    <a href="#" class="select-all">Select All</a> /
                                    <a href="#" class="remove-all">Remove All</a>
                                </p>
                                @foreach($roles[$role_id]->cost_reports as $report)
                                    @if ($project->is_activity_rollup && $report->class_name == \App\Http\Controllers\Reports\CostReports\VarianceAnalysisReport::class)
                                        @continue
                                    @endif

                                    @if ($project->is_activity_rollup && $report->class_name == App\Http\Controllers\Reports\CostReports\ResourceCodeReport::class)
                                        @continue
                                    @endif

                                    @if ($project->hasRollup() && $report->class_name == App\Http\Controllers\Reports\CostReports\BoqReport::class)
                                        @continue
                                    @endif

                                    @if ($project->hasRollup() && $report->class_name == App\Http\Controllers\Reports\CostReports\OverdraftReport::class)
                                        @continue
                                    @endif

                                    <article class="checkbox">
                                        <label>
                                            <input type="hidden" name="schedule[{{$role_id}}][reports][{{$report->id}}]"
                                                   value="0">
                                            <input type="checkbox" value="{{$report->id}}"
                                                   name="schedule[{{$role_id}}][reports][{{$report->id}}]"
                                                    {{old("schedule.{$role_id}.reports.{$report->id}", 1)? 'checked' : ''}}>
                                            {{$report->name}}
                                        </label>
                                    </article>
                                @endforeach
                            </div>
                            {{--<div class="col-sm-4 report-group">
                                <h4>Budget Reports</h4>
                                <p>
                                    <a href="#" class="select-all">Select All</a> /
                                    <a href="#" class="remove-all">Remove All</a>
                                </p>
                                @foreach($roles[$role_id]->budget_reports as $report)
                                    <article class="checkbox">
                                        <label>
                                            <input type="hidden" name="schedule[{{$role_id}}][reports][{{$report->id}}]" value="0">
                                            <input type="checkbox" value="{{$report->id}}"
                                                   name="schedule[{{$role_id}}][reports][{{$report->id}}]"
                                                    {{old("schedule.{$role_id}.reports.{$report->id}", 1)? 'checked' : ''}}>
                                            {{$report->name}}
                                        </label>
                                    </article>
                                @endforeach
                            </div>--}}
                        </div>
                    </div>
                </div>
            </article>
        @endforeach

        <div class="form-group col-sm-12">
            <button class="btn btn-primary">
                <i class="fa fa-check"></i> Save
            </button>
        </div>
    </form>
@endsection

@section('javascript')
    <script>
        $(function () {
            $('.select-role').on('change', function () {
                if (this.checked) {
                    $(this).closest('.panel').find('.panel-body').addClass('in');
                } else {
                    $(this).closest('.panel').find('.panel-body').removeClass('in');
                }
            }).change();

            $('.select-all').on('click', function (e) {
                e.preventDefault();
                $(e.currentTarget).closest('.report-group').find(':input').prop('checked', true);
            });

            $('.remove-all').on('click', function (e) {
                e.preventDefault();
                $(e.currentTarget).closest('.report-group').find(':input').prop('checked', false);
            });
        });
    </script>
@endsection