<div class="form-group tab-actions pull-right">
    <a href="{{route('wbs-level.import', $project->id)}}" class="btn btn-success btn-sm">
        <i class="fa fa-cloud-upload"></i> Import
    </a>

    <a href="{{route('wbs-level.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Add Level
    </a>
</div>


<div class="clearfix"></div>
@if ($project->wbs_tree->count())
    <ul class="list-unstyled tree">
        @foreach($project->wbs_tree as $wbs_level)
            @include('wbs-level._recursive', compact('wbs_level'))
        @endforeach
    </ul>
@else
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No WBS found</div>
@endif