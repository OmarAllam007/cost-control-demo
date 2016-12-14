<section id="periods" class="project-tab">
    <div class="btn-toolbar pull-right">
        <a href="{{route('period.create', compact('project'))}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Period</a>
    </div>
    <div class="clearfix"></div>

    <table class="table table-condensed table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Start Date</th>
            <th>Open</th>
            <th>Actions</th>
        </tr>
        </thead>
        @foreach($project->periods as $period)
        <tbody>
        <tr>
            <td>{{$period->name}}</td>
            <td>{{$period->start_date->format('d/m/Y')}}</td>
            <td class="text-{{$period->is_open? 'success' : 'muted'}}"><i class="fa fa-{{$period->is_open? 'check' : 'close'}}"></i></td>
            <td>
                <a href="{{route('period.edit', $period)}}" class="btn btn-primary btn-sm">
                    <i class="fa fa-edit"></i> Edit
                </a>
            </td>
        </tr>
        </tbody>
        @endforeach
    </table>

</section>