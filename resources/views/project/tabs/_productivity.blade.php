
@if ($project->productivities->count())
    <table class="table table-condensed table-striped table-fixed">
        <thead>
        <tr>
            <th class="col-xs-2">Code</th>
            <th class="col-xs-2">Category</th>
            <th class="col-xs-2">Daily output</th>
            <th class="col-xs-2">After reduction</th>
            <th class="col-xs-2">Unit of measure</th>
            <th class="col-xs-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($project->productivities as $productivity)
            <tr>
                <td class="col-xs-2">{{$productivity->code}}</td>
                <td class="col-xs-2">{{$productivity->category->name}}</td>
                <td class="col-xs-2">{{$productivity->daily_output}}</td>
                <td class="col-xs-2">{{$productivity->after_reduction}}</td>
                <td class="col-xs-2">{{$productivity->units->type or ''}}</td>
                <td class="col-xs-2"><a href="{{route('productivity.override', compact('project', 'productivity'))}}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Override</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No productivity found</div>
@endif