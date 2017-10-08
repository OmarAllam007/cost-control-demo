@if (trim($project->description))
    <h4 class="text-center page-header">Project Brief</h4>
    <p class="small">
        {!! nl2br(e($project->description)) !!}
    </p>
@endif


@if (trim($project->discipline_brief))
    <h4 class="text-center page-header">Discipline Brief</h4>
    <p class="small">
        {!! nl2br(e($project->discipline_brief)) !!}
    </p>
@endif

@if (trim($project->assumptions))
    <h4 class="text-center page-header">Assumptions</h4>
    <p class="small">
        {!! nl2br(e($project->assumptions)) !!}
    </p>
@endif
