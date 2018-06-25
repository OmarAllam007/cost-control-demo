<section id="periods" class="project-tab">

    @can('periods', $project)
        <div class="form-group">
            <div class="btn-toolbar pull-right">
                <a href="{{route('period.create', compact('project'))}}" class="btn btn-primary btn-sm"><i
                            class="fa fa-plus"></i> Add Period</a>
            </div>
            <div class="clearfix"></div>
        </div>
    @endcan

    @if ($project->periods->count())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Open</th>
                <th>Actions</th>
            </tr>
            </thead>
            @foreach($project->periods as $period)
                <tbody>
                <tr>
                    <td>{{$period->name}}</td>
                    <td>{{$period->start_date->format('d M Y')}}</td>
                    <td>{{$period->end_date->format('d M Y')}}</td>
                    <td class="text-{{$period->is_open? 'success' : 'muted'}}"><i
                                class="fa fa-{{$period->is_open? 'check' : 'close'}}"></i></td>
                    <td>
                        <form action="{{route('period-report.store', $period)}}" method="post">
                            {{csrf_field()}}
                            <a href="{{route('period.edit', $period)}}" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            @if ($period->status == \App\Period::NONE)
                                <button class="btn btn-success btn-sm">
                                    <i class="fa fa-calculator"></i> Generate Reports
                                </button>
                            @elseif ($period->status == \App\Period::GENERATED)
                                <button class="btn btn-default btn-sm">
                                    <i class="fa fa-refresh"></i> Regenerate Reports
                                </button>
                            @else
                                <button class="btn btn-info btn-sm disabled" disabled>
                                    <i class="fa fa-spin fa-refresh"></i> Generating ...
                                </button>
                            @endif
                        </form>
                    </td>
                </tr>
                </tbody>
            @endforeach
        </table>
    @else
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i>
            No open period in the project. Please <a href="/period/create?project={{$project->id}}">add a period
                here</a>.
        </div>
    @endif

</section>