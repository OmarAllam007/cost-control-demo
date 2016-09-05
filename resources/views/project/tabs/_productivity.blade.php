<div class="form-group tab-actions pull-right">


    <a href="{{route('productivity.create')}}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Add Productivity
    </a>
</div>
<div class="clearfix"></div>


@if ($project->productivity)
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
        @foreach($project->productivity as $productivity)
            <tr>
                <td>{{$productivity->code}}</td>
                <td>{{$productivity->category->name}}</td>
                <td>{{$productivity->daily_output}}</td>
                <td>{{$productivity->after_reduction}}</td>
                <td>{{$productivity->units->type}}</td>
                <td><a href="#" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i>Override</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>

@else
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No productivity found</div>
@endif