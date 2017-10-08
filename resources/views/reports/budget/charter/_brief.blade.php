@if (trim($project->description))
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4 class="panel-title">Project Brief</h4>
        </div>

        <div class="panel-body">
            {!! nl2br(e($project->description)) !!}
        </div>
    </div>
@endif


@if (trim($project->discipline_brief))
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4 class="panel-title">Discipline Brief</h4>
        </div>

        <div class="panel-body">
            {!! nl2br(e($project->discipline_brief)) !!}
        </div>
    </div>
@endif

@if (trim($project->assumptions))
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4 class="panel-title">Assumptions</h4>
        </div>

        <div class="panel-body">
            {!! nl2br(e($project->assumptions)) !!}
        </div>
    </div>
@endif
