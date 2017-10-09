@if (trim($project->discipline_brief))
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4 class="panel-title">Discipline Brief</h4>
        </div>

        <div class="panel-body">
            <p class="small">
                {!! nl2br(e($project->discipline_brief)) !!}
            </p>
        </div>
    </div>
@endif