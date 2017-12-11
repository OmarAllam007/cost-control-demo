@extends(request('iframe')? 'layouts.iframe' : 'layouts.app')

@section('title', 'Send Budget Reports')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Send Reports &mdash; {{$project->name}}</h2>
        <a href="{{route('project.budget', $project)}}" class="btn btn-default btn-small">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')
    <form action="" method="post" class="row">
        {{csrf_field()}}

        @foreach($project_roles as $role_id => $group)
            <article class="col-md-9 col-sm-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <label>
                        <input type="hidden" name="schedule[{{$role_id}}][enabled]" value="0">
                        <input type="checkbox" name="schedule[{{$role_id}}][enabled]" class="select-role" value="1" {{old("schedule.{$role_id}.enabled", 1)? 'checked' : ''}}>
                        {{$roles[$role_id]->name}}
                    </label>
                </div>

                <div class="panel-body collapse">
                    <div class="row">
                        <div class="col-sm-6">
                            <h4>Users</h4>
                            @foreach($group as $project_role)
                                <article class="checkbox">
                                    <label>
                                        <input type="hidden" name="schedule[{{$role_id}}][users][{{$project_role->id}}]" value="0">
                                        <input type="checkbox" value="{{$project_role->id}}"
                                               name="schedule[{{$role_id}}][users][{{$project_role->id}}]"
                                               {{old("schedule.{$role_id}.users.{$project_role->id}", 1)? 'checked' : ''}}>
                                        {{$project_role->name}}
                                    </label>
                                </article>
                            @endforeach
                        </div>
                        <div class="col-sm-6">
                            <h4>Reports</h4>
                            @foreach($roles[$role_id]->budget_reports as $report)
                                <article class="checkbox">
                                    <label>
                                        <input type="hidden" name="schedule[{{$role_id}}][users][{{$report->id}}]" value="0">
                                        <input type="checkbox" value="{{$report->id}}"
                                               name="schedule[{{$role_id}}][reports][{{$report->id}}]"
                                               {{old("schedule.{$role_id}.reports.{$report->id}", 1)? 'checked' : ''}}>
                                        {{$report->name}}
                                    </label>
                                </article>
                            @endforeach
                        </div>
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
        $(function() {
            $('.select-role').on('change', function() {
                if (this.checked) {
                    $(this).closest('.panel').find('.panel-body').addClass('in');
                } else {
                    $(this).closest('.panel').find('.panel-body').removeClass('in');
                }
            }).change();
        });
    </script>
@endsection