<div class="form-group tab-actions clearfix">
    <a href="{{route('survey.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm pull-right">
        <i class="fa fa-plus"></i> Add Quantity Survey
    </a>
</div>

@if ($project->quantities)
    <table>
        <thead>
        <tr>
            <th>Code</th>
            <th>Budget Quantity</th>
            <th>Eng Quantity</th>
        </tr>
        </thead>
    </table>
@else
    <div class="alert alert-info"><i class="fa fa-info-circle"></i> No WBS found</div>
@endif