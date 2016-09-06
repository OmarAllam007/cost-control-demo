@if ($project->productivities->count())
    <table class="table table-condensed table-striped">
        <thead>
        <tr>
            <th>Code</th>
            <th>Category</th>
            <th>Daily output</th>
            <th>After reduction</th>
            <th>Unit of measure</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($project->productivities as $productivity)
            <tr>
                <td>{{$productivity->code}}</td>
                <td>{{$productivity->category->name}}</td>
                <td>{{$productivity->daily_output}}</td>
                <td>{{$productivity->after_reduction}}</td>
                <td>{{$productivity->units->type or ''}}</td>
                <td><a href="{{route('productivity.override', compact('project', 'productivity'))}}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Override</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No productivity found</div>
@endif