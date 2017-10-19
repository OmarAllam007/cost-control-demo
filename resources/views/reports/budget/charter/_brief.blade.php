@if (trim($project->description))
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">Project Brief</h4>
        </div>
        <div class="panel-body">
            <p class="small">
                {!! nl2br(e($project->description)) !!}
            </p>
        </div>
    </div>
@endif
