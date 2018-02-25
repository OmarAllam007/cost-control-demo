<h3 class="card-title dark-green">Contract information</h3>
<section class="card-body">
    <div class="row">
        <article class="col-xs-4">
            <dl>
                <dt>Contract Value</dt>
                <dd>{{number_format($project->project_contract_signed_value, 2)}}</dd>
            </dl>
        </article>

        <article class="col-xs-4">
            <dl>
                <dt>Duration (Days)</dt>
                <dd>{{$project->project_duration}}</dd>
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
                <dt>Initial Profit</dt>
                <dd>{{number_format($project->estimated_profit_and_risk, 2)}}</dd>
            </dl>
        </article>

        <article class="col-xs-4">
            <dl>
                <dt>Initial Profitability</dt>
                <dd>{{number_format($project->contract_signed_value, 2)}}</dd>
            </dl>
        </article>

        <article class="col-xs-4">
            <dl>
                <dt>Planned Finish Date</dt>
                <dd>{{\Carbon\Carbon::parse($project->expected_finished_date)->format('d M Y')}}</dd>
            </dl>
        </article>
    </div>
</section>