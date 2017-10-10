@if (trim($project->assumptions))
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">Assumptions</h4>
        </div>

        <div class="panel-body">
            <p class="small">
                {!! nl2br(e($project->assumptions)) !!}
            </p>
        </div>
    </div>
@endif
