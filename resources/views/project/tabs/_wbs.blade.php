<div class="form-group tab-actions clearfix">
    <a href="{{route('wbs-level.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm pull-right">
        <i class="fa fa-plus"></i> Add Level
    </a>
</div>

@if ($project->wbs_tree)
    <ul class="list-unstyled tree">
        @foreach($project->wbs_tree as $wbs_level)
            @include('wbs-level._recursive', compact('wbs_level'))
        @endforeach
    </ul>
@else
    <div class="alert alert-info"><i class="fa fa-info-circle"></i> No WBS found</div>
@endif