<h4 class="section-header">Revised Contract information</h4>

<div class="row">
    <article class="col-xs-4">
        <dl>
            <dt>Change Order Value</dt>
            <dd>{{number_format($period->change_order_value, 2)}}</dd>
        </dl>
    </article>

    <article class="col-xs-4">
        <dl>
            <dt>Time Extension</dt>
            <dd>{{$period->time_extension}}</dd>
        </dl>
    </article>

    <article class="col-xs-4">
        <dl>
            <dt>Planned Start Date</dt>
            <dd>{{\Carbon\Carbon::parse($project->project_start_date)->format('d M Y')}}</dd>
        </dl>
    </article>

    <article class="col-xs-4">
        <dl>
            <dt>Contract Value</dt>
            <dd class="display-flex">
                <span>{{number_format($period->contract_value, 2)}}</span>

                @if ($period->contract_value > $project->project_contract_signed_value)
                    <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                @else
                    <span class="text-danger"><i class="fa fa-arrow-down"></i></span>
                @endif
            </dd>
        </dl>
    </article>

    <article class="col-xs-4">
        <dl>
            <dt>Duration (Days)</dt>
            <dd>
                <span>{{$period->project_duration}}</span>

                @if ($period->project_duration > $project->project_duration)
                    <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                @else
                    <span class="text-danger"><i class="fa fa-arrow-down"></i></span>
                @endif
            </dd>
        </dl>
    </article>

    <article class="col-xs-4">
        <dl>
            <dt>Planned Finish Date</dt>
            <dd>{{\Carbon\Carbon::parse($period->planned_finish_date)->format('d M Y')}}</dd>
        </dl>
    </article>
</div>